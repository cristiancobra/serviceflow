<template>
  <div class="page-container">
    <AddMessage
      v-if="messageStatus"
      :messageStatus="messageStatus"
      :messageText="messageText"
    >
    </AddMessage>

    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-bullseye" class="page-icon" />
        <h1>
          <TextEditableField
            name="name"
            class="text-white text-2xl font-bold"
            v-model="opportunity.name"
            placeholder="descrição detalhada da tarefa"
            @save="updateOpportunity('name', $event)"
          />
        </h1>
      </div>
    </div>

    <nav class="section-menu">
      <button
        :class="[
          'w-10 h-10 ms-1 me-1 flex items-center justify-center rounded-full text-white transition duration-200 ease-in-out',
          activeTab === 'info' ? 'bg-secondary ring-2 ring-white shadow-lg' : 'bg-primary hover:bg-secondary'
        ]"
        @click="activeTab = 'info'"
        title="Informações"
      >
        <font-awesome-icon icon="fas fa-info" class="icon" />
      </button>
      <button
        :class="[
          'w-10 h-10 ms-1 me-1 flex items-center justify-center rounded-full text-white transition duration-200 ease-in-out',
          activeTab === 'proposals' ? 'bg-secondary ring-2 ring-white shadow-lg' : 'bg-primary hover:bg-secondary'
        ]"
        @click="activeTab = 'proposals'"
        title="Propostas e Faturas"
      >
        <font-awesome-icon icon="fas fa-money-bill" class="icon" />
      </button>
      <button
        :class="[
          'w-10 h-10 ms-1 me-1 flex items-center justify-center rounded-full text-white transition duration-200 ease-in-out',
          activeTab === 'attachments' ? 'bg-secondary ring-2 ring-white shadow-lg' : 'bg-primary hover:bg-secondary'
        ]"
        @click="activeTab = 'attachments'"
        title="Anexos"
      >
        <font-awesome-icon icon="fas fa-link" class="icon" />
      </button>
      <button
        :class="[
          'w-10 h-10 ms-1 me-1 flex items-center justify-center rounded-full text-white transition duration-200 ease-in-out',
          activeTab === 'tasks' ? 'bg-secondary ring-2 ring-white shadow-lg' : 'bg-primary hover:bg-secondary'
        ]"
        @click="activeTab = 'tasks'"
        title="Tarefas"
      >
        <font-awesome-icon icon="fas fa-tasks" class="icon" />
      </button>
    </nav>

    <!-- Tab Info -->
    <div v-if="activeTab === 'info'" class="mt-6">
      <div class="flex mb-6 gap-10">
        <opportunity-info-section
          :opportunity="opportunity"
          @update-field="handleUpdateField"
        />

        <opportunity-dates-section
          :opportunity="opportunity"
          @update-field="handleUpdateField"
        />
        
      </div>

      <section class="section-container">
        <div class="flex-1 mr-10 mb-6 p-10 border border-primary rounded-lg">
          <h2 class="text-xl text-primary font-semibold">Descrição</h2>
          <text-editor
            name="description"
            v-model="opportunity.description"
            @save="updateOpportunity('description', $event)"
          />
        </div>
      </section>
    </div>

    <!-- Tab Propostas -->
    <section v-if="activeTab === 'proposals'" class="mt-6">
      <div class="flex flex-col lg:flex-row gap-6">
        <div class="flex-1">
          <proposals-list-section
            :proposals="opportunity.proposals || []"
            :opportunityId="opportunityId"
            @proposal-added="handleProposalAdded"
            @proposal-updated="handleProposalUpdated"
          />
        </div>
        <div class="flex-1">
          <credit-invoices-section
            :proposal="safeAcceptedProposal"
            @reload-proposal="getOpportunity"
          />
        </div>
      </div>
    </section>

    <!-- Tab Anexos -->
    <section v-if="activeTab === 'attachments'" class="mt-6">
                    <button-new-form 
                  target="link" 
                  @open-modal="isCreateLinkModalVisible = true" 
              />
      <links-list :links="opportunity.links || []" :opportunityId="opportunityId" />
    </section>

    <!-- Tab Tarefas -->
    <section v-if="activeTab === 'tasks'" class="mt-6">
      <tasks-list-section
        :tasks="opportunity.tasks || []"
        :opportunity="opportunity"
        sortOrder="asc"
        @update-opportunity-duration="updateOpportunityDuration()"
      />
         Total da oportunidade:    {{ formatDuration(opportunity.duration_time) }}
    </section>

    <div class="final-row">
      <button class="btn btn-error" @click="deleteOpportunity()">
        excluir
      </button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, OPPORTUNITY_URL_PARAMETER } from "@/config/apiConfig";
import { formatDuration, formatDateBr, formatDateTimeBr } from "@/utils/date/dateUtils";
import { destroy, show, updateField } from "@/utils/requests/httpUtils";
import {
  getStatusClass,
  getStatusIcon,
} from "@/utils/card/cardUtils";
import { provide, ref } from "vue";
import { translateStatus } from "@/utils/translations/translationsUtils";
import { translatePriority } from "@/utils/translations/translationsUtils";
import CreditInvoicesSection from "@/components/lists/CreditInvoicesSection.vue";
import LinksList from "@/components/lists/LinksList.vue";
import OpportunityInfoSection from "@/components/show/OpportunityInfoSection.vue";
import OpportunityDatesSection from "@/components/show/OpportunityDatesSection.vue";
import ProposalsListSection from "@/components/lists/ProposalsListSection.vue";
import TextEditableField from "@/components/fields/text/TextEditableField.vue";
import TextEditor from "@/components/forms/inputs/TextEditor.vue";
import TasksListSection from "../../components/lists/TasksListSection.vue";

export default {
  name: "ProjectShow",
  components: {
    CreditInvoicesSection,
    LinksList,
    OpportunityInfoSection,
    OpportunityDatesSection,
    ProposalsListSection,
    TextEditableField,
    TextEditor,
    TasksListSection,
  },
  data() {
    return {
      activeTab: "info",
      currentSection: "info",
      journeysData: [],
      journeysUrl: "",
      messageStatus: "",
      messageText: "",
      opportunity: {},
      editedProject: [],
      opportunityId: this.$route.params.id,
    };
  },
  setup() {
    const currentOpportunity = ref(null);
    provide("currentOpportunity", currentOpportunity);
    return {
      currentOpportunity,
    };
  },
  methods: {
    formatDateBr,
    formatDateTimeBr,
    formatDuration,
    getStatusClass,
    getStatusIcon,
    translateStatus,
    translatePriority,
    async getOpportunity() {
      this.opportunity = await show("opportunities", this.opportunityId);
      this.currentOpportunity = this.opportunity;
      this.opportunityLoaded = true;
    },
    setOpportunityId(opportunityId) {
      this.opportunityId = opportunityId;
    },
    async deleteOpportunity() {
      this.response = await destroy("opportunities", this.opportunityId);
      this.$router.push({ name: "opportunitiesIndex" });
    },
    updateJourneys(updatedJourney) {
      // Encontrar e atualizar a jornada na lista journeysData
      const index = this.journeysData.findIndex(
        (journey) => journey.id === updatedJourney.id
      );

      if (index !== -1) {
        this.journeysData[index] = updatedJourney;
      }
    },
    async updateOpportunity(fieldName, editedValue) {
      this.opportunity = await updateField(
        "opportunities",
        this.opportunityId,
        fieldName,
        editedValue
      );
    },
    async handleUpdateField(fieldName, editedValue) {
      this.opportunity = await updateField(
        "opportunities",
        this.opportunityId,
        fieldName,
        editedValue
      );
    },
    updateOpportunityDuration() {
      axios
        .get(`${BACKEND_URL}${OPPORTUNITY_URL_PARAMETER}${this.opportunityId}`)
        .then((response) => {
          this.opportunity.duration_time = response.data.data.duration_time;
        })
        .catch((error) => console.log(error));
    },
    handleProposalAdded(newProposal) {
      this.opportunity.proposals.push(newProposal);
    },
    handleProposalUpdated(updatedProposal) {
      const index = this.opportunity.proposals.findIndex(
        (proposal) => proposal.id === updatedProposal.id
      );
      if (index !== -1) {
        this.opportunity.proposals.splice(index, 1, updatedProposal);
      }
    },
  },
  computed: {
    translatedStatus() {
      return translateStatus(this.opportunity.status);
    },
    acceptedProposal() {
      return (
        this.opportunity.proposals?.find(
          (proposal) => proposal.status === "accepted"
        ) || null
      );
    },
    safeAcceptedProposal() {
      return this.acceptedProposal || { invoices: [] };
    },
  },
  mounted() {
    this.setOpportunityId(this.$route.params.id);
    
    // Define a tab inicial pela query string ou usa 'info' como padrão
    if (this.$route.query.tab) {
      this.activeTab = this.$route.query.tab;
    }
    
    this.getOpportunity();
  },
  watch: {
    activeTab(newTab) {
      // Atualiza a URL quando mudar de tab (sem reload da página)
      this.$router.replace({ query: { ...this.$route.query, tab: newTab } });
    }
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
p {
  text-align: left;
  font-size: 1.2rem;
  font-weight: 400;
}

h3 {
  margin: 40px 0 0;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  display: inline-block;
  margin: 0 10px;
}

a {
  color: rgb(61, 61, 61);
}

a:link {
  text-decoration: none;
}

a:visited {
  text-decoration: none;
}

a:hover {
  text-decoration: none;
}

a:active {
  text-decoration: none;
}

.status {
  text-align: center;
  font-size: 3rem;
}
</style>
