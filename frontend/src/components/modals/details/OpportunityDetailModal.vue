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
          :title="tab.label"
          @click="activeTab = tab.value"
        >
          <font-awesome-icon :icon="tab.icon" />
          <span v-if="!compact">{{ tab.label }}</span>
        </button>
      </div>

      <!-- Informações -->
      <div v-if="activeTab === 'info'">
        <div class="grid grid-cols-1 gap-6 mb-6" :class="compact ? '' : 'md:grid-cols-2'">
          <div class="flex flex-col gap-6">
            <opportunity-dates-section :opportunity="opportunity" @update-field="updateOpportunity" />
            <opportunity-info-section :opportunity="opportunity" @update-field="updateOpportunity" />
          </div>
          <opportunity-duration-section :opportunity="opportunity" />
        </div>

        <section-card title="Descrição" icon="fas fa-align-left">
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

      <!-- Faturas (da proposta aceita) -->
      <div v-if="activeTab === 'invoices'">
        <credit-invoices-section :proposal="safeAcceptedProposal" @reload-proposal="getOpportunity" />
        <debit-invoices-section :proposal="safeAcceptedProposal" @reload-proposal="getOpportunity" />
      </div>

      <!-- Anexos -->
      <div v-if="activeTab === 'attachments'">
        <links-list :links="opportunity.links || []" :opportunityId="opportunity.id" />
      </div>

      <!-- Tarefas -->
      <div v-if="activeTab === 'tasks'">
        <tasks-list-section
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
        <button type="button" class="btn" title="Fechar" @click="$emit('close')">
          <font-awesome-icon v-if="compact" icon="fa-solid fa-times" />
          <template v-else>Fechar</template>
        </button>
      </div>
    </template>
  </ModalCard>
</template>

<script>
import { mapState } from "vuex";
import { show, destroy, updateField } from "@/utils/requests/httpUtils";
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

const TABS = [
  { value: "info", label: "Informações", icon: "fas fa-info" },
  { value: "proposals", label: "Propostas", icon: "fas fa-file-contract" },
  { value: "invoices", label: "Faturas", icon: "fas fa-file-invoice-dollar" },
  { value: "attachments", label: "Anexos", icon: "fas fa-link" },
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
  },
  methods: {
    formatDuration,
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
