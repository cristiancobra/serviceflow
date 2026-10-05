<template>
  <ModalCard
    title="Fatura"
    :subtitle="invoice?.name || ''"
    :subtitle-editable="!!invoice"
    :subtitle-placeholder="invoice ? `Fatura #${invoice.id}` : ''"
    icon="fa-solid fa-file-invoice-dollar"
    size="lg"
    :compact="compact"
    compact-size="max-w-3xl"
    @close="$emit('close')"
    @save-subtitle="updateInvoice('name', $event)"
  >
    <div v-if="!invoice" class="p-5 text-center text-base-content/60">
      Carregando fatura...
    </div>

    <template v-else>
      <!-- Mensagem de erro -->
      <div v-if="errorMessage" class="bg-error/10 border-2 border-error rounded-lg p-4 mb-6">
        <div class="flex gap-4">
          <div class="flex-shrink-0 pt-0.5">
            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="text-2xl text-error" />
          </div>
          <div class="flex-1">
            <h3 class="text-error font-bold text-lg mb-2">Erro ao atualizar fatura</h3>
            <p class="text-error text-sm">{{ errorMessage }}</p>
          </div>
          <button @click="errorMessage = null" class="text-error hover:text-error">
            <font-awesome-icon icon="fa-solid fa-times" />
          </button>
        </div>
      </div>

      <!-- Status + Valor -->
      <div class="flex flex-wrap items-center justify-between gap-6 mb-6">
        <div>
          <p v-if="invoice.category || isDebit" class="text-sm text-base-content/60 mb-2">
            <span
              v-if="invoice.category"
              class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-base-200 text-base-content/80 mr-2"
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
                class="inline-flex items-center rounded-lg bg-base-100 px-3 py-2 ring-1 ring-success/30 shadow-sm ml-auto"
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
      <div class="rounded-lg border border-base-300 p-6 mb-6">
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
            <button type="button" class="text-primary hover:underline font-medium" @click="openOpportunityModal">
              {{ invoice.proposal.opportunity.name }}
            </button>
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
        class="rounded-lg border p-6 mb-6"
        :class="invoice.tasks?.length ? 'border-success/30 bg-success/10' : 'border-base-300 bg-base-200'"
      >
        <div class="flex items-center justify-between gap-2 mb-3">
          <h3
            class="text-lg font-bold flex items-center gap-2"
            :class="invoice.tasks?.length ? 'text-success' : 'text-base-content'"
          >
            <font-awesome-icon :icon="invoice.tasks?.length ? 'fa-solid fa-check-circle' : 'fa-solid fa-tasks'" />
            Tarefas Vinculadas
          </h3>
          <div class="flex gap-2">
            <button type="button" class="btn btn-success btn-sm" :disabled="isStartingTask" @click="startTask">
              <span v-if="isStartingTask" class="loading loading-spinner loading-xs"></span>
              <font-awesome-icon v-else icon="fa-solid fa-play" />
              Iniciar tarefa
            </button>
            <button type="button" class="btn btn-primary btn-sm" @click="openTaskCreateModal">
              <font-awesome-icon icon="fa-solid fa-plus" />
              Gerar tarefa
            </button>
          </div>
        </div>
        <p v-if="!invoice.tasks?.length" class="text-sm text-base-content/60">
          Nenhuma tarefa ligada a esta fatura.
        </p>
        <div v-else class="space-y-2">
          <div
            v-for="task in invoice.tasks"
            :key="task.id"
            class="w-full flex items-center justify-between p-3 bg-base-100 rounded-lg border border-success/30 hover:border-success transition-colors cursor-pointer"
            @click="openTaskModal(task.id)"
          >
            <div class="flex items-center gap-3">
              <div class="flex flex-col items-center gap-1">
                <span
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                  :class="task.status === 'done' ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning'"
                >
                  {{ task.status === "done" ? "Concluída" : "Pendente" }}
                </span>
                <!-- .stop: clicar no badge edita o departamento em vez de abrir a tarefa -->
                <div @click.stop>
                  <DepartmentBadge :task="task" />
                </div>
              </div>
              <span class="text-sm font-medium text-base-content">{{ task.name }}</span>
            </div>
            <span v-if="task.date_due" class="text-xs text-base-content/60">{{ displayDate(task.date_due) }}</span>
          </div>
        </div>
      </div>

      <!-- Datas -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-base-200 rounded-lg p-4">
          <div class="flex items-center gap-2 mb-1">
            <font-awesome-icon icon="fa fa-calendar-alt" class="text-error" />
            <label class="text-sm font-semibold text-base-content/80">Vencimento</label>
          </div>
          <p class="text-base-content">{{ formatDateBr(invoice.date_due) }}</p>
        </div>
        <div v-if="invoice.proposal" class="bg-base-200 rounded-lg p-4">
          <div class="flex items-center gap-2 mb-1">
            <font-awesome-icon icon="fas fa-credit-card" class="text-primary" />
            <label class="text-sm font-semibold text-base-content/80">Parcelamento</label>
          </div>
          <p class="text-base-content">{{ invoice.proposal.installment_quantity }}x</p>
        </div>
        <div class="bg-base-200 rounded-lg p-4">
          <div class="flex items-center gap-2 mb-1">
            <font-awesome-icon icon="fa fa-calendar-plus" class="text-base-content/60" />
            <label class="text-sm font-semibold text-base-content/80">Criação</label>
          </div>
          <p class="text-base-content">{{ formatDateBr(invoice.created_at) }}</p>
        </div>
      </div>

      <!-- Descrição -->
      <div class="mb-6 bg-base-200 rounded-lg p-4">
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
        <h3 class="text-lg font-bold text-base-content flex items-center gap-2">
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
        <div class="rounded-xl border border-base-300 bg-base-100 p-4 shadow-sm">
          <div class="text-xs font-semibold text-base-content/60">Total da Fatura</div>
          <div class="mt-1 font-bold text-base-content">
            <money-field name="total" :modelValue="invoiceTotal" readonly />
          </div>
        </div>
        <div
          class="rounded-xl border p-4 shadow-sm"
          :class="isDebit ? 'border-error/30 bg-error/10' : 'border-success/30 bg-success/10'"
        >
          <div class="text-xs font-semibold" :class="isDebit ? 'text-error' : 'text-success'">
            {{ isDebit ? "Total Pago" : "Total Recebido" }}
          </div>
          <div class="mt-1 font-bold" :class="isDebit ? 'text-error' : 'text-success'">
            <money-field name="paid" :modelValue="transactionsTotal" readonly />
          </div>
        </div>
        <div class="rounded-xl border border-info/30 bg-info/10 p-4 shadow-sm">
          <div class="text-xs font-semibold text-info">Saldo</div>
          <div class="mt-1 font-bold" :class="balance >= 0 ? 'text-info' : 'text-error'">
            <money-field name="balance" :modelValue="balance" readonly />
          </div>
        </div>
      </div>

      <!-- Pix do saldo em aberto: a receber usa a chave da conta, a pagar usa a do fornecedor -->
      <pix-payment-card
        v-if="canChargePix"
        class="mt-6"
        :title="isDebit ? 'Pagar via Pix' : 'Cobrar via Pix'"
        :pix="pix"
        :loading="pixLoading"
        :error="pixError"
        :is-payable="isDebit"
      >
        <template #error-action>
          <template v-if="isDebit">
            <button
              v-if="invoice.company_id"
              type="button"
              class="text-primary hover:underline font-medium ml-1"
              @click="openCompanyModal(invoice.company_id)"
            >
              Abrir empresa fornecedora
            </button>
            <button
              v-else-if="invoice.lead_id"
              type="button"
              class="text-primary hover:underline font-medium ml-1"
              @click="openLeadModal(invoice.lead_id)"
            >
              Abrir fornecedor
            </button>
          </template>
          <router-link
            v-else-if="accountId"
            :to="{ name: 'accountShow', params: { id: accountId } }"
            class="text-primary hover:underline font-medium ml-1"
            @click="$emit('close')"
          >
            Ir para Configurações
          </router-link>
        </template>
      </pix-payment-card>
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
            <span v-if="!compact" class="text-sm text-base-content/80 font-medium">quantidades</span>
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
import axios from "axios";
import { mapActions, mapMutations } from "vuex";
import { BACKEND_URL } from "@/config/apiConfig";
import { destroy, post, show, updateField } from "@/utils/requests/httpUtils";
import { displayDate, formatDateBr } from "@/utils/date/dateUtils";
import ModalCard from "@/components/modals/ModalCard.vue";
import MoneyField from "@/components/fields/number/MoneyField.vue";
import MoneyEditableField from "@/components/fields/number/MoneyEditableField.vue";
import SelectStatusButton from "@/components/buttons/SelectStatusButton.vue";
import TextAreaEditableInput from "@/components/forms/inputs/textarea/TextAreaEditableInput.vue";
import CompanyAvatar from "@/components/common/CompanyAvatar.vue";
import DepartmentBadge from "@/components/badges/DepartmentBadge.vue";
import CompaniesSelectEditableField from "@/components/fields/selects/CompaniesSelectEditableField.vue";
import LeadAvatar from "@/components/common/LeadAvatar.vue";
import LeadsSelectEditableField from "@/components/fields/selects/LeadsSelectEditableField.vue";
import TransactionsListSection from "@/components/show/TransactionsListSection.vue";
import PixPaymentCard from "@/components/common/PixPaymentCard.vue";

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
    DepartmentBadge,
    CompaniesSelectEditableField,
    LeadAvatar,
    LeadsSelectEditableField,
    TransactionsListSection,
    PixPaymentCard,
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
      isStartingTask: false,
      pix: null,
      pixError: null,
      pixLoading: false,
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
    canChargePix() {
      return (
        this.invoice &&
        this.invoice.status !== "cancelled" &&
        Number(this.invoice.price ?? 0) - Number(this.invoice.total_paid ?? 0) > 0
      );
    },
    accountId() {
      return this.$store.state.accountId ?? null;
    },
  },
  methods: {
    ...mapMutations(["openModal"]),
    ...mapActions(["checkOpenJourneys"]),
    displayDate,
    formatDateBr,
    getCategoryLabel(category) {
      return CATEGORY_LABELS[category] || category;
    },
    async getInvoice() {
      this.invoice = await show("invoices", this.invoiceId);
      this.getPix();
    },
    // O valor do QR é o saldo em aberto, então é regerado sempre que a fatura recarrega
    async getPix() {
      this.pix = null;
      this.pixError = null;
      if (!this.canChargePix) return;

      this.pixLoading = true;
      try {
        const response = await axios.get(`${BACKEND_URL}invoices/${this.invoice.id}/pix`);
        this.pix = response.data.data;
      } catch (error) {
        this.pixError = error.response?.data?.message || "Não foi possível gerar o QR Code Pix.";
      } finally {
        this.pixLoading = false;
      }
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
    openCompanyModal(companyId) {
      // Ao salvar a chave Pix na empresa fornecedora, o QR desta fatura já é regerado
      this.openModal({
        component: "CompanyDetailModal",
        props: { companyId },
        listeners: { "company-updated": this.refreshInvoice },
        id: `company-${companyId}`,
      });
    },
    openLeadModal(leadId) {
      // Ao salvar a chave Pix no fornecedor, o QR desta fatura já é regerado
      this.openModal({
        component: "LeadDetailModal",
        props: { leadId },
        listeners: { "lead-updated": this.refreshInvoice },
        id: `lead-${leadId}`,
      });
    },
    openOpportunityModal() {
      const opportunityId = this.invoice.proposal.opportunity.id;
      this.openModal({
        component: "OpportunityDetailModal",
        props: { opportunityId },
        listeners: {
          "opportunity-updated": this.getInvoice,
        },
        id: `opportunity-${opportunityId}`,
      });
    },
    // Cria a tarefa com prazo hoje, já com jornada aberta, e abre a tarefa (timer)
    async startTask() {
      this.isStartingTask = true;
      this.errorMessage = null;
      try {
        const { data: task } = await post(`invoices/${this.invoice.id}/start-task`);
        this.checkOpenJourneys();
        await this.refreshInvoice();
        this.openTaskModal(task.id);
      } catch (error) {
        this.errorMessage = error.response?.data?.message || "Erro ao iniciar tarefa. Tente novamente.";
      } finally {
        this.isStartingTask = false;
      }
    },
    openTaskCreateModal() {
      this.openModal({
        component: "TaskCreateForm",
        props: { invoice: this.invoice, opportunity: this.invoice.proposal?.opportunity || null },
        listeners: { "new-task-event": this.refreshInvoice },
        id: `invoice-task-create-${this.invoice.id}`,
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
