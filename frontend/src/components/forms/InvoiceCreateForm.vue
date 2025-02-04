<template>
  <div>
    <button v-if="!isSubmitDisabled" type="button" class="button" @click=openModal>
      <font-awesome-icon icon="fa-solid fa-file-invoice" class="me-2" />
      GERAR {{ proposal.installment_quantity }} FATURAS
    </button>
    <div v-else class="button disabled">
      <font-awesome-icon icon="fa-solid fa-circle-check" class="me-0" />
      FATURAS GERADAS
    </div>

    <div v-if="isModalVisible" class="myModal">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <font-awesome-icon icon="fa-solid fa-file-invoice" class="icon pe-3 primary" />
            <h5 class="modal-title" id="taskModalLabel">Nova fatura</h5>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm">

              <div class="row mt-4">
                <TextAreaInput label="Observações:" name="observations" v-model="form.observations"
                  placeholder="Detalhamento da tarefa" :rows="5" />
              </div>

              <div class="row mb-4 mt-4">
                <div class="col">
                  <div>
                    <label for="proposal" class="form-label">Proposta</label>
                    <text-value v-model="localProposal.date" class="selected" />
                  </div>
                </div>
                <div class="col">
                  <div>
                    <label for="installment_quantity" class="form-label">Quantidade de Parcelas</label>
                    <br>
                    {{ proposal.installment_quantity }}
                  </div>
                </div>
              </div>

              <div class="row mb-4 mt-4">
                <div class="col">
                  <UsersSelectInput label="Responsável" v-model="form.user_id" fieldsToDisplay="name" autoSelect=true />
                </div>
                <div class="col">
                  <date-input v-model="form.date_due" label="Data de vencimento" name="date_due" :autoFillNow="true"
                    placeholder="data quando a fatura vence" @update="updateForm" />
                </div>
              </div>

              <div class="row mt-5">
                <div class="col">
                  <p class="title">
                    PARCELAMENTO
                  </p>
                </div>
              </div>

              <div class="row mb-2 mt-2" v-for="index in proposal.installment_quantity" :key="index">
                <div class="col-3">
                  <label :for="'price-' + index" class="form-label">Valor da Parcela {{ index }}</label>
                </div>
                <div class="col-3">
                  <money-editable-field :name="'price-' + index" v-model="form.prices[index - 1]"
                    @update="adjustPrices(index - 1, $event)" />
                </div>
              </div>

              <div class="row mb-2 mt-2">
                <div class="col-3">
                  <label for="total" class="form-label">Total</label>
                </div>
                <div class="col-3">
                  <money-field v-model="totalPrices" class="selected" readonly />
                </div>
              </div>

              <div v-if="errorMessage" class="row mt-5">
                <div class="col">
                  <p class="error">
                    {{ errorMessage }}
                  </p>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click=closeModal>Fechar</button>
                <button type="submit" class="button-new">criar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { submitFormCreate } from "@/utils/requests/httpUtils";
// import AddMessage from "@/components/forms/messages/AddMessage.vue";
import DateInput from "./inputs/date/DateInput.vue";
import MoneyField from "../fields/number/MoneyField.vue";
import MoneyEditableField from "../fields/number/MoneyEditableField.vue";
import TextAreaInput from "./inputs/textarea/TextAreaInput.vue";
import TextValue from "../fields/text/TextValue.vue";
import UsersSelectInput from "./selects/UsersSelectInput.vue";

export default {
  name: "TaskCreateForm",
  emits: ["new-task-event"],
  components: {
    // AddMessage,
    DateInput,
    MoneyEditableField,
    MoneyField,
    TextAreaInput,
    TextValue,
    UsersSelectInput,
  },
  props: {
    proposal: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      allStatus: [],
      companies: [],
      data: [],
      form: {
        date_due: null,
        date_start: null,
        proposal_id: this.proposal.id,
        prices: [],
      },
      isActiveCompany: false,
      isActiveLead: false,
      isModalVisible: false,
      isSubmitDisabled: false,
      leads: [],
      localProposal: this.proposal,
      message: null,
      messageStatus: "",
      messageText: "",
      newTask: null,
      // selectedProject: inject('currentProject'),
      users: [],
    };
  },
  watch: {
    proposal: {
      immediate: true,
      handler(newProposal) {
        if (newProposal) {
          this.form.prices = this.initializePrices();
        }
      }
    }
  },
  computed: {
    totalPrices() {
      return this.form.prices.reduce((acc, price) => acc + price, 0).toFixed(2);
    }
  },
  methods: {
    submitFormCreate,
    addLeadCreated(newLead) {
      this.leads.push(newLead.lead);
      !this.toggleLead();
      this.form.contact_id = newLead.lead.id;
    },
    adjustPrices(changedIndex, newValue) {
      const totalPrice = this.proposal.total_price;
      const prices = [...this.form.prices]; // Cria uma cópia do array de preços

      console.log('Antes da alteração:', prices);

      // Atualiza o valor alterado com o novo valor
      let newPrice = parseFloat(newValue);
      const sumBefore = prices.slice(0, changedIndex).reduce((acc, price) => acc + price, 0);
      const remaining = totalPrice - sumBefore;

      if (newPrice > remaining) {
        console.log('Valor excedente:', newPrice - remaining);
        newPrice = remaining;
        console.log('O valor da parcela não pode exceder o valor total restante.');
      }

      prices[changedIndex] = newPrice;

      const remainingAfterChange = totalPrice - prices.slice(0, changedIndex + 1).reduce((acc, price) => acc + price, 0);
      const remainingInstallments = prices.length - (changedIndex + 1);
      const newPricePerInstallment = (remainingAfterChange / remainingInstallments).toFixed(2);

      for (let i = changedIndex + 1; i < prices.length; i++) {
        prices[i] = parseFloat(newPricePerInstallment);
      }

      // Ajustar a última parcela para compensar a diferença
      const totalCalculated = prices.reduce((acc, price) => acc + price, 0);
      const difference = totalPrice - totalCalculated;
      prices[prices.length - 1] += difference;


      this.form.prices = prices; // Atualiza o array de preços
    },
    checkInvoices() {
      console.log("invoices", this.proposal.invoices);
      console.log("length", this.proposal.invoices.length);
      if (this.proposal.invoices.length > 0) {
        this.isSubmitDisabled = true;
      }
    },
    clearForm() {
      this.form.name = "";
      this.form.observations = "";
      this.form.company_id = null;
      this.form.contact_id = null;
      this.form.date_start = null;
      this.form.date_due = "";
      this.form.date_conclusion = "";
    },
    closeModal() {
      this.isModalVisible = false;
    },
    initializePrices() {
      const installmentQuantity = this.proposal.installment_quantity;
      const totalPrice = this.proposal.total_price;
      const pricePerInstallment = (totalPrice / installmentQuantity).toFixed(2);
      const prices = Array(installmentQuantity).fill(parseFloat(pricePerInstallment));

      // Ajustar a última parcela para compensar a diferença
      const totalCalculated = prices.reduce((acc, price) => acc + price, 0);
      const difference = totalPrice - totalCalculated;
      prices[installmentQuantity - 1] += difference;

      return prices;
    },
    openModal() {
      this.isModalVisible = true;
    },
    async submitForm() {
      const totalPrice = this.proposal.total_price;
      const totalCalculated = this.form.prices.reduce((acc, price) => acc + (isNaN(price) ? 0 : price), 0);

      if (totalCalculated > totalPrice) {
        const difference = totalCalculated - totalPrice;
        this.errorMessage = `A soma das parcelas excede o valor total da proposta em ${difference.toFixed(2)}. Ajuste os valores.`;
        return; // Impede o envio do formulário
      }

      const { data, error } = await this.submitFormCreate("invoices", this.form);

      if (data) {
        this.messageStatus = "success";
        this.messageText = "Faturas criadas com sucesso!";
        this.isError = false;
        this.closeModal();
        this.clearForm();
        this.$emit("new-invoice-event", data);
      }
      if (error) {
        this.errorMessage = "Erro ao criar faturas. Tente novamente.";
        this.errors = error;
      }
    },
    updateForm(field, value) {
      this.form[field] = value;
    },
  },
  mounted() {
    this.checkInvoices();
    console.log("isSubmitDisabled", this.isSubmitDisabled);
  },
};
</script>

<style scoped>
.button.disabled {
  font-size: 1rem;
  text-align: center;
  display: flex;
  background-color: gray;
  color: white;
  border-color: gray;
  cursor: not-allowed;
}
</style>