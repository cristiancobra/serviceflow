<template>
  <div class="mb-5">
    <AddMessage v-if="messageStatus" :messageStatus="messageStatus" :messageText="messageText">
    </AddMessage>
    <div class="header-fixed">
      <div class="row ms-1">
        <div class="col-1 status">
          <font-awesome-icon icon="fa-solid fa-bullseye" class="primary" />
          <p class="duration">
            {{ formatDuration(opportunity.duration_time) }}
          </p>
        </div>
        <div class="col-8 ps-3">
          <p class="title">
            <TextEditableField name="name" v-model="opportunity.name" placeholder="descrição detalhada da tarefa"
              @save="updateOpportunity('name', $event)" />
          </p>
        </div>
        <div class="col-3">
          <div class="row">
            <DateEditableInput class="d-flex justify-content-end" name="date_start" label="Início:"
              v-model="opportunity.date_start" @save="updateOpportunity('date_start', $event)" />
          </div>
          <div class="row">
            <DateEditableInput class="d-flex justify-content-end" name="date_due" label="Prazo:"
              v-model="opportunity.date_due" @save="updateOpportunity('date_due', $event)" />
          </div>
          <div class="row">
            <DateEditableInput class="d-flex justify-content-end" name="date_conclusion" label="Conclusão:"
              v-model="opportunity.date_conclusion" @save="updateOpportunity('date_conclusion', $event)" />
          </div>
        </div>
      </div>
      <div class="header-fixed-menu mt-3">
        <button class="item-menu" @click="currentSection = 'info'" :class="{ active: currentSection === 'info' }">
          Informações
        </button>
        <button class="item-menu" @click="currentSection = 'proposals'" :class="{ active: currentSection === 'proposals' }">
          Propostas
        </button>
        <button class="item-menu" @click="currentSection = 'tasks'" :class="{ active: currentSection === 'tasks' }">
          Tarefas
        </button>
      </div>
    </div>
    <div class="info-container" v-show="currentSection === 'info'">
      <div class="list-container pt-4">
        <div class="col-3">
          <companies-select-editable-field label="Empresa" name="company_id" v-model="opportunity.company_id"
            @update:modelValue="updateOpportunity('company_id', $event)" />
        </div>
        <div class="col-3">
          <leads-select-editable-field label="Cliente" name="lead_id" v-model="opportunity.lead_id"
            @update:modelValue="updateOpportunity('lead_id', $event)" />
        </div>
        <div class="col-3">
          <users-select-editable-field label="Responsável" name="user_id" v-model="opportunity.user_id"
            @update:modelValue="updateOpportunity('user_id', $event)" />
        </div>
        <div class="row pt-2">
          <TextEditor label="Descrição" name="description" v-model="opportunity.description"
          @save="updateOpportunity('description', $event)" />
        </div>
      </div>
    </div>
    <div class="info-container" v-show="currentSection === 'proposals'">
      <proposals-list :opportunityId="opportunityId" />
    </div>
    <div class="info-container" v-show="currentSection === 'tasks'">
      <tasks-list template="opportunity" :opportunityId="opportunityId"
        @update-opportunity-duration="updateOpportunityDuration()" />
    </div>
    <div class="row d-flex justify-content-end mt-2 mb-5 me-5">
      <div class="col-1">
        <button class="button delete" @click="deleteOpportunity()">
          excluir
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, OPPORTUNITY_URL_PARAMETER } from "@/config/apiConfig";
import { formatDuration, convertDateTimeToLocal } from "@/utils/date/dateUtils";
import { destroy, show, updateField } from "@/utils/requests/httpUtils";
import { formatDateBr, formatDateTimeBr, getStatusClass, getStatusIcon } from "@/utils/card/cardUtils";
import { provide, ref } from 'vue';
import { translateStatus } from "@/utils/translations/translationsUtils";
import { translatePriority } from "@/utils/translations/translationsUtils";
import CompaniesSelectEditableField from '../../components/fields/selects/CompaniesSelectEditableField.vue';
import DateEditableInput from "@/components/fields/datetime/DateTimeEditableInput";
import LeadsSelectEditableField from '../../components/fields/selects/LeadsSelectEditableField.vue';
import ProposalsList from "@/components/lists/ProposalsList.vue";
import TasksList from "@/components/lists/TasksList.vue";
import TextEditableField from "@/components/fields/text/TextEditableField";
import TextEditor from "@/components/forms/inputs/TextEditor.vue";
import UsersSelectEditableField from "@/components/fields/selects/UsersSelectEditableField.vue";

export default {
  name: "ProjectShow",
  components: {
    CompaniesSelectEditableField,
    DateEditableInput,
    LeadsSelectEditableField,
    ProposalsList,
    TasksList,
    TextEditableField,
    TextEditor,
    UsersSelectEditableField,
  },
  data() {
    return {
      currentSection: 'info',
      journeysData: [],
      journeysUrl: "",
      messageStatus: "",
      messageText: "",
      opportunity: [],
      editedProject: [],
      opportunityId: this.$route.params.id,

    };
  },
  setup() {
    const currentOpportunity = ref(null);
    provide('currentOpportunity', currentOpportunity);
    return {
      currentOpportunity,
    };
  },
  emits: ["new-journey-event", "journey-updated", "journey-deleted"],
  methods: {
    formatDateBr,
    formatDateTimeBr,
    formatDuration,
    getStatusClass,
    getStatusIcon,
    translateStatus,
    translatePriority,
    convertDateTimeToLocal,
    async getOpportunity() {
      this.opportunity = await show("opportunities", this.opportunityId);
      this.currentOpportunity = this.opportunity;
      convertDateTimeToLocal(this.opportunity.date_start);
      this.opportunityLoaded = true;
    },
    setOpportunityId(opportunityId) {
      this.opportunityId = opportunityId;
    },
    async deleteOpportunity() {
      this.response = await destroy('opportunities', this.opportunityId);
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
      this.opportunity = await updateField("opportunities", this.opportunityId, fieldName, editedValue);
    },
    updateOpportunityDuration() {
      axios
        .get(`${BACKEND_URL}${OPPORTUNITY_URL_PARAMETER}${this.opportunityId}`)
        .then((response) => {
          this.opportunity.duration_time = response.data.data.duration_time;
        })
        .catch((error) => console.log(error));
    },
  },
  computed: {
    translatedStatus() {
      return translateStatus(this.opportunity.status);
    },
    localDate(date) {
      return convertDateTimeToLocal(date);
    },
  },
  mounted() {
    this.setOpportunityId(this.$route.params.id);
    this.getOpportunity();
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

.duration {
  font-weight: 800;
  font-size: 1.2rem;
  text-align: center;
}

.status {
  text-align: center;
  font-size: 3rem;
}

.title {
  font-size: 24px;
  font-weight: 900;
  padding-top: 10px;
  padding-bottom: 0px;
  color: black;
}

</style>
