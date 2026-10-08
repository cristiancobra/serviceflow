<template>
  <ModalCard
    title="Oportunidade"
    :subtitle="opportunity?.name || ''"
    :subtitle-editable="!!opportunity"
    subtitle-placeholder="Nome da oportunidade"
    icon="fa-solid fa-bullseye"
    size="xl"
    :compact="compact"
    compact-size="max-w-3xl"
    @close="$emit('close')"
    @save-subtitle="updateOpportunity('name', $event)"
  >
    <div v-if="!opportunity" class="p-5 text-center text-base-content/60">
      Carregando oportunidade...
    </div>

    <template v-else>
      <div
        v-if="message"
        class="flex items-center justify-between gap-4 rounded-lg p-3 mb-6 text-sm font-medium"
        :class="message.status === 'success' ? 'bg-success/10 text-success border border-success/30' : 'bg-error/10 text-error border border-error/30'"
      >
        <span>
          <font-awesome-icon
            :icon="message.status === 'success' ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-exclamation'"
            class="me-2"
          />
          {{ message.text }}
        </span>
        <button type="button" class="opacity-70 hover:opacity-100" @click="message = null">
          <font-awesome-icon icon="fa-solid fa-times" />
        </button>
      </div>

      <div role="tablist" class="tabs tabs-box mb-6">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          type="button"
          role="tab"
          class="tab gap-2"
          :class="{ 'tab-active': activeTab === tab.value }"
          :title="tabStatus[tab.value] ? `${tab.label}: ${tabStatus[tab.value].title}` : tab.label"
          @click="activeTab = tab.value"
        >
          <span
            class="inline-flex items-center justify-center size-6 rounded-full text-xs"
            :class="tabIconClass(tab.value)"
          >
            <font-awesome-icon :icon="tab.icon" />
          </span>
          <span v-if="!compact">{{ tab.label }}</span>
        </button>
      </div>

      <!-- Informações -->
      <div v-if="activeTab === 'info'">
        <div class="grid grid-cols-1 gap-6 mb-6" :class="compact ? '' : 'md:grid-cols-2'">
          <div class="flex flex-col gap-6">
            <opportunity-dates-section :opportunity="opportunity" @update-field="updateOpportunity"
              @update-fields="updateOpportunityFields" />
            <opportunity-info-section :opportunity="opportunity" @update-field="updateOpportunity" />
          </div>
          <opportunity-duration-section :opportunity="opportunity" />
        </div>

        <section-card title="Descrição">
          <text-editor
            name="description"
            :modelValue="opportunity.description"
            @save="updateOpportunity('description', $event)"
          />
        </section-card>
      </div>

      <!-- Propostas -->
      <div v-if="activeTab === 'proposals'">
        <proposals-list-section
          :proposals="opportunity.proposals || []"
          :opportunityId="opportunity.id"
          @proposal-added="handleProposalAdded"
          @proposal-updated="handleProposalUpdated"
        />
      </div>

      <!-- Faturas de receita (da proposta aceita) -->
      <div v-if="activeTab === 'creditInvoices'">
        <credit-invoices-section :proposal="safeAcceptedProposal" @reload-proposal="getOpportunity" />
      </div>

      <!-- Faturas de custos (da proposta aceita) -->
      <div v-if="activeTab === 'debitInvoices'">
        <debit-invoices-section :proposal="safeAcceptedProposal" @reload-proposal="getOpportunity" />
      </div>

      <!-- Anexos -->
      <div v-if="activeTab === 'attachments'">
        <links-list :links="opportunity.links || []" :opportunityId="opportunity.id" />
      </div>

      <!-- Tarefas -->
      <div v-if="activeTab === 'tasks'">
        <tasks-list-section
          :show-title="false"
          :tasks="opportunity.tasks || []"
          :opportunity="opportunity"
          sortOrder="asc"
        />
        <p class="text-sm text-base-content/80 mt-4">
          Total da oportunidade:
          <b class="text-primary">{{ formatDuration(opportunity.duration_time) }}</b>
        </p>
      </div>
    </template>

    <template v-if="opportunity" #footer>
      <div class="flex w-full items-center justify-between">
        <button type="button" class="btn btn-error" title="Excluir oportunidade" @click="deleteOpportunity">
          <font-awesome-icon icon="fa-solid fa-trash" :class="{ 'me-2': !compact }" />
          <span v-if="!compact">Excluir</span>
        </button>
        <div class="flex gap-2">
          <div
            v-if="!isValidDate(opportunity.date_conclusion) && !isValidDate(opportunity.date_canceled)"
            class="tooltip tooltip-left"
            :data-tip="finishBlockedMessage || null"
          >
            <button
              type="button"
              class="btn btn-success"
              title="Finalizar oportunidade"
              :disabled="!!finishBlockedMessage"
              @click="showFinishConfirm = true"
            >
              <font-awesome-icon icon="fa-solid fa-check" :class="{ 'me-2': !compact }" />
              <span v-if="!compact">Finalizar</span>
            </button>
          </div>
          <button type="button" class="btn" title="Fechar" @click="$emit('close')">
            <font-awesome-icon v-if="compact" icon="fa-solid fa-times" />
            <template v-else>Fechar</template>
          </button>
        </div>
      </div>

      <teleport to="body">
        <!-- z-[1000]: fica acima dos modais abertos via store (z-50) -->
        <div
          v-if="showFinishConfirm"
          class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm"
          @click.self="showFinishConfirm = false"
        >
          <ModalCard
            title="Finalizar Oportunidade"
            :icon="finishPendencies.length ? 'fa-solid fa-triangle-exclamation' : 'fa-solid fa-check'"
            size="sm"
            @close="showFinishConfirm = false"
          >
            <p class="text-base-content">
              A oportunidade será finalizada em <b>{{ formatLocalDate(lastJourneyEnd) }}</b>,
              data do término da última jornada.
            </p>

            <div v-if="finishPendencies.length" role="alert" class="alert alert-warning alert-soft mt-4 items-start">
              <font-awesome-icon icon="fa-solid fa-triangle-exclamation" class="mt-0.5" />
              <div>
                <p class="font-semibold">Esta oportunidade ainda tem pendências:</p>
                <ul class="list-disc ms-5 mt-1">
                  <li v-for="pendency in finishPendencies" :key="pendency">{{ pendency }}</li>
                </ul>
              </div>
            </div>

            <p v-if="finishPendencies.length" class="mt-4 text-sm text-base-content/60">Deseja finalizar mesmo assim?</p>

            <template #footer>
              <button type="button" class="btn btn-ghost" @click="showFinishConfirm = false">Cancelar</button>
              <button type="button" class="btn btn-success" @click="finishOpportunity">Finalizar</button>
            </template>
          </ModalCard>
        </div>
      </teleport>
    </template>
  </ModalCard>
</template>

<script>
import { mapState } from "vuex";
import { show, destroy, update, updateField } from "@/utils/requests/httpUtils";
import { formatDuration } from "@/utils/date/dateUtils";
import ModalCard from "@/components/modals/ModalCard.vue";
import SectionCard from "@/components/common/SectionCard.vue";
import CreditInvoicesSection from "@/components/lists/CreditInvoicesSection.vue";
import DebitInvoicesSection from "@/components/lists/DebitInvoicesSection.vue";
import LinksList from "@/components/lists/LinksList.vue";
import OpportunityInfoSection from "@/components/show/OpportunityInfoSection.vue";
import OpportunityDatesSection from "@/components/show/OpportunityDatesSection.vue";
import OpportunityDurationSection from "@/components/show/OpportunityDurationSection.vue";
import ProposalsListSection from "@/components/lists/ProposalsListSection.vue";
import TasksListSection from "@/components/lists/TasksListSection.vue";
import TextEditor from "@/components/forms/inputs/TextEditor.vue";

// Datas "zeradas" (epoch) chegam do backend no lugar de null em tarefas antigas
const isValidDate = (date) => !!date && !/^(1969-12-31|1970-01-01)/.test(date);

const TABS = [
  { value: "info", label: "Informações", icon: "fas fa-info" },
  { value: "attachments", label: "Anexos", icon: "fas fa-link" },
  { value: "proposals", label: "Propostas", icon: "fas fa-file-contract" },
  { value: "creditInvoices", label: "Faturas de receita", icon: "fas fa-file-invoice-dollar" },
  { value: "debitInvoices", label: "Faturas de custos", icon: "fas fa-file-invoice" },
  { value: "tasks", label: "Tarefas", icon: "fas fa-tasks" },
];

export default {
  name: "OpportunityDetailModal",
  components: {
    ModalCard,
    CreditInvoicesSection,
    DebitInvoicesSection,
    LinksList,
    OpportunityInfoSection,
    OpportunityDatesSection,
    OpportunityDurationSection,
    SectionCard,
    ProposalsListSection,
    TasksListSection,
    TextEditor,
  },
  props: {
    opportunityId: {
      type: [Number, String],
      required: true,
    },
    initialTab: {
      type: String,
      default: "info",
    },
    // id do elemento a destacar ao abrir (ex: "task-12"), usado pelos links que apontam para uma tarefa
    highlightElementId: {
      type: String,
      default: null,
    },
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
  },
  // opportunity-updated: qualquer alteração na oportunidade (payload: oportunidade atualizada)
  // opportunity-deleted: oportunidade excluída (payload: id)
  // tab-changed: aba selecionada (usado pela rota /opportunities/:id para manter ?tab= na URL)
  emits: ["close", "opportunity-updated", "opportunity-deleted", "tab-changed"],
  data() {
    return {
      opportunity: null,
      activeTab: TABS.some((tab) => tab.value === this.initialTab) ? this.initialTab : "info",
      message: null,
      tabs: TABS,
      showFinishConfirm: false,
    };
  },
  computed: {
    ...mapState(["updatedTask"]),
    acceptedProposal() {
      return this.opportunity?.proposals?.find((proposal) => proposal.status === "accepted") || null;
    },
    safeAcceptedProposal() {
      return this.acceptedProposal || { invoices: [] };
    },
    // Faturas da proposta aceita (as mesmas que as abas de faturas mostram) e tarefas,
    // ignorando canceladas
    statusCounts() {
      const invoices = (this.safeAcceptedProposal.invoices || []).filter(
        (invoice) => invoice.status !== "cancelled"
      );
      const invoiceCounts = (type) => {
        const ofType = invoices.filter((invoice) => invoice.type === type);
        return {
          done: ofType.filter((invoice) => invoice.status === "paid").length,
          total: ofType.length,
          overdue: ofType.filter((invoice) => invoice.status === "overdue").length,
        };
      };
      const tasks = (this.opportunity?.tasks || []).filter((task) => !isValidDate(task.date_canceled));
      return {
        creditInvoices: invoiceCounts("credit"),
        debitInvoices: invoiceCounts("debit"),
        tasks: {
          done: tasks.filter((task) => isValidDate(task.date_conclusion)).length,
          total: tasks.length,
        },
      };
    },
    // Jornadas de todas as tarefas (end vem do backend no fuso do usuário, "Y-m-d H:i:s")
    journeys() {
      return (this.opportunity?.tasks || []).flatMap((task) => task.journeys || []);
    },
    lastJourneyEnd() {
      return this.journeys.reduce((latest, journey) => (journey.end > latest ? journey.end : latest), "") || null;
    },
    finishBlockedMessage() {
      if (!this.journeys.length) return "A oportunidade não pode ser finalizada sem nenhuma jornada registrada.";
      if (this.journeys.some((journey) => !journey.end))
        return "A oportunidade não pode ser finalizada enquanto houver jornada em aberto.";
      return "";
    },
    // Avisos do modal de finalização: tarefas abertas e faturas a receber/a pagar
    finishPendencies() {
      const pendencies = [];
      const plural = (count, singular, pluralForm) => `${count} ${count === 1 ? singular : pluralForm}`;
      const { tasks, creditInvoices, debitInvoices } = this.statusCounts;
      const openTasks = tasks.total - tasks.done;
      const toReceive = creditInvoices.total - creditInvoices.done;
      const toPay = debitInvoices.total - debitInvoices.done;
      if (openTasks) pendencies.push(plural(openTasks, "tarefa aberta", "tarefas abertas"));
      if (toReceive) pendencies.push(plural(toReceive, "fatura a receber", "faturas a receber"));
      if (toPay) pendencies.push(plural(toPay, "fatura a pagar", "faturas a pagar"));
      return pendencies;
    },
    // Estado de cada aba que tem marcador: { level, title }. Abas fora daqui ficam neutras.
    tabStatus() {
      const result = {
        proposals: this.proposalsStatus(),
      };
      const titles = {
        creditInvoices: "faturas de receita recebidas",
        debitInvoices: "faturas de custo pagas",
        tasks: "tarefas concluídas",
      };
      Object.entries(this.statusCounts).forEach(([tab, counts]) => {
        // Sem itens (total 0) fica neutro: "nada a pagar" não é o mesmo que "tudo pago"
        if (!counts.total) return;
        result[tab] = this.progressStatus(counts, titles[tab]);
      });
      return result;
    },
  },
  methods: {
    tabIconClass(tab) {
      const classes = {
        success: "bg-success text-success-content",
        warning: "bg-warning text-warning-content",
        info: "bg-info text-info-content",
        error: "bg-error text-error-content",
      };
      return classes[this.tabStatus[tab]?.level] || "bg-base-300 text-base-content/70";
    },
    proposalsStatus() {
      const proposals = this.opportunity?.proposals || [];
      if (!proposals.length) return { level: "error", title: "nenhuma proposta" };
      if (this.acceptedProposal) return { level: "success", title: "proposta aceita" };
      return { level: "warning", title: "nenhuma proposta aceita" };
    },
    // Mesmas cores do ícone da fatura (invoiceIconClass): concluído, vencido, parcial, nada feito
    progressStatus({ done, total, overdue = 0 }, label) {
      let title = `${done} de ${total} ${label}`;
      if (overdue) title += ` (${overdue} ${overdue === 1 ? "vencida" : "vencidas"})`;
      if (done >= total) return { level: "success", title };
      if (overdue > 0) return { level: "error", title };
      if (done > 0) return { level: "warning", title };
      return { level: "info", title };
    },
    formatDuration,
    isValidDate,
    // "2026-10-08 18:30:00" → "08/10/2026", sem passar por Date (evita deslocar o dia pelo fuso)
    formatLocalDate(dateTime) {
      if (!dateTime) return "";
      const [year, month, day] = dateTime.slice(0, 10).split("-");
      return `${day}/${month}/${year}`;
    },
    // date_conclusion da oportunidade é só data: envia o dia local do término da última jornada
    async finishOpportunity() {
      this.showFinishConfirm = false;
      if (this.finishBlockedMessage) return;
      await this.updateOpportunityFields({ date_conclusion: this.lastJourneyEnd.slice(0, 10) });
      if (isValidDate(this.opportunity?.date_conclusion)) this.$emit("close");
    },
    async getOpportunity() {
      try {
        this.opportunity = await show("opportunities", this.opportunityId);
      } catch (error) {
        console.error("Erro ao carregar oportunidade:", error);
        this.message = { status: "error", text: "Erro ao carregar a oportunidade." };
      }
    },
    async updateOpportunity(fieldName, newValue) {
      try {
        this.opportunity = await updateField("opportunities", this.opportunityId, fieldName, newValue);
        this.$emit("opportunity-updated", this.opportunity);
      } catch (error) {
        console.error(`Erro ao atualizar ${fieldName}:`, error);
        this.message = { status: "error", text: "Erro ao atualizar a oportunidade. Tente novamente." };
      }
    },
    // Vários campos de uma vez (ex: prazo + motivo do adiamento)
    async updateOpportunityFields(fields) {
      try {
        this.opportunity = await update("opportunities", this.opportunityId, fields);
        this.$emit("opportunity-updated", this.opportunity);
      } catch (error) {
        const errors = error.response?.data?.errors;
        this.message = {
          status: "error",
          text: errors?.[Object.keys(fields)[0]]?.[0]
            || error.response?.data?.message
            || "Erro ao atualizar a oportunidade. Tente novamente.",
        };
        await this.getOpportunity();
      }
    },
    // Tarefa da oportunidade (ou que acabou de sair dela) teve jornada criada, editada ou excluída
    belongsToOpportunity(task) {
      return Number(task.opportunity_id) === Number(this.opportunityId)
        || !!this.opportunity?.tasks?.some((t) => t.id === task.id);
    },
    async updateOpportunityDuration() {
      const updated = await show("opportunities", this.opportunityId);
      this.opportunity.duration_time = updated.duration_time;
      this.opportunity.duration_by_department = updated.duration_by_department;
      this.opportunity.contracted_hours = updated.contracted_hours;
    },
    async deleteOpportunity() {
      if (!confirm("Tem certeza que deseja excluir esta oportunidade? Esta ação não pode ser desfeita.")) {
        return;
      }
      try {
        await destroy("opportunities", this.opportunityId);
        this.$emit("opportunity-deleted", this.opportunity.id);
        this.$emit("close");
      } catch (error) {
        console.error("Erro ao excluir oportunidade:", error);
        this.message = { status: "error", text: "Erro ao excluir a oportunidade. Tente novamente." };
      }
    },
    handleProposalAdded(newProposal) {
      if (!this.opportunity.proposals) this.opportunity.proposals = [];
      this.opportunity.proposals.push(newProposal);
      this.updateOpportunityDuration();
    },
    handleProposalUpdated(updatedProposal) {
      const index = this.opportunity.proposals.findIndex((proposal) => proposal.id === updatedProposal.id);
      if (index !== -1) this.opportunity.proposals.splice(index, 1, updatedProposal);
      this.updateOpportunityDuration();
    },
    highlightElement(attempts = 0) {
      if (!this.highlightElementId) return;
      const element = document.getElementById(this.highlightElementId);
      if (!element) {
        // A lista de tarefas renderiza depois dos dados; tenta de novo por alguns instantes
        if (attempts < 15) setTimeout(() => this.highlightElement(attempts + 1), 300);
        return;
      }
      element.scrollIntoView({ behavior: "smooth", block: "center" });
      element.classList.add("highlight-element");
      setTimeout(() => element.classList.remove("highlight-element"), 2000);
    },
  },
  watch: {
    opportunityId() {
      this.getOpportunity();
    },
    activeTab(tab) {
      this.$emit("tab-changed", tab);
    },
    // Alimentado pelo evento task-updated de qualquer modal (App.vue → store)
    updatedTask: {
      deep: true,
      handler(task) {
        if (task && this.opportunity && this.belongsToOpportunity(task)) {
          this.updateOpportunityDuration();
        }
      },
    },
  },
  async mounted() {
    await this.getOpportunity();
    this.$nextTick(() => this.highlightElement());
  },
};
</script>

<style scoped>
/* Destaque temporário da tarefa ao abrir a oportunidade a partir de um link para ela */
:deep(.highlight-element) {
  box-shadow: 0 0 15px color-mix(in oklab, var(--color-warning) 50%, transparent);
  animation: highlight-pulse 2s ease-in-out;
}

@keyframes highlight-pulse {
  0%,
  100% {
    background-color: transparent;
  }
  50% {
    background-color: color-mix(in oklab, var(--color-warning) 15%, var(--color-base-100));
  }
}
</style>
