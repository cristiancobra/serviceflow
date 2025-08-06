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
            v-model="opportunity.name"
            placeholder="descrição detalhada da tarefa"
            @save="updateOpportunity('name', $event)"
          />
        </h1>
      </div>
      <div class="page-action">
        <font-awesome-icon
          v-if="!opportunity.date_conclusion"
          icon="fa-solid fa-circle-check"
          class="big-icon done"
        />
        <font-awesome-icon
          v-if="!opportunity.date_canceled"
          icon="fa-solid fa-x"
          class="big-icon canceled"
        />
      </div>
      <div class="page-action">
        <p class="show-duration">
          {{ formatDuration(opportunity.duration_time) }}
        </p>
      </div>
    </div>

    <nav class="section-menu">
      <button class="item-menu" @click="scrollToSection('info')">
        <font-awesome-icon icon="fas fa-file-invoice" class="icon" />
      </button>
      <button class="item-menu" @click="scrollToSection('proposals')">
        <font-awesome-icon icon="fas fa-money-bill" class="icon" />
      </button>
      <button class="item-menu" @click="scrollToSection('attachments')">
        <font-awesome-icon icon="fas fa-link" class="icon" />
      </button>
      <button class="item-menu" @click="scrollToSection('journeys')">
        <font-awesome-icon icon="fas fa-clock" class="icon" />
      </button>
    </nav>

    <section id="info" class="section-container">
      <div class="section-title">
        <font-awesome-icon icon="fas fa-file-invoice" class="icon" />
        <h2>Informações</h2>
      </div>
      <div class="table-row">
        <div class="column-70">
          <div class="table-row">
            <companies-select-editable-field
              label="Empresa"
              name="company_id"
              v-model="opportunity.company_id"
              @update:modelValue="updateOpportunity('company_id', $event)"
            />
          </div>
          <div class="table-row">
            <leads-select-editable-field
              label="Cliente"
              name="lead_id"
              v-model="opportunity.lead_id"
              @update:modelValue="updateOpportunity('lead_id', $event)"
            />
          </div>
          <div class="table-row">
            <users-select-editable-field
              label="Responsável"
              name="user_id"
              v-model="opportunity.user_id"
              @update:modelValue="updateOpportunity('user_id', $event)"
            />
          </div>
        </div>
        <div class="column-30">
          <div class="table-row">
            <DateEditableInput
              class="d-flex justify-content-end"
              name="date_start"
              label="Início:"
              v-model="opportunity.date_start"
              @save="updateOpportunity('date_start', $event)"
            />
          </div>

          <div class="table-row">
            <DateEditableInput
              class="d-flex justify-content-end"
              name="date_due"
              label="Prazo:"
              v-model="opportunity.date_due"
              @save="updateOpportunity('date_due', $event)"
            />
          </div>
          <div class="table-row">
            <DateEditableInput
              class="d-flex justify-content-end"
              name="date_conclusion"
              label="Conclusão:"
              v-model="opportunity.date_conclusion"
              @save="updateOpportunity('date_conclusion', $event)"
            />
          </div>
          <div class="table-row">
            <DateEditableInput
              class="d-flex justify-content-end"
              name="date_conclusion"
              label="Cancelado:"
              v-model="opportunity.date_canceled"
              @save="updateOpportunity('date_canceled', $event)"
            />
          </div>
        </div>
      </div>
    </section>

    <section class="section-container">
      <div class="table-row">
        <TextEditor
          label="Descrição"
          name="description"
          v-model="opportunity.description"
          @save="updateOpportunity('description', $event)"
        />
      </div>
    </section>

    <section id="proposals">
      <proposals-list-section :opportunityId="opportunityId" />
    </section>

    <section id="attachments">
      <links-list :links="opportunity.links" :opportunityId="opportunityId" />
    </section>

    <section id="tasks">
      <tasks-list-section
        :tasks="opportunity.tasks"
        @update-opportunity-duration="updateOpportunityDuration()"
      />
    </section>

    <div class="final-row">
        <button class="button delete" @click="deleteOpportunity()">
          excluir
        </button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, OPPORTUNITY_URL_PARAMETER } from "@/config/apiConfig";
import { formatDuration, convertDateTimeToLocal } from "@/utils/date/dateUtils";
import { destroy, show, updateField } from "@/utils/requests/httpUtils";
import {
  formatDateBr,
  formatDateTimeBr,
  getStatusClass,
  getStatusIcon,
} from "@/utils/card/cardUtils";
import { scrollToSection } from "@/utils/layout/navigationUtils";
import { provide, ref } from "vue";
import { translateStatus } from "@/utils/translations/translationsUtils";
import { translatePriority } from "@/utils/translations/translationsUtils";
import CompaniesSelectEditableField from "../../components/fields/selects/CompaniesSelectEditableField.vue";
import DateEditableInput from "@/components/fields/datetime/DateTimeEditableInput";
import LeadsSelectEditableField from "../../components/fields/selects/LeadsSelectEditableField.vue";
import LinksList from "@/components/lists/LinksList.vue";
import ProposalsListSection from "@/components/lists/ProposalsListSection.vue";
import TextEditableField from "@/components/fields/text/TextEditableField";
import TextEditor from "@/components/forms/inputs/TextEditor.vue";
import UsersSelectEditableField from "@/components/fields/selects/UsersSelectEditableField.vue";
import TasksListSection from '../../components/lists/TasksListSection.vue';

export default {
  name: "ProjectShow",
  components: {
    CompaniesSelectEditableField,
    DateEditableInput,
    LeadsSelectEditableField,
    LinksList,
    ProposalsListSection,
    TextEditableField,
    TextEditor,
    UsersSelectEditableField,
    TasksListSection,
  },
  data() {
    return {
      currentSection: "info",
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
    scrollToSection,
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

.status {
  text-align: center;
  font-size: 3rem;
}
</style>
