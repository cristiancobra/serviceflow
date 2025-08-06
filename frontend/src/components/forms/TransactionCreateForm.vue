<template>
  <div>
    <button type="button" class="button button-new d-flex justify-content-center" @click="showModal"
            data-bs-toggle="modal" data-bs-target="#taskModal">
            <font-awesome-icon icon="fa-solid fa-plus" class="" />
        </button>

    <div v-if="isModalVisible" class="myModal">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <font-awesome-icon icon="fa-solid fa-file-invoice" class="icon pe-3 primary" />
            <h5 class="modal-title" id="taskModalLabel">Adicionar recebimento</h5>
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
                    <label for="invoice" class="form-label">Proposta</label>
                    <text-value v-model="localProposal.date" class="selected" />
                  </div>
                </div>
                <div class="col">
                  <div>
                    <label for="installment_quantity" class="form-label">Quantidade de Parcelas</label>
                    <br>
                    {{ invoice.installment_quantity }}
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
// import MoneyEditableField from "../fields/number/MoneyEditableField.vue";
import TextAreaInput from "./inputs/textarea/TextAreaInput.vue";
import TextValue from "../fields/text/TextValue.vue";
import UsersSelectInput from "./selects/UsersSelectInput.vue";

export default {
  name: "TaskCreateForm",
  emits: ["new-task-event"],
  components: {
    // AddMessage,
    DateInput,
    // MoneyEditableField,
    MoneyField,
    TextAreaInput,
    TextValue,
    UsersSelectInput,
  },
  props: {
    invoice: {
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
        invoice_id: this.invoice.id,
        prices: [],
      },
      isActiveCompany: false,
      isActiveLead: false,
      isModalVisible: false,
      leads: [],
      localInvoice: this.invoice,
      message: null,
      messageStatus: "",
      messageText: "",
      newTask: null,
      // selectedProject: inject('currentProject'),
      users: [],
    };
  },
  methods: {
    submitFormCreate,
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
    openModal() {
      this.isModalVisible = true;
    },
    async submitForm() {
      // const totalPrice = this.invoice.total_price;
      // const totalCalculated = this.form.prices.reduce((acc, price) => acc + (isNaN(price) ? 0 : price), 0);

      // if (totalCalculated > totalPrice) {
      //   const difference = totalCalculated - totalPrice;
      //   this.errorMessage = `A soma das parcelas excede o valor total da proposta em ${difference.toFixed(2)}. Ajuste os valores.`;
      //   return;
      // }
console.log("Form:", this.form);
      const { data, error } = await this.submitFormCreate("transactions", this.form);

      if (data) {
        this.messageStatus = "success";
        this.messageText = "Faturas criadas com sucesso!";
        this.isError = false;
        this.closeModal();
        this.clearForm();
        this.$emit("new-transaction-event", data);
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