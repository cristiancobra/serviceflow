<template>
  <div class="container mb-5">
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
            <TextEditableInput
              name="name"
              v-model="task.name"
              placeholder="descrição detalhada da tarefa"
              @save="updateTask('name', $event)"
            />
          </p>
        </div>
      </div>
      <div class="row">
        <div class="col-3">
          <SelectInput
            label="Responsável"
            name="user_id"
            v-model="task.user_id"
            :items="users"
            fieldsToDisplay="name"
          />
        </div>
      </div>
    </div>

    <div class="row mb-5">
      <div class="col-12">
        <TextEditor
          label="Descrição"
          name="description"
          v-model="task.description"
          @save="updateTask('description', $event)"
        />
      </div>
    </div>

    <div class="row">
      <div class="col-12"></div>
    </div>

    <div class="row">
      <div class="col">
        <DateEditableInput
          name="date_start"
          label="Data de início:"
          v-model="task.date_start"
          @save="updateTask('date_start', $event)"
        />
        <p>
          <span class="duration"> Data de início: </span>
          {{ task.date_start }}
        </p>
        <p>
          <span class="duration"> Prazo final: </span>
          {{ formatDateBr(task.date_due) }}
        </p>
        <p>
          <span class="duration"> Data de conclusão: </span>
          {{ formatDateTimeBr(task.date_conclusion) }}
        </p>
      </div>

      <div class="col">
        <p>
          <span class="duration"> Prioridade: </span>
          {{ translatePriority(task.priority) }}
        </p>
        <p>
          <span class="duration"> Situação: </span>
          {{ translateStatus(task.status) }}
        </p>
      </div>
    </div>

    <div class="row mt-5 mb-5">
      <button class="offset-10 col-1 myButton delete" @click="deleteTask()">
        excluir
      </button>
    </div>

    <JourneysList
      :journeys="journeysData"
      @new-journey-event="addJourneyCreated"
      @journey-updated="updateJourneys"
      @journey-deleted="deleteJourney"
    />
  </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, TASK_URL } from "@/config/apiConfig";
import { formatDateBr } from "@/utils/date/dateUtils";
import { formatDateTimeBr } from "@/utils/date/dateUtils";
import { formatDuration } from "@/utils/date/dateUtils";
import { getStatusClass } from "@/utils/card/cardUtils";
import { getStatusIcon } from "@/utils/card/cardUtils";
import { translateStatus } from "@/utils/translations/translationsUtils";
import { translatePriority } from "@/utils/translations/translationsUtils";
import DateEditableInput from "@/components/forms/inputs/date/DateEditableInput";
import JourneysList from "@/components/lists/JourneysList.vue";
import TextEditableInput from "@/components/forms/inputs/text/TextEditableInput";
import TextEditor from "@/components/forms/inputs/TextEditor.vue";

export default {
  name: "TaskShow",
  components: {
    DateEditableInput,
    JourneysList,
    TextEditableInput,
    TextEditor,
  },
  data() {
    return {
      journeysData: [],
      task: [],
      editedTask: [],
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
    async getTask() {
      try {
        const response = await axios.get(
          `${BACKEND_URL}${TASK_URL}/${this.taskId}`
        );

        this.task = response.data.data;
        this.journeysData = this.task.journeys;
        this.taskLoaded = true; // Marque a tarefa como carregada
      } catch (error) {
        console.error("Erro ao acessar tarefa:", error);
      }
    },
    setTaskId(taskId) {
      this.taskId = taskId;
    },
    async deleteTask() {
      axios
        .delete(`http://localhost:8191/api/tasks/${this.taskId}`)
        .then((response) => {
          this.data = response.data;
          this.isSuccess = true;
          this.isError = false;
          this.$router.push({ name: "tasksIndex", query: { isSuccess: this.isSuccess } });
        })
        .catch((error) => {
          console.error("Erro ao deletar task:", error);
          this.isError = true;
          this.isSuccess = false;
        });
    },
    async updateTask(fieldName, editedValue) {
      const editedTask = { ...this.task };

      editedTask[fieldName] = editedValue;

      try {
        const response = await axios.put(
          `${BACKEND_URL}${TASK_URL}/${this.taskId}`,
          editedTask
        );

        this.task = response.data.data;
      } catch (error) {
        console.error("Erro ao atualizar a tarefa:", error);
      }
    },
    addJourneyCreated(newJourney) {
      // Add the new journey to the journeysData array
      this.journeysData.push(newJourney);
      this.updateTaskDuration();
    },
    deleteJourney(deletedJourneyId) {
      // Atualize a lista de jornadas após a exclusão
      this.journeysData = this.journeysData.filter(
        (journey) => journey.id !== deletedJourneyId
      );
    },
    updateJourneys(updatedJourney) {
      console.log("updatedJourney:", updatedJourney);
      console.log(updatedJourney);
      // Encontrar e atualizar a jornada na lista journeysData
      const index = this.journeysData.findIndex(
        (journey) => journey.id === updatedJourney.id
      );
      console.log("index:", index);

      if (index !== -1) {
        console.log("Before update:", this.journeysData[index]);
        this.journeysData[index] = updatedJourney;
        console.log("After update:", this.journeysData[index]);
      }
    },
    updateTaskDuration() {
      axios
        .get(`http://localhost:8191/api/tasks/${this.taskId}`)
        .then((response) => {
          this.task.duration = response.data.data.duration;
        })
        .catch((error) => console.log(error));
    },
  },
  async mounted() {
    this.setTaskId(this.$route.params.id);
    this.getTask();
  },
  computed: {
    translatedStatus() {
      return translateStatus(this.task.status);
    },
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
