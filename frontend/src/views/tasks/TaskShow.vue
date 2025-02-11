<template>
  <div class="mb-5">
    <AddMessage v-if="messageStatus" :messageStatus="messageStatus" :messageText="messageText">
    </AddMessage>
    <div class="header-fixed">
      <div class="row ms-0">
        <div class="col-1 status">
          <font-awesome-icon icon="fa-solid fa-list-check" class="show-title-icon " />
          <p class="show-duration">
            {{ formatDuration(task.duration_time) }}
          </p>
        </div>
        <div class="col-11 ps-3">
          <p class="show-title d-flex">
            <TextEditableField name="name" v-model="task.name" placeholder="descrição detalhada da tarefa"
              @save="updateTask('name', $event)" />
          </p>
          <p class="opportunity" v-if="task.opportunity">
            <router-link :to="'/opportunities/' + task.opportunity.id">
              <font-awesome-icon icon="fa-solid fa-search" class="primary" />
            </router-link>
            {{ task.opportunity.name }}
          </p>
        </div>
      </div>
      <div class="header-fixed-menu mt-3">
        <button class="item-menu" @click="currentSection = 'info'" :class="{ active: currentSection === 'info' }">
          Informações
        </button>
        <button class="item-menu" @click="currentSection = 'attachments'"
          :class="{ active: currentSection === 'attachments' }">
          Anexos
        </button>
        <button class="item-menu" @click="currentSection = 'journeys'"
          :class="{ active: currentSection === 'journeys' }">
          Jornadas
        </button>
      </div>
    </div>

    <div class="content-below-header">
      <div class="info-container" v-show="currentSection === 'info'">
        <div class="list-container d-flex pt-4">
          <div class="col-6">
            <users-select-editable-field label="Responsável" name="user_id" v-model="task.user_id"
              @update:modelValue="updateTask('user_id', $event)" />
            <projects-select-editable-field label="Projeto" v-model="task.project_id"
              @update:modelValue="updateTask('project_id', $event)" fieldNull="Nenhum" />
            <opportunities-select-editable-field label="Oportunidade" v-model="task.opportunity_id"
              @update:modelValue="updateTask('opportunity_id', $event)" fieldNull="Nenhum" />
          </div>
          <div class="col-6">
            <div class="row">
              <DateEditableInput class="d-flex justify-content-end" name="date_start" label="Início:"
                v-model="task.date_start" @save="updateTask('date_start', $event)" />
            </div>
            <div class="row mt-2">
              <DateEditableInput class="d-flex justify-content-end" name="date_due" label="Prazo:"
                v-model="task.date_due" @save="updateTask('date_due', $event)" />
            </div>
            <div class="row mt-2">
              <div class="col d-flex justify-content-end">
                <button v-if="showEndTaskButton" class="button-circular primary me-3" @click="updateDateConclusion"
                  :title="endTaskTitle">
                  <font-awesome-icon icon="fa-solid fa-check-square" />
                </button>
                <DateEditableInput class="d-flex justify-content-end" name="date_conclusion" label="Conclusão:"
                  v-model="task.date_conclusion" @save="updateTask('date_conclusion', $event)" />
              </div>
            </div>
            <div class="row mt-2">
              <DateEditableInput class="d-flex justify-content-end" name="date_conclusion" label="Cancelado:"
                v-model="task.date_canceled" @save="updateTask('date_canceled', $event)" />
            </div>
          </div>
        </div>
        <div class="list-container d-flex pt-4">
          <TextEditor label="Descrição" name="description" v-model="task.description"
            @save="updateTask('description', $event)" />
        </div>
      </div>

      <div class="info-container" v-show="currentSection === 'attachments'">
        <links-list :links="task.links" :taskId="taskId" />
      </div>

      <div class="info-container" v-show="currentSection === 'journeys'">
        <journeys-list template="by-task" :taskId="taskId" @update-task-duration="updateTaskDuration()"
          @last-journey-end="updateEndTaskButtonVisibility" />
      </div>

      <div class="d-flex justify-content-end">
        <task-clone-form :task="task" />
        <button class="button delete" @click="deleteTask()">
          <font-awesome-icon icon="fa-solid fa-trash" class="" />
          excluir
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, TASK_URL_PARAMETER } from "@/config/apiConfig";
import { convertUtcToLocal } from "@/utils/date/dateUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import { formatDateTimeBr } from "@/utils/date/dateUtils";
import { formatDuration } from "@/utils/date/dateUtils";
import { getStatusClass } from "@/utils/card/cardUtils";
import { getStatusIcon } from "@/utils/card/cardUtils";
import { translateStatus } from "@/utils/translations/translationsUtils";
import { translatePriority } from "@/utils/translations/translationsUtils";
import DateEditableInput from "@/components/fields/datetime/DateTimeEditableInput";
import { show } from "@/utils/requests/httpUtils.js";
import JourneysList from "@/components/lists/JourneysList.vue";
import OpportunitiesSelectEditableField from '../../components/fields/selects/OpportunitiesSelectEditableField.vue';
import ProjectsSelectEditableField from "@/components/fields/selects/ProjectsSelectEditableField.vue";
import TextEditableField from "@/components/fields/text/TextEditableField";
import TextEditor from "@/components/forms/inputs/TextEditor.vue";
import UsersSelectEditableField from "@/components/fields/selects/UsersSelectEditableField.vue";
import TaskCloneForm from '../../components/forms/TaskCloneForm.vue';
import LinksList from '../../components/lists/LinksList.vue';

export default {
  name: "TaskShow",
  components: {
    DateEditableInput,
    JourneysList,
    OpportunitiesSelectEditableField,
    ProjectsSelectEditableField,
    TextEditableField,
    TextEditor,
    UsersSelectEditableField,
    TaskCloneForm,
    LinksList
  },
  data() {
    return {
      currentSection: 'info',
      // journeysData: [],
      journeysUrl: "",
      journeyEnd: "",
      messageStatus: "",
      messageText: "",
      project: [],
      showEndTaskButton: false,
      task: {
        date_conclusion: null,
        journeys: [],
      },
      updatedField: [],
      taskId: "",
    };
  },
  computed: {
    showEndTask() {
      return !this.task.date_conclusion && this.task.journeys.length > 0;
    },
    latestJourneyEnd() {
      if (this.task.journeys.length === 0) return null;
      const sortedJourneys = [...this.task.journeys].sort((a, b) => new Date(b.end) - new Date(a.end));
      console.log("sortedJourneys", sortedJourneys);
      return sortedJourneys[0].end;
    },
    endTaskTitle() {
      return `Finalizar tarefa com data da última jornada ${this.latestJourneyEnd ? this.latestJourneyEnd : ''}`;
    },
  },
  methods: {
    formatDateBr,
    formatDateTimeBr,
    formatDuration,
    getStatusClass,
    getStatusIcon,
    show,
    translateStatus,
    translatePriority,
    convertUtcToLocal,
    async getTask() {
      this.task = await show("tasks", this.taskId);
      this.project = this.task.project;
      this.taskLoaded = true; // Marque a tarefa como carregada
    },
    async deleteTask() {
      axios
        .delete(`${BACKEND_URL}${TASK_URL_PARAMETER}${this.taskId}`)
        .then((response) => {
          this.data = response.data;
          this.isSuccess = true;
          this.isError = false;
          this.$router.push({
            name: "tasksIndex",
            query: { isSuccess: this.isSuccess },
          });
          this.messageStatus = "deleted";
          this.messageText = "Jornada deletada com sucesso!";
        })
        .catch((error) => {
          console.error("Erro ao deletar task:", error);
          this.isError = true;
          this.isSuccess = false;
        });
    },
    async reloadTask(newId) {
      this.task = await show("tasks", newId);
      console.log("taskReload", this.task);
      this.project = this.task.project;
      this.taskLoaded = true; // Marque a tarefa como carregada
    },
    setTaskId(taskId) {
      this.taskId = taskId;
    },
    updateDateConclusion() {
      if (this.journeyEnd) {
        this.updateTask('date_conclusion', this.journeyEnd)
        this.showEndTaskButton = false;
      } else {
        console.log('Nenhum item encontrado na lista de journeys');
      }
    },
    updateEndTaskButtonVisibility(journeyEnd) {
      this.journeyEnd = journeyEnd;
      console.log("journeyEnd", journeyEnd);
      if (journeyEnd && this.task.date_conclusion === null) {
        this.showEndTaskButton = true;
      } else {
        this.showEndTaskButton = false;
      }
    },
    // updateJourneys(updatedJourney) {
    //   const index = this.journeysData.findIndex(
    //     (journey) => journey.id === updatedJourney.id
    //   );

    //   if (index !== -1) {
    //     this.journeysData[index] = updatedJourney;
    //   }
    // },
    async updateTask(fieldName, editedValue) {
      const updatedField = {};
      updatedField[fieldName] = editedValue;

      try {
        const response = await axios.put(
          `${BACKEND_URL}${TASK_URL_PARAMETER}${this.taskId}`,
          updatedField
        );

        this.task = response.data.data;
        this.project = this.task.project;
      } catch (error) {
        console.error("Erro ao atualizar a tarefa:", error);
      }
    },
    updateTaskDuration() {
      axios
        .get(`${BACKEND_URL}${TASK_URL_PARAMETER}${this.taskId}`)
        .then((response) => {
          this.task.duration_time = response.data.data.duration_time;
        })
        .catch((error) => console.log(error));
    },
  },
  watch: {
    '$route.params.id': {
      immediate: true,
      handler(newId) {
        this.reloadTask(newId);
      }
    }
  },
  async mounted() {
    console.log("showButon", this.showEndTaskButton);
    this.setTaskId(this.$route.params.id);
    this.getTask();
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
  display: block;
  text-align: center;
  font-size: 3rem;
}

.header {
  display: flex;
  margin: 0;
}

.project {
  width: 20%;
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

.opportunity {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--gray);
}
</style>
