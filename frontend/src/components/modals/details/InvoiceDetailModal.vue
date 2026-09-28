<template>
  <ModalCard
    title="Fatura"
    :subtitle="invoice?.name || (invoice ? `Fatura #${invoice.id}` : '')"
    icon="fa-solid fa-file-invoice-dollar"
    size="lg"
    :compact="compact"
    compact-size="max-w-3xl"
    @close="$emit('close')"
  >
    <div v-if="!invoice" class="p-5 text-center text-gray-500">
      Carregando fatura...
    </div>

    <template v-else>
      <!-- Mensagem de erro -->
      <div v-if="errorMessage" class="bg-red-50 border-2 border-red-500 rounded-lg p-4 mb-6">
        <div class="flex gap-4">
          <div class="flex-shrink-0 pt-0.5">
            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="text-2xl text-red-600" />
          </div>
          <div class="flex-1">
            <h3 class="text-red-800 font-bold text-lg mb-2">Erro ao atualizar fatura</h3>
            <p class="text-red-700 text-sm">{{ errorMessage }}</p>
          </div>
          <button @click="errorMessage = null" class="text-red-500 hover:text-red-700">
            <font-awesome-icon icon="fa-solid fa-times" />
          </button>
        </div>
      </div>

      <!-- Status + Valor -->
      <div class="flex flex-wrap items-center justify-between gap-6 mb-6">
        <div>
          <p v-if="invoice.category || isDebit" class="text-sm text-gray-500 mb-2">
            <span
              v-if="invoice.category"
              class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 mr-2"
            >
              {{ getCategoryLabel(invoice.category) }}
            </span>
            {{ isDebit ? "Conta a pagar" : "Conta a receber" }}
          </p>
          <select-status-button
            :status="invoice.status"
            @update:modelValue="updateInvoice('status', $event)"
          />
        </div>

        <div class="relative rounded-2xl border-2 border-primary bg-primary-50/60 p-4 shadow-sm">
          <div
            class="absolute -top-3 left-4 inline-flex items-center gap-2 rounded-md bg-primary px-2 py-0.5 text-xs font-semibold text-white shadow"
          >
            <span>VALOR DA FATURA</span>
          </div>
          <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <div class="grid h-10 w-10 place-items-center rounded-lg bg-primary text-white shadow">
                <font-awesome-icon icon="fas fa-dollar-sign" />
              </div>
              <span class="text-sm font-medium text-primary">Preço</span>
            </div>
            <div class="min-w-[180px] text-right text-primary">
              <div
                class="inline-flex items-center rounded-lg bg-white px-3 py-2 ring-1 ring-emerald-200 shadow-sm ml-auto"
              >
                <money-editable-field
                  name="price"
                  v-model="invoice.price"
                  @update:modelValue="updateInvoice('price', $event)"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empresa / Cliente / Oportunidade / Proposta -->
      <div class="rounded-lg border border-gray-200 p-6 mb-6">
        <div class="space-y-4">
          <div class="flex items-center gap-3">
            <company-avatar
              :photo="invoice.company?.photo"
              :business-name="invoice.company?.business_name"
              :legal-name="invoice.company?.legal_name"
              :company-id="invoice.company?.id"
              size="md"
            />
            <companies-select-editable-field
              label="Empresa"
              name="company_id"
              :modelValue="invoice.company_id"
              @update:modelValue="updateInvoice('company_id', $event)"
              class="flex-1"
            />
          </div>

          <div class="flex items-center gap-3">
            <lead-avatar
              :photo="invoice.lead?.photo"
              :name="invoice.lead?.name"
              :lead-id="invoice.lead?.id"
              size="md"
            />
            <leads-select-editable-field
              label="Cliente"
              name="lead_id"
              :modelValue="invoice.lead_id"
              @update:modelValue="updateInvoice('lead_id', $event)"
              class="flex-1"
            />
          </div>

          <div v-if="invoice.proposal?.opportunity" class="flex items-center gap-2 text-sm">
            <font-awesome-icon icon="fa-solid fa-bullseye" class="text-primary" />
            <router-link
              :to="{ name: 'opportunityShow', params: { id: invoice.proposal.opportunity.id } }"
              class="text-primary hover:underline font-medium"
              @click="$emit('close')"
            >
              {{ invoice.proposal.opportunity.name }}
            </router-link>
          </div>

          <div v-if="invoice.proposal" class="flex items-center gap-2 text-sm">
            <font-awesome-icon icon="fa-solid fa-file-contract" class="text-primary" />
            <router-link
              :to="{ name: 'proposalShow', params: { id: invoice.proposal.id } }"
              class="text-primary hover:underline font-medium"
              @click="$emit('close')"
            >
              Proposta {{ invoice.proposal.id }} - {{ invoice.proposal.description }}
            </router-link>
          </div>
        </div>
      </div>

      <!-- Tarefas vinculadas -->
      <div
        v-if="invoice.tasks && invoice.tasks.length > 0"
        class="rounded-lg border border-green-200 bg-green-50 p-6 mb-6"
      >
        <h3 class="text-lg font-bold text-green-800 mb-3 flex items-center gap-2">
          <font-awesome-icon icon="fa-solid fa-check-circle" class="text-green-600" />
          Tarefas Vinculadas
        </h3>
        <div class="space-y-2">
          <button
            v-for="task in invoice.tasks"
            :key="task.id"
            type="button"
            class="w-full flex items-center justify-between p-3 bg-white rounded-lg border border-green-200 hover:border-green-400 transition-colors text-left"
            @click="openTaskModal(task.id)"
          >
            <div class="flex items-center gap-3">
              <span
                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                :class="task.status === 'done' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
              >
                {{ task.status === "done" ? "Concluída" : "Pendente" }}
              </span>
              <span class="text-sm font-medium text-gray-800">{{ task.name }}</span>
            </div>
            <span v-if="task.date_due" class="text-xs text-gray-500">{{ formatDateBr(task.date_due) }}</span>
          </button>
        </div>
      </div>

      <!-- Datas -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="flex items-center gap-2 mb-1">
            <font-awesome-icon icon="fa fa-calendar-alt" class="text-error" />
            <label class="text-sm font-semibold text-gray-700">Vencimento</label>
          </div>
          <p class="text-gray-800">{{ formatDateBr(invoice.date_due) }}</p>
        </div>
        <div v-if="invoice.proposal" class="bg-gray-50 rounded-lg p-4">
          <div class="flex items-center gap-2 mb-1">
            <font-awesome-icon icon="fas fa-credit-card" class="text-primary" />
            <label class="text-sm font-semibold text-gray-700">Parcelamento</label>
          </div>
          <p class="text-gray-800">{{ invoice.proposal.installment_quantity }}x</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="flex items-center gap-2 mb-1">
            <font-awesome-icon icon="fa fa-calendar-plus" class="text-gray-500" />
            <label class="text-sm font-semibold text-gray-700">Criação</label>
          </div>
          <p class="text-gray-800">{{ formatDateBr(invoice.created_at) }}</p>
        </div>
      </div>

      <!-- Descrição -->
      <div class="mb-6 bg-gray-50 rounded-lg p-4">
        <text-area-editable-input
          name="description"
          label="Descrição"
          v-model="invoice.description"
          placeholder="Adicione uma descrição"
          @save="updateInvoice('description', $event)"
        />
      </div>

      <!-- Pagamentos -->
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
          <font-awesome-icon icon="fas fa-coins" class="text-primary" />
          {{ isDebit ? "Pagamentos realizados" : "Pagamentos recebidos" }}
        </h3>
        <button type="button" class="btn btn-primary btn-sm" title="Adicionar pagamento" @click="openTransactionModal">
          <font-awesome-icon icon="fa-solid fa-plus" class="text-white" />
        </button>
      </div>

      <transactions-list-section
        :transactions="invoice.transactions"
        :is-debit="isDebit"
        @update-transaction="
          (fieldName, transactionId, editedValue) =>
            updateTransaction(fieldName, editedValue, transactionId)
        "
        @delete-transaction="deleteTransaction"
      />

      <!-- Totais -->
      <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
          <div class="text-xs font-semibold text-gray-500">Total da Fatura</div>
          <div class="mt-1 font-bold text-gray-800">
            <money-field name="total" :modelValue="invoiceTotal" readonly />
          </div>
        </div>
        <div
          class="rounded-xl border p-4 shadow-sm"
          :class="isDebit ? 'border-red-200 bg-red-50' : 'border-emerald-200 bg-emerald-50'"
        >
          <div class="text-xs font-semibold" :class="isDebit ? 'text-red-700' : 'text-emerald-700'">
            {{ isDebit ? "Total Pago" : "Total Recebido" }}
          </div>
          <div class="mt-1 font-bold" :class="isDebit ? 'text-red-800' : 'text-emerald-800'">
            <money-field name="paid" :modelValue="transactionsTotal" readonly />
          </div>
        </div>
        <div class="rounded-xl border border-sky-200 bg-sky-50 p-4 shadow-sm">
          <div class="text-xs font-semibold text-sky-700">Saldo</div>
          <div class="mt-1 font-bold" :class="balance >= 0 ? 'text-sky-800' : 'text-red-700'">
            <money-field name="balance" :modelValue="balance" readonly />
          </div>
        </div>
      </div>
    </template>

    <template v-if="invoice" #footer>
      <div class="flex w-full items-center justify-between gap-2">
        <button type="button" class="btn btn-error" title="Excluir fatura" @click="deleteInvoice">
          <font-awesome-icon icon="fa-solid fa-trash" :class="{ 'me-2': !compact }" />
          <span v-if="!compact">Excluir</span>
        </button>

        <div class="flex items-center gap-4">
          <label class="flex items-center gap-2 cursor-pointer" title="Mostrar quantidades no PDF">
            <input type="checkbox" class="toggle toggle-sm toggle-primary" v-model="isVisibleQuantity" />
            <span v-if="!compact" class="text-sm text-gray-700 font-medium">quantidades</span>
          </label>

          <button type="button" class="btn btn-primary" title="Gerar PDF" @click="exportPDF">
            <font-awesome-icon icon="fa-solid fa-file-invoice" :class="{ 'me-2': !compact }" />
            <span v-if="!compact">Gerar PDF</span>
          </button>
        </div>
      </div>
    </template>
  </ModalCard>
</template>

<script>
import { mapMutations } from "vuex";
import { BACKEND_URL } from "@/config/apiConfig";
import { destroy, show, updateField } from "@/utils/requests/httpUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import ModalCard from "@/components/modals/ModalCard.vue";
import MoneyField from "@/components/fields/number/MoneyField.vue";
import MoneyEditableField from "@/components/fields/number/MoneyEditableField.vue";
import SelectStatusButton from "@/components/buttons/SelectStatusButton.vue";
import TextAreaEditableInput from "@/components/forms/inputs/textarea/TextAreaEditableInput.vue";
import CompanyAvatar from "@/components/common/CompanyAvatar.vue";
import CompaniesSelectEditableField from "@/components/fields/selects/CompaniesSelectEditableField.vue";
import LeadAvatar from "@/components/common/LeadAvatar.vue";
import LeadsSelectEditableField from "@/components/fields/selects/LeadsSelectEditableField.vue";
import TransactionsListSection from "@/components/show/TransactionsListSection.vue";

const CATEGORY_LABELS = {
  fixed_cost: "Custo Fixo",
  recurring: "Recorrente",
  supplier: "Fornecedor",
  operational: "Operacional",
  other: "Outro",
};

export default {
  name: "InvoiceDetailModal",
  components: {
    ModalCard,
    MoneyField,
    MoneyEditableField,
    SelectStatusButton,
    TextAreaEditableInput,
    CompanyAvatar,
    CompaniesSelectEditableField,
    LeadAvatar,
    LeadsSelectEditableField,
    TransactionsListSection,
  },
  props: {
    invoiceId: {
      type: [Number, String],
      required: true,
    },
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
  },
  // invoice-updated: qualquer alteração na fatura ou nos pagamentos (payload: fatura recarregada)
  // invoice-deleted: fatura excluída (payload: id)
  emits: ["close", "invoice-updated", "invoice-deleted"],
  data() {
    return {
      invoice: null,
      isVisibleQuantity: false,
      errorMessage: null,
    };
  },
  computed: {
    invoiceTotal() {
      const v = Number(this.invoice?.price ?? 0);
      return isNaN(v) ? 0 : v;
    },
    transactionsTotal() {
      const list = this.invoice?.transactions ?? [];
      const sum = list.reduce((acc, t) => acc + Number(t?.amount ?? 0), 0);
      return isNaN(sum) ? 0 : sum;
    },
    balance() {
      return this.invoiceTotal - this.transactionsTotal;
    },
    isDebit() {
      return this.invoice?.type === "debit";
    },
  },
  methods: {
    ...mapMutations(["openModal"]),
    formatDateBr,
    getCategoryLabel(category) {
      return CATEGORY_LABELS[category] || category;
    },
    async getInvoice() {
      this.invoice = await show("invoices", this.invoiceId);
    },
    // Recarrega a fatura (status/saldo são calculados no backend) e avisa quem abriu o modal
    async refreshInvoice() {
      await this.getInvoice();
      this.$emit("invoice-updated", this.invoice);
    },
    async updateInvoice(fieldName, editedValue) {
      try {
        this.errorMessage = null;
        await updateField("invoices", this.invoiceId, fieldName, editedValue);
      } catch (error) {
        if (error.response?.status === 422) {
          const errors = error.response.data.errors;
          this.errorMessage = errors?.price?.[0] || error.response.data.message || "Erro ao atualizar fatura";
        } else {
          this.errorMessage = "Erro ao atualizar fatura. Tente novamente.";
        }
        console.error("Erro ao atualizar fatura:", error);
      }
      // Recarrega sempre: traz as relações atualizadas ou restaura o valor anterior em caso de erro
      await this.refreshInvoice();
    },
    async deleteInvoice() {
      if (!confirm("Tem certeza que deseja excluir esta fatura? Esta ação não pode ser desfeita.")) {
        return;
      }
      try {
        this.errorMessage = null;
        await destroy("invoices", this.invoiceId);
        this.$emit("invoice-deleted", this.invoice.id);
        this.$emit("close");
      } catch (error) {
        if (error.response?.status === 422) {
          this.errorMessage = error.response.data.message || "Não foi possível excluir a fatura.";
        } else {
          this.errorMessage = "Erro ao excluir fatura. Tente novamente.";
        }
        console.error("Erro ao excluir fatura:", error);
      }
    },
    openTransactionModal() {
      this.openModal({
        component: "TransactionCreateForm",
        props: { invoice: this.invoice },
        listeners: {
          "new-transaction-event": this.refreshInvoice,
        },
      });
    },
    openTaskModal(taskId) {
      this.openModal({ component: "TaskDetailModal", props: { taskId }, id: `task-${taskId}` });
    },
    async updateTransaction(fieldName, editedValue, transactionId) {
      try {
        await updateField("transactions", transactionId, fieldName, editedValue);
      } catch (error) {
        this.errorMessage = "Erro ao atualizar pagamento. Tente novamente.";
        console.error("Erro ao atualizar pagamento:", error);
      }
      await this.refreshInvoice();
    },
    async deleteTransaction(transactionId) {
      try {
        this.errorMessage = null;
        await destroy("transactions", transactionId);
      } catch (error) {
        this.errorMessage = "Erro ao excluir pagamento. Tente novamente.";
        console.error("Erro ao excluir pagamento:", error);
      }
      await this.refreshInvoice();
    },
    exportPDF() {
      const url = `${BACKEND_URL}invoices/${this.invoice.id}/pdf?isVisibleQuantity=${this.isVisibleQuantity}`;
      window.open(url, "_blank");
    },
  },
  watch: {
    invoiceId() {
      this.getInvoice();
    },
  },
  mounted() {
    this.getInvoice();
  },
};
</script>
