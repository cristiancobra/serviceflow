<template>
  <div class="container mb-5">
    <AddMessage v-if="messageStatus" :messageStatus="messageStatus" :messageText="messageText">
    </AddMessage>
    <div class="show-title">
      <div class="row ms-1">
        <div class="col-1 status">
          <font-awesome-icon icon="fa-solid fa-bullseye" class="primary" />
          <p class="duration">
            {{ opportunity.duration_time }}
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
      <div class="row">
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
      </div>
    </div>
    <div class="description-container">
      <TextEditor label="Descrição" name="description" v-model="opportunity.description"
        @save="updateOpportunity('description', $event)" />
    </div>
    <div class="row pt-2">
      <proposals-list :opportunityId="opportunityId" />
    </div>
    <div class="row pt-2">
      <tasks-list template="opportunity" :opportunityId="opportunityId" />
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
import { BACKEND_URL, OPPORTUNITY_URL_PARAMETER, PROJECT_URL_PARAMETER } from "@/config/apiConfig";
import { convertDateTimeToLocal } from "@/utils/date/dateUtils";
import { show, updateField } from "@/utils/requests/httpUtils";
import { formatDateBr, formatDateTimeBr, formatDuration, getStatusClass, getStatusIcon } from "@/utils/card/cardUtils";
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
      console.log(this.opportunityId);
      this.opportunity = await show("opportunities", this.opportunityId);
      this.currentOpportunity = this.opportunity;
      convertDateTimeToLocal(this.opportunity.date_start);
      this.opportunityLoaded = true;
    },
    setOpportunityId(opportunityId) {
      this.opportunityId = opportunityId;
    },
    async deleteOpportunity() {
      axios
        .delete(`${BACKEND_URL}${OPPORTUNITY_URL_PARAMETER}${this.opportunityId}`)
        .then((response) => {
          this.data = response.data;
          this.isSuccess = true;
          this.isError = false;
          this.$router.push({
            name: "opportunitysIndex",
            query: { isSuccess: this.isSuccess },
          });
          this.messageStatus = "deleted";
          this.messageText = "Jornada deletada com sucesso!";
        })
        .catch((error) => {
          console.error("Erro ao deletar opportunity:", error);
          this.isError = true;
          this.isSuccess = false;
        });
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
      // const updatedField = {};

      // updatedField[fieldName] = editedValue;

      // try {
      //   const response = await axios.put(
      //     `${BACKEND_URL}${PROJECT_URL_PARAMETER}${this.opportunityId}`,
      //     updatedField
      //   );

      //   this.opportunity = response.data.data;
      // } catch (error) {
      //   console.error("Erro ao atualizar a tarefa:", error);
      // }
    },
    updateOpportunityDuration() {
      axios
        .get(`${BACKEND_URL}${PROJECT_URL_PARAMETER}${this.opportunityId}`)
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

.card {
  margin-bottom: 60px;
  margin-top: 60px;
  border-style: solid;
  border-width: 2px;
  border-color: gray;
  border-radius: 6px;
  padding: 10px;
  padding-right: 20px;
  min-height: 15vh;
}

.done {
  background-color: var(--green-light);
  border-color: var(--green);
  color: var(--green);
}

.doing {
  background-color: var(--blue-light);
  border-color: var(--blue);
  color: var(--blue);
}

.to-do {
  background-color: var(--orange-light);
  border-color: var(--orange);
  color: var(--orange);
}

.wait {
  background-color: var(--gray-light);
  border-color: var(--gray);
  color: var(--gray);
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

.container {
  margin-left: 10vw;
  margin-right: 10vw;
}

.myButton {
  border-width: 2px;
  border-style: solid;
  border-color: white;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  /* margin: 0 4px 0 4px; */
  color: white;
  font-weight: 800;
  /* width: 120px; */
}

.delete {
  background-color: #ffa1a1;
  border-color: #c82333;
  color: #c82333;
}

.delete:hover {
  background-color: #c82333;
  border-color: #c82333;
  color: white;
}

.icon {
  font-size: 1.2rem;
  text-align: center;
  font-weight: 400;
  color: var(--green);
}
</style>
