<template>
  <div class="container mb-5">
    <AddMessage v-if="messageStatus" :messageStatus="messageStatus" :messageText="messageText">
    </AddMessage>

    <div class="header">
      <div class="project" v-bind:class="project ? getStatusClass(project.status) : ''">
        <div v-if="project">

          <router-link :to="{ name: 'projectShow', params: { id: task.project_id } }">
            <div class="status" v-bind:class="getStatusClass(project.status)">
              <font-awesome-icon :icon="getStatusIcon(project.status)" />
              <p class="duration">
                {{ formatDuration(project.duration_time) }}
              </p>
            </div>
          </router-link>

          <ProjectsSelectInput label="Nome do Projeto" v-model="task.project_id"
            @update:modelValue="updateTask('project_id', $event)" fieldsToDisplay="name" autoSelect=false fieldNull="Nenhum"/>
        </div>
        <div v-else class="">
          <ProjectsSelectInput label="Adicionar projeto" v-model="task.project_id"
            @update:modelValue="updateTask('project_id', $event)" fieldsToDisplay="name" autoSelect=false />
        </div>
      </div>

      <div class="card" v-bind:class="getStatusClass(task.status)">
        <div class="row ms-1">
          <div class="col-1 status" v-bind:class="getStatusClass(task.status)">
            <font-awesome-icon :icon="getStatusIcon(task.status)" />
            <p class="duration">
              {{ formatDuration(task.duration_time) }}
            </p>
          </div>
          <div class="col-11 ps-3">
            <p class="title">
              <TextEditableField name="name" v-model="task.name" placeholder="descrição detalhada da tarefa"
                @save="updateTask('name', $event)" />
            </p>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <UsersSelectInput label="Responsável" v-model="task.user_id"
              @update:modelValue="updateTask('user_id', $event)" fieldsToDisplay="name" autoSelect=false />
          </div>
        </div>
      </div>
    </div>


    <div class="row pt-0">
      <div id="col-infos" class="col">
        <div class="row pt-5 pb-4">
          <DateEditableInput name="date_start" label="Início:" v-model="task.date_start"
            @save="updateTask('date_start', $event)" />
          <DateEditableInput name="date_due" label="Prazo:" v-model="task.date_due"
            @save="updateTask('date_due', $event)" />
          <DateEditableInput name="date_conclusion" label="Conclusão:" v-model="task.date_conclusion"
            @save="updateTask('date_conclusion', $event)" />
        </div>
        <div class="row pt-5 pb-3">
          <div class="duration">
            <PrioritySelectInput v-if="task.priority" v-model="task.priority"
              @update:modelValue="updateTask('priority', $event)" />
          </div>
        </div>
        <div class="row pt-5 mb-5">
          <div class="duration">
            <StatusLinearRadioInput v-if="task.status" :status="task.status"
              @status-change="updateTask('status', $event)" />
          </div>
        </div>
        <div class="row mt-5 mb-5">
          <TextEditor label="Descrição" name="description" v-model="task.description"
            @save="updateTask('description', $event)" />
        </div>
        <div class="row pt-5">
          <button class="col myButton delete" @click="deleteTask()">
            excluir
          </button>
        </div>

      </div>

      <div id="col-list" class="col pt-0">
        <JourneysList :taskId="taskId" @update-task-duration="updateTaskDuration()" />
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
import StatusLinearRadioInput from "@/components/forms/inputs/StatusLinearRadioInput.vue";
import PrioritySelectInput from "@/components/forms/inputs/PrioritySelectInput.vue";
import { translateStatus } from "@/utils/translations/translationsUtils";
import { translatePriority } from "@/utils/translations/translationsUtils";
import DateEditableInput from "@/components/fields/datetime/DateTimeEditableInput";
import JourneysList from "@/components/lists/JourneysList.vue";
import ProjectsSelectInput from "@/components/forms/selects/ProjectsSelectInput.vue";
import TextEditableField from "@/components/fields/text/TextEditableField";
import TextEditor from "@/components/forms/inputs/TextEditor.vue";
import UsersSelectInput from "@/components/forms/selects/UsersSelectInput.vue";

export default {
  name: "TaskShow",
  components: {
    DateEditableInput,
    JourneysList,
    PrioritySelectInput,
    ProjectsSelectInput,
    TextEditableField,
    TextEditor,
    StatusLinearRadioInput,
    UsersSelectInput,
  },
  data() {
    return {
      journeysData: [],
      journeysUrl: "",
      messageStatus: "",
      messageText: "",
      project: [],
      task: [],
      updatedField: [],
      taskId: "",
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
    convertUtcToLocal,
    async getTask() {
      try {
        const response = await axios.get(
          `${BACKEND_URL}${TASK_URL_PARAMETER}${this.taskId}`
        );

        this.task = response.data.data;
        this.project = this.task.project;
        this.taskLoaded = true; // Marque a tarefa como carregada
      } catch (error) {
        console.error("Erro ao acessar tarefa:", error);
      }
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
    setTaskId(taskId) {
      this.taskId = taskId;
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
  async mounted() {
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
  width: 80%;
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
