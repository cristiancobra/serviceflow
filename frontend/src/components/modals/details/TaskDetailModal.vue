<template>
  <ModalCard
    title="Tarefa"
    :subtitle="task?.name || ''"
    :subtitle-editable="!!task"
    subtitle-placeholder="Nome da tarefa"
    icon="fa-solid fa-tasks"
    size="xl"
    :compact="compact"
    compact-size="max-w-2xl"
    @close="closeModal"
    @save-subtitle="updateTask('name', $event)"
  >
    <div v-if="!task" class="p-5 text-center text-base-content/60">
      Carregando tarefa...
    </div>

    <template v-else>
      <!-- Erro ao salvar um campo (ex: concluir tarefa sem jornada registrada) -->
      <div v-if="errorMessage" role="alert" class="alert alert-error alert-soft mb-6">
        <font-awesome-icon icon="fa-solid fa-circle-exclamation" />
        <span>{{ errorMessage }}</span>
        <button type="button" class="btn btn-ghost btn-sm btn-circle" title="Fechar" @click="errorMessage = null">
          <font-awesome-icon icon="fa-solid fa-times" />
        </button>
      </div>

      <!-- Status, oportunidade/projeto e fatura (o nome da tarefa fica editável no cabeçalho) -->
      <div class="flex flex-wrap items-center gap-4 mb-6 pb-6 border-b border-base-300">
        <font-awesome-icon v-if="task.date_canceled" icon="fas fa-times-circle" class="text-3xl text-error"
          title="Tarefa cancelada" />
        <font-awesome-icon v-else icon="fas fa-check-circle" class="text-3xl"
          :class="isValidDate(task.date_conclusion) ? 'text-success' : 'text-base-content/50'"
          :title="isValidDate(task.date_conclusion) ? 'Tarefa concluída' : 'Tarefa em aberto'" />

        <div v-if="task.opportunity" class="flex items-center gap-2 text-sm">
          <font-awesome-icon icon="fa-solid fa-bullseye" class="text-primary" />
          <button type="button" class="text-primary hover:underline font-medium" @click="openOpportunityModal">
            {{ task.opportunity.name }}
          </button>
        </div>

        <div v-else-if="task.project" class="flex items-center gap-2 text-sm">
          <font-awesome-icon icon="fa-solid fa-folder-open" class="text-primary" />
          <router-link :to="{ name: 'projectShow', params: { id: task.project.id } }"
            class="text-primary hover:underline font-medium">
            {{ task.project.name }}
          </router-link>
        </div>

        <div v-else class="flex items-center gap-2 text-sm">
          <template v-if="!showOpportunitySelect">
            <font-awesome-icon icon="fa-solid fa-bullseye" class="text-base-content/50" />
            <button type="button" class="text-base-content/50 hover:text-primary font-medium transition-colors"
              @click="showOpportunitySelect = true">
              Adicionar oportunidade
            </button>
          </template>
          <template v-else>
            <opportunities-select-input name="opportunity_id" label="Oportunidade" fieldToDisplay="name"
              fieldNull="Nenhuma" v-model="selectedOpportunity" @update:modelValue="onOpportunitySelected" />
            <button type="button" class="text-base-content/50 hover:text-error ml-1 transition-colors" title="Cancelar"
              @click="showOpportunitySelect = false">
              <font-awesome-icon icon="fa-solid fa-times" />
            </button>
          </template>
        </div>

        <!-- Fatura vinculada: atalho para registrar o pagamento -->
        <div v-if="task.invoice" class="flex items-center gap-2 text-sm">
          <font-awesome-icon icon="fa-solid fa-file-invoice-dollar" class="text-primary" />
          <button type="button" class="text-primary hover:underline font-medium" title="Abrir fatura"
            @click="openInvoiceModal">
            {{ invoiceLabel }}
          </button>
          <span class="badge badge-sm" :class="invoiceStatusClass">{{ formatCurrencySymbol(task.invoice.balance ?? task.invoice.price) }}</span>
          <button type="button" class="text-base-content/50 hover:text-error ml-1 transition-colors"
            title="Desvincular fatura" @click="updateTask('invoice_id', null)">
            <font-awesome-icon icon="fa-solid fa-times" />
          </button>
        </div>

        <div v-else-if="isFinancialTask" class="flex items-center gap-2 text-sm">
          <template v-if="!showInvoiceSelect">
            <font-awesome-icon icon="fa-solid fa-file-invoice-dollar" class="text-base-content/50" />
            <button type="button" class="text-base-content/50 hover:text-primary font-medium transition-colors"
              @click="showInvoiceSelect = true">
              Vincular fatura
            </button>
          </template>
          <template v-else>
            <invoices-select-input name="invoice_id" label="Fatura" fieldNull="Nenhuma" :type="financialInvoiceType"
              v-model="selectedInvoice" @update:modelValue="onInvoiceSelected" />
            <button type="button" class="text-base-content/50 hover:text-error ml-1 transition-colors" title="Cancelar"
              @click="showInvoiceSelect = false">
              <font-awesome-icon icon="fa-solid fa-times" />
            </button>
          </template>
        </div>
      </div>

      <!-- Datas e Duração -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-base-200 rounded-lg p-4">
          <div class="flex items-center gap-2 mb-2">
            <font-awesome-icon icon="fa-solid fa-exclamation-circle" class="text-error" />
            <label class="text-sm font-semibold text-base-content/80">Data de Vencimento</label>
          </div>
          <due-date-editable-input :modelValue="task.date_due" :classText="getDeadlineClass(task.date_due)"
            :changes="task.due_date_changes || []" @save="updateTaskFields" />
        </div>

        <div class="bg-base-200 rounded-lg p-4">
          <div class="flex items-center gap-2 mb-2">
            <font-awesome-icon icon="fa-solid fa-check-circle" class="text-success" />
            <label class="text-sm font-semibold text-base-content/80">Data de Conclusão</label>
          </div>
          <date-time-editable-input name="date_conclusion" v-model="task.date_conclusion"
            @save="updateTask('date_conclusion', $event)" />
        </div>

        <div class="bg-primary-50 rounded-lg p-4">
          <div class="flex items-center gap-2 mb-2">
            <font-awesome-icon icon="fa-solid fa-clock" class="text-primary" />
            <label class="text-sm font-semibold text-base-content/80">Duração</label>
            <span v-if="isJourneyRunning" class="flex items-center gap-1 text-xs font-semibold text-success">
              <span class="w-2 h-2 rounded-full bg-success animate-pulse"></span>
              AO VIVO
            </span>
          </div>
          <p class="text-2xl font-bold text-primary">
            <journey-timer v-if="isJourneyRunning" :base-seconds="task.duration_time || 0" />
            <template v-else>{{ formatDuration(task.duration_time) }}</template>
          </p>
        </div>
      </div>

      <!-- Descrição -->
      <div class="mb-6 bg-base-200 rounded-lg p-4">
        <text-area-editable-input name="description" label="Descrição" v-model="task.description"
          placeholder="Adicione uma descrição detalhada da tarefa" @save="updateTask('description', $event)" />
      </div>

      <!-- Formulário de Nova Jornada -->
      <div v-if="showJourneyForm" class="mb-6">
        <journey-create-form :taskId="task.id" @new-journey-event="addJourneyCreated"
          @close="showJourneyForm = false" />
      </div>

      <!-- Área de Cancelamento -->
      <div v-if="showCancelArea" class="bg-error/10 border border-error/30 rounded-lg p-6 mb-6">
        <h4 class="text-lg font-bold text-error mb-4">Cancelar Tarefa</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <date-time-editable-input name="date_canceled" v-model="task.date_canceled" label="Data de Cancelamento"
            @save="updateTask('date_canceled', $event)" />
          <cancellation-reason-select-input name="cancellation_reason" v-model="task.cancellation_reason"
            :disabled="!task.date_canceled" @update:modelValue="updateTask('cancellation_reason', $event)" />
        </div>
      </div>
    </template>

    <!-- Footer com Ações -->
    <template v-if="task" #footer>
      <div class="flex justify-between items-center gap-2 w-full">
        <!-- Ações Rápidas -->
        <div class="flex" :class="compact ? 'gap-2' : 'gap-3'">
          <button type="button"
            class="px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-purple-600 transition-colors"
            @click="isJourneyRunning ? stopJourney() : quickStartJourney()"
            :title="isJourneyRunning ? 'Parar jornada' : 'Iniciar jornada agora'">
            <font-awesome-icon :icon="isJourneyRunning ? 'fa-solid fa-stop' : 'fa-solid fa-bolt'"
              :class="{ 'me-2': !compact }" />
            <span v-if="!compact">{{ isJourneyRunning ? 'Parar' : 'Iniciar' }}</span>
          </button>

          <button type="button"
            class="px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-purple-600 transition-colors"
            @click="openJourneysModal" title="Ver jornadas da tarefa">
            <font-awesome-icon icon="fa-solid fa-clock" :class="{ 'me-2': !compact }" />
            <span v-if="!compact">Jornadas</span>
            <span v-if="task.journeys && task.journeys.length" :class="{ 'ms-1': compact }">({{ task.journeys.length
              }})</span>
          </button>

          <add-journey-button :is-open="showJourneyForm" :icon-only="compact" @click="toggleJourneyForm"
            title="Adicionar jornada manualmente" />

          <button type="button"
            class="px-4 py-2 bg-info text-white rounded-lg font-semibold hover:bg-info transition-colors"
            @click="openLinksModal" title="Ver links da tarefa">
            <font-awesome-icon icon="fa-solid fa-link" :class="{ 'me-2': !compact }" />
            <span v-if="!compact">Links</span>
            <span v-if="task.links && task.links.length" :class="{ 'ms-1': compact }">({{ task.links.length }})</span>
          </button>

          <button type="button"
            class="px-4 py-2 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors"
            @click="cloneTask" title="Clonar tarefa">
            <font-awesome-icon icon="fa-solid fa-copy" :class="{ 'me-2': !compact }" />
            <span v-if="!compact">Clonar</span>
          </button>

          <button type="button"
            class="px-4 py-2 bg-error text-white rounded-lg font-semibold hover:bg-error transition-colors"
            @click="toggleCancelArea" :title="showCancelArea ? 'Ocultar cancelamento' : 'Cancelar tarefa'">
            <font-awesome-icon icon="fa-solid fa-times-circle" :class="{ 'me-2': !compact }" />
            <span v-if="!compact">{{ showCancelArea ? 'Ocultar' : 'Cancelar' }}</span>
          </button>
        </div>


        <!-- Botões Finalizar + Fechar -->
        <div class="flex gap-2">
          <div v-if="!task.date_conclusion" class="relative group">
            <button type="button" :disabled="!canFinishTask"
              class="px-4 py-2 text-white rounded-lg font-semibold transition-colors" :class="canFinishTask
                ? 'bg-success hover:bg-success cursor-pointer'
                : 'bg-base-300 cursor-not-allowed opacity-60'" @click="finishTask" title="Finalizar tarefa">
              <font-awesome-icon icon="fa-solid fa-check" :class="{ 'me-2': !compact }" />
              <span v-if="!compact">Finalizar</span>
            </button>
            <div v-if="!canFinishTask"
              class="absolute bottom-full right-0 mb-2 w-64 bg-gray-800 text-white text-xs rounded-lg px-3 py-2 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50">
              {{ canFinishTaskMessage }}
              <div class="absolute top-full right-4 border-4 border-transparent border-t-gray-800"></div>
            </div>
          </div>

          <button type="button"
            class="py-2 text-base-content/80 bg-base-100 border border-base-300 rounded-lg font-semibold hover:bg-base-200 transition-colors"
            :class="compact ? 'px-4' : 'px-6'" @click="closeModal" title="Fechar">
            <font-awesome-icon v-if="compact" icon="fa-solid fa-times" />
            <template v-else>Fechar</template>
          </button>
        </div>
      </div>
    </template>
  </ModalCard>
</template>

<script>
import axios from "axios";
import { mapMutations, mapActions, mapState } from "vuex";
import { formatDuration } from "@/utils/date/dateUtils";
import { getDeadlineClass } from "@/utils/card/cardUtils";
import { formatCurrencySymbol } from "@/utils/number/moneyUtils";
import { BACKEND_URL, TASK_URL_PARAMETER, JOURNEY_URL_PARAMETER } from "@/config/apiConfig";
import DateTimeEditableInput from "@/components/fields/datetime/DateTimeEditableInput.vue";
import DueDateEditableInput from "@/components/fields/datetime/DueDateEditableInput.vue";
import TextAreaEditableInput from "@/components/forms/inputs/textarea/TextAreaEditableInput.vue";
import CancellationReasonSelectInput from "@/components/forms/selects/CancellationReasonSelectInput.vue";
import JourneyCreateForm from "@/components/forms/JourneyCreateForm.vue";
import ModalCard from "@/components/modals/ModalCard.vue";
import AddJourneyButton from "@/components/buttons/AddJourneyButton.vue";
import OpportunitiesSelectInput from "@/components/forms/selects/OpportunitiesSelectInput.vue";
import InvoicesSelectInput from "@/components/forms/selects/InvoicesSelectInput.vue";
import JourneyTimer from "@/components/journeys/JourneyTimer.vue";

export default {
  name: "TaskDetailModal",
  components: {
    DateTimeEditableInput,
    DueDateEditableInput,
    TextAreaEditableInput,
    CancellationReasonSelectInput,
    JourneyCreateForm,
    ModalCard,
    AddJourneyButton,
    OpportunitiesSelectInput,
    InvoicesSelectInput,
    JourneyTimer,
  },
  props: {
    taskId: {
      type: Number,
      required: true,
    },
    compact: {
      type: Boolean,
      default: false,
    },
  },
  emits: ["close", "task-updated"],
  computed: {
    ...mapState(["openJourney"]),
    isJourneyRunning() {
      return !!(this.task && this.openJourney && this.openJourney.task_id === this.task.id);
    },
    canFinishTask() {
      if (!this.task) return false;
      const journeys = this.task.journeys || [];
      if (journeys.length === 0) return false;
      return journeys.every(j => j.end);
    },
    // Tarefa de pagamento/recebimento: criada a partir de uma fatura ("Pagar: ..."/"Receber: ...")
    // ou do departamento financeiro. Só nelas aparece a opção de vincular fatura.
    isFinancialTask() {
      if (!this.task) return false;
      if (/^(Pagar|Receber):/i.test(this.task.name || '')) return true;
      const department = this.task.department;
      return !!department && (department.slug === 'financeiro' || /financeiro/i.test(department.name || ''));
    },
    // Pré-filtra o seletor: "Receber:" só faturas a receber, "Pagar:" só a pagar
    financialInvoiceType() {
      if (/^Receber:/i.test(this.task?.name || '')) return 'credit';
      if (/^Pagar:/i.test(this.task?.name || '')) return 'debit';
      return null;
    },
    invoiceLabel() {
      const invoice = this.task?.invoice;
      if (!invoice) return '';
      const name = invoice.name || `Fatura #${invoice.id}`;
      return invoice.installment_quantity > 1
        ? `${name} (${invoice.installment_number}/${invoice.installment_quantity})`
        : name;
    },
    invoiceStatusClass() {
      return {
        paid: 'badge-success',
        partial: 'badge-warning',
        overdue: 'badge-error',
        cancelled: 'badge-ghost',
      }[this.task?.invoice?.status] || 'badge-info';
    },
    canFinishTaskMessage() {
      if (!this.task) return '';
      const journeys = this.task.journeys || [];
      if (journeys.length === 0)
        return 'A tarefa não pode ser finalizada sem nenhuma jornada registrada.';
      if (journeys.some(j => !j.end))
        return 'A tarefa não pode ser finalizada enquanto houver jornada em aberto.';
      return '';
    },
  },
  data() {
    return {
      task: null,
      loading: false,
      errorMessage: null,
      showCancelArea: false,
      showJourneyForm: false,
      showOpportunitySelect: false,
      selectedOpportunity: null,
      showInvoiceSelect: false,
      selectedInvoice: null,
    };
  },
  methods: {
    formatDuration,
    getDeadlineClass,
    formatCurrencySymbol,
    ...mapMutations(["openModal"]),
    ...mapActions(["checkOpenJourneys"]),

    cloneTask() {
      this.openModal({
        component: "TaskCreateForm",
        props: {
          opportunity: this.task.opportunity || null,
          project: this.task.project || null,
          cloneFrom: this.task,
        },
        id: "task-create",
      });
    },

    async loadTask() {
      if (!this.taskId) return;

      this.loading = true;
      try {
        const response = await axios.get(`${BACKEND_URL}${TASK_URL_PARAMETER}${this.taskId}`);
        this.task = response.data.data;

        // Se tarefa já está cancelada, mostra a área de cancelamento
        if (this.task.date_canceled) {
          this.showCancelArea = true;
        }
      } catch (error) {
        console.error("Erro ao carregar tarefa:", error);
      } finally {
        this.loading = false;
      }
    },

    async updateTask(fieldName, editedValue) {
      await this.updateTaskFields({ [fieldName]: editedValue });
    },

    async updateTaskFields(fields) {
      const fieldName = Object.keys(fields)[0];

      try {
        const response = await axios.put(
          `${BACKEND_URL}${TASK_URL_PARAMETER}${this.taskId}`,
          fields
        );
        this.errorMessage = null;
        this.task = response.data.data;
        this.$emit('task-updated', this.task);
      } catch (error) {
        console.error("Erro ao atualizar a tarefa:", error);
        const errors = error.response?.data?.errors;
        this.errorMessage = errors?.[fieldName]?.[0]
          || error.response?.data?.message
          || "Erro ao atualizar a tarefa. Tente novamente.";
        // O v-model já alterou o campo localmente; recarrega para voltar ao valor salvo
        await this.loadTask();
      }
    },

    async quickStartJourney() {
      try {
        const quickForm = {
          task_id: this.taskId,
          start: new Date(),
          user_timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
        };

        const response = await axios.post(`${BACKEND_URL}journeys`, quickForm);
        const newJourney = response.data.data;

        // Adiciona a nova jornada à lista
        if (!this.task.journeys) this.task.journeys = [];
        this.task.journeys.unshift(newJourney);

        this.$emit('task-updated', this.task);
        this.checkOpenJourneys();
      } catch (error) {
        console.error("Erro ao iniciar jornada:", error);
      }
    },

    async stopJourney() {
      const journeyId = this.openJourney?.id;
      if (!journeyId) return;
      try {
        await axios.put(`${BACKEND_URL}${JOURNEY_URL_PARAMETER}${journeyId}`, {
          id: journeyId,
          end: new Date().toISOString(),
        });
        this.$store.commit("setOpenJourney", null);
        await this.refreshTask();
      } catch (error) {
        console.error("Erro ao parar jornada:", error);
      }
    },

    async finishTask() {
      if (!this.canFinishTask) return;
      const sortedJourneys = [...this.task.journeys].sort(
        (a, b) => new Date(b.end) - new Date(a.end)
      );
      await this.updateTask("date_conclusion", sortedJourneys[0].end);
      this.closeModal();
    },

    toggleCancelArea() {
      this.showCancelArea = !this.showCancelArea;
    },

    toggleJourneyForm() {
      this.showJourneyForm = !this.showJourneyForm;
    },

    addJourneyCreated({ journey }) {
      if (!this.task.journeys) this.task.journeys = [];
      this.task.journeys.unshift(journey);
      this.showJourneyForm = false;
      this.$emit('task-updated', this.task);
    },

    openJourneysModal() {
      this.openModal({
        component: "TaskJourneysModal",
        props: { taskId: this.task.id, taskName: this.task.name },
        id: `task-journeys-${this.task.id}`,
      });
    },

    openOpportunityModal() {
      this.openModal({
        component: "OpportunityDetailModal",
        props: { opportunityId: this.task.opportunity.id },
        listeners: {
          "opportunity-updated": this.onOpportunityUpdated,
          "opportunity-deleted": this.refreshTask,
        },
        id: `opportunity-${this.task.opportunity.id}`,
      });
    },
    onOpportunityUpdated(opportunity) {
      if (this.task.opportunity?.id !== opportunity.id) return;
      this.task.opportunity = { ...this.task.opportunity, name: opportunity.name };
      this.$emit('task-updated', this.task);
    },

    openInvoiceModal() {
      this.openModal({
        component: "InvoiceDetailModal",
        props: { invoiceId: this.task.invoice.id },
        listeners: {
          "invoice-updated": this.refreshTask,
          "invoice-deleted": this.refreshTask,
        },
        id: `invoice-${this.task.invoice.id}`,
      });
    },

    async onInvoiceSelected(invoiceId) {
      if (!invoiceId) return;
      await this.updateTask('invoice_id', invoiceId);
      this.showInvoiceSelect = false;
      this.selectedInvoice = null;
    },

    openLinksModal() {
      this.openModal({
        component: "LinksModal",
        props: {
          taskId: this.task.id,
          taskName: this.task.name,
          opportunityId: this.task.opportunity?.id || null,
        },
        listeners: {
          "links-changed": this.onLinksChanged,
        },
        id: `task-links-${this.task.id}`,
      });
    },
    onLinksChanged(links) {
      this.task.links = [...links];
      this.$emit('task-updated', this.task);
    },

    async refreshTask() {
      await this.loadTask();
      this.$emit('task-updated', this.task);
    },

    async onOpportunitySelected(opportunityId) {
      if (!opportunityId) return;
      await this.updateTask('opportunity_id', opportunityId);
      this.showOpportunitySelect = false;
      this.selectedOpportunity = null;
    },

    isValidDate(date) {
      if (
        date != "1969-12-31 18:00:00" &&
        date != "1969-12-31 21:00:00" &&
        date != "1970-01-01 00:00:00" &&
        date != null
      ) {
        return true;
      }
      return false;
    },

    closeModal() {
      this.$emit("close");
    },
  },
  watch: {
    taskId() {
      this.loadTask();
    },
  },
  mounted() {
    this.loadTask();
  },
};
</script>

<style scoped>
/* Estilo para o div de visualização (não editando) */
:deep(.w-full.border-none.p-1) {
  color: color-mix(in oklab, var(--color-base-content) 60%, transparent);
  /* text-base-content/60 - placeholder */
  font-style: italic;
  min-height: 80px;
}

/* Quando tem conteúdo no div de visualização */
:deep(.w-full.border-none.p-1:not(:empty)) {
  color: color-mix(in oklab, var(--color-base-content) 80%, transparent);
  /* text-base-content/80 - texto normal */
  font-style: normal;
}

/* Estilo para o textarea quando está editando */
:deep(textarea) {
  color: var(--color-base-content) !important;
  /* text-base-content - texto digitado visível */
  background-color: var(--color-base-100) !important;
}

/* Força visibilidade dos botões Salvar/Cancelar */
:deep(button) {
  display: inline-block !important;
  opacity: 1 !important;
  visibility: visible !important;
  pointer-events: auto !important;
}

/* Garante contraste nos botões */
:deep(.bg-primary-500) {
  background-color: var(--color-info) !important;
  color: white !important;
}

:deep(.bg-gray-500) {
  background-color: #6b7280 !important;
  color: white !important;
}
</style>
