<template>
  <div>
    <button v-if="installmentStatus === 'notIssued'" type="button" class="button-new" @click="openModal">
      <font-awesome-icon icon="fa-solid fa-plus-circle" class="mr-2" />
      <span class="pt-1">{{ proposal.installment_quantity }}</span>
    </button>
    <div v-else-if="installmentStatus === 'issued'" class="button disabled p-2 flex items-center">
      <font-awesome-icon icon="fa-solid fa-circle-check" class="mr-2" />
      FATURAS GERADAS
    </div>
    <div v-else-if="installmentStatus === 'pending'" class="button delete p-2 flex items-center">
      <font-awesome-icon icon="fa-solid fa-circle-check" class="mr-2" />
      APROVAÇÃO PENDENTE
    </div>

    <div v-if="isModalVisible" class="myModal">
      <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg  mt-25">
          <div class="flex items-center justify-between p-4 border-b">
            <div class="flex items-center">
              <font-awesome-icon icon="fa-solid fa-file-invoice" class="icon pr-3 primary" />
              <h5 class="text-lg font-semibold text-black" id="taskModalLabel">Nova fatura</h5>
            </div>
            <button type="button" class="text-gray-400 hover:text-gray-600" @click="closeModal" aria-label="Close">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div class="p-6 space-y-6">
            <form @submit.prevent="submitForm" class="space-y-6">
              <div class="bg-gray-50 rounded-lg p-4">
                <TextAreaInput 
                  label="Observações:" 
                  name="observations" 
                  v-model="form.observations"
                  placeholder="Detalhamento da tarefa" 
                  :rows="4" 
                />
              </div>

              <div class="flex flex-col lg:flex-row gap-6">
                <div class="flex-1 space-y-2">
                  <label for="proposal" class="block text-sm font-semibold text-gray-700">Proposta</label>
                  <text-value v-model="localProposal.date" class="selected" />
                </div>
                <div class="flex-1 space-y-2">
                  <label for="installment_quantity" class="block text-sm font-semibold text-gray-700">Quantidade de Parcelas</label>
                  <div class="px-3 py-2 bg-gray-100 rounded-lg">
                    <span class="text-lg font-bold text-gray-900">{{ proposal.installment_quantity }}</span>
                  </div>
                </div>
              </div>

              <div class="flex flex-col lg:flex-row gap-6">
                <div class="flex-1">
                  <UsersSelectInput label="Responsável" v-model="form.user_id" fieldsToDisplay="name" autoSelect=true />
                </div>
                <div class="flex-1">
                  <date-input 
                    v-model="form.date_due" 
                    label="Data de vencimento" 
                    name="date_due" 
                    :autoFillNow="true"
                    placeholder="data quando a fatura vence" 
                    @update="updateForm" 
                  />
                </div>
              </div>

              <div class="border-t border-gray-200 pt-6">
                <div class="flex items-center space-x-2 mb-4">
                  <div class="w-2 h-8 bg-blue-500 rounded-full"></div>
                  <h4 class="text-lg font-bold text-gray-900 uppercase tracking-wide">
                    Parcelamento
                  </h4>
                </div>

                <div class="space-y-4">
                  <div 
                    v-for="index in proposal.installment_quantity" 
                    :key="index"
                    class="flex flex-col md:flex-row gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200"
                  >
                    <div class="flex items-center flex-1">
                      <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 text-sm font-bold rounded-full mr-3">
                        {{ index }}
                      </span>
                      <label :for="'price-' + index" class="text-sm font-semibold text-gray-700">
                        Valor da Parcela {{ index }}
                      </label>
                    </div>
                    <div class="flex-1 md:max-w-xs">
                      <money-editable-field 
                        :name="'price-' + index" 
                        v-model="form.prices[index - 1]"
                        @save="adjustPrices(index - 1, $event)" 
                      />
                    </div>
                  </div>
                </div>

                <div class="mt-6 p-4 bg-blue-50 rounded-lg border-2 border-blue-200">
                  <div class="flex flex-col md:flex-row gap-4 items-center">
                    <div class="flex-1">
                      <label for="total" class="text-sm font-semibold text-blue-700">Total Geral</label>
                    </div>
                    <div class="flex-1 md:max-w-xs">
                      <money-field v-model="totalPrices" class="selected font-bold text-lg" readonly />
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="errorMessage" class="mt-8">
                <div>
                  <p class="error text-black">
                    {{ errorMessage }}
                  </p>
                </div>
              </div>

              <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600" @click="closeModal">Fechar</button>
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
      installmentStatus: false,
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
    'proposal.status': function() {
      this.checkInvoices();
    },
    proposal: {
      immediate: true,
      handler(newProposal) {
        this.form.proposal_id = newProposal.id;
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
      if(this.proposal.status !== "accepted") {
        this.installmentStatus = "pending";
      }
      else if (this.proposal.invoices.length > 0) {
        this.installmentStatus = 'issued';
      }
      else {
        this.installmentStatus = "notIssued";
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
        return;
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
  },
};
</script>

<style scoped>
.button.disabled {
  font-size: 1rem;
  text-align: center;
  background-color: gray;
  color: white;
  border-color: gray;
  cursor: not-allowed;
}
</style>