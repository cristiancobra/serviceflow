<template>
  <div>
    <button v-if="installmentStatus === 'notIssued'" type="button" class="btn btn-primary btn-sm" @click="openModal">
      <font-awesome-icon icon="fa-solid fa-plus" />
      Gerar {{ proposal.installment_quantity }} {{ proposal.installment_quantity == 1 ? 'fatura' : 'faturas' }}
    </button>
    <span v-else-if="installmentStatus === 'issued'" class="badge badge-success badge-soft gap-2 font-semibold">
      <font-awesome-icon icon="fa-solid fa-circle-check" />
      Faturas geradas
    </span>
    <span
      v-else-if="installmentStatus === 'pending'"
      class="badge badge-warning badge-soft gap-2 font-semibold"
      title="As faturas só podem ser geradas depois que a proposta for aceita"
    >
      <font-awesome-icon icon="fa-solid fa-clock" />
      Aguardando aceite
    </span>

    <div v-if="isModalVisible" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm">
      <ModalCard
        title="Nova Fatura"
        icon="fa-solid fa-file-invoice"
        size="xl"
        @close="closeModal"
      >
            <form id="creditInvoiceCreateForm" @submit.prevent="submitForm" class="space-y-6">
              <div class="bg-base-200 rounded-lg p-4">
                <TextAreaInput 
                  label="Observações" 
                  name="observations" 
                  v-model="form.observations"
                  placeholder="Detalhamento da tarefa" 
                  :rows="4" 
                />
              </div>

              <div class="flex flex-col lg:flex-row gap-6">
                <div class="flex-1 space-y-2">
                  <label for="proposal" class="block text-sm font-semibold text-base-content">Proposta</label>
                  <text-value v-model="localProposal.date" class="selected" />
                </div>
                <div class="flex-1 space-y-2">
                  <label for="installment_quantity" class="block text-sm font-semibold text-base-content">Quantidade de Parcelas</label>
                  <div class="px-3 py-2 bg-base-300 rounded-lg">
                    <span class="text-lg font-bold text-base-content">{{ proposal.installment_quantity }}</span>
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

              <div class="border-t border-base-300 pt-6">
                <div class="flex items-center space-x-2 mb-4">
                  <div class="w-2 h-8 bg-info rounded-full"></div>
                  <h4 class="text-lg font-bold text-base-content uppercase tracking-wide">
                    Parcelamento
                  </h4>
                </div>

                <div class="space-y-4">
                  <div 
                    v-for="index in proposal.installment_quantity" 
                    :key="index"
                    class="flex flex-col md:flex-row gap-4 p-4 bg-base-200 rounded-lg border border-base-300"
                  >
                    <div class="flex items-center flex-1">
                      <span class="inline-flex items-center justify-center w-8 h-8 bg-info/10 text-info text-sm font-bold rounded-full mr-3">
                        {{ index }}
                      </span>
                      <label :for="'price-' + index" class="text-sm font-semibold text-base-content">
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

                <div class="mt-6 p-4 bg-info/10 rounded-lg border-2 border-info/30">
                  <div class="flex flex-col md:flex-row gap-4 items-center">
                    <div class="flex-1">
                      <label for="total" class="text-sm font-semibold text-info">Total Geral</label>
                    </div>
                    <div class="flex-1 md:max-w-xs">
                      <money-field v-model="totalPrices" class="selected font-bold text-lg" readonly />
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-base-200 rounded-lg p-4">
                <label class="flex items-center gap-3 cursor-pointer">
                  <input v-model="form.generate_task" type="checkbox" class="checkbox checkbox-sm checkbox-success" />
                  <div>
                    <span class="text-sm font-semibold text-base-content">Gerar tarefas de cobrança</span>
                    <p class="text-xs text-base-content/60">
                      Cria uma tarefa "Receber: ..." por parcela, com o vencimento da parcela
                    </p>
                  </div>
                </label>

                <div v-if="form.generate_task" class="mt-3">
                  <DepartmentsSelectInput
                    label="Departamento Responsável"
                    name="task_department_id"
                    v-model="form.task_department_id"
                    fieldNull="Selecione o departamento"
                  />
                </div>
              </div>

              <div v-if="errorMessage" class="mt-8">
                <div>
                  <p class="error text-base-content">
                    {{ errorMessage }}
                  </p>
                </div>
              </div>
            </form>

        <template #footer>
          <button
            type="button"
            class="px-6 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg font-semibold hover:bg-base-200 transition-colors"
            @click="closeModal"
          >
            Cancelar
          </button>
          <button
            type="submit"
            form="creditInvoiceCreateForm"
            class="px-6 py-2 bg-primary hover:opacity-90 text-white rounded-lg font-semibold transition-colors flex items-center gap-2"
          >
            <font-awesome-icon icon="fa-solid fa-plus" />
            Criar Fatura
          </button>
        </template>
      </ModalCard>
    </div>
  </div>
</template>

<script>
import { index, submitFormCreate } from "@/utils/requests/httpUtils";
// import AddMessage from "@/components/forms/messages/AddMessage.vue";
import DateInput from "./inputs/date/DateInput.vue";
import DepartmentsSelectInput from "./selects/DepartmentsSelectInput.vue";
import MoneyField from "../fields/number/MoneyField.vue";
import MoneyEditableField from "../fields/number/MoneyEditableField.vue";
import TextAreaInput from "./inputs/textarea/TextAreaInput.vue";
import TextValue from "../fields/text/TextValue.vue";
import UsersSelectInput from "./selects/UsersSelectInput.vue";
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
  name: "CreditInvoiceCreateForm",
  emits: ["new-task-event"],
  components: {
    // AddMessage,
    DateInput,
    DepartmentsSelectInput,
    MoneyEditableField,
    MoneyField,
    TextAreaInput,
    TextValue,
    UsersSelectInput,
    ModalCard,
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
        generate_task: true,
        task_department_id: null,
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
    async loadFinanceiroDepartment() {
      try {
        const departments = (await index("departments")) || [];
        const financeiro = departments.find(d => d.slug === 'financeiro' || d.name?.toLowerCase().includes('financeiro'));
        if (financeiro) {
          this.form.task_department_id = financeiro.id;
        }
      } catch (e) {
        // usuário escolhe o departamento manualmente
      }
    },
    openModal() {
      this.isModalVisible = true;
      if (!this.form.task_department_id) {
        this.loadFinanceiroDepartment();
      }
    },
    async submitForm() {
      const totalPrice = this.proposal.total_price;
      const totalCalculated = this.form.prices.reduce((acc, price) => acc + (isNaN(price) ? 0 : price), 0);

      if (totalCalculated > totalPrice) {
        const difference = totalCalculated - totalPrice;
        this.errorMessage = `A soma das parcelas excede o valor total da proposta em ${difference.toFixed(2)}. Ajuste os valores.`;
        return;
      }

      const payload = {
        ...this.form,
        generate_task: this.form.generate_task ? 1 : 0,
        task_department_id: this.form.generate_task ? this.form.task_department_id : null,
      };
      const { data, error } = await this.submitFormCreate("invoices", payload);

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
  border-color: color-mix(in oklab, var(--color-base-content) 50%, transparent);
  cursor: not-allowed;
}
</style>