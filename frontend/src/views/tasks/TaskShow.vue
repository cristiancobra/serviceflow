<template>
  <div class="container mb-5">
    <div class="card" v-bind:class="getStatusClass(task.status)">
        
          <div class="row ms-1">
            <div
              class="col-1 status"
              v-bind:class="getStatusClass(task.status)"
            >
              <font-awesome-icon :icon="getStatusIcon(task.status)" />
              <p class="label">
                {{ formatDuration(task.duration_time) }}
              </p>
            </div>
            <div class="col-11 ps-3">
              <p class="title">
                {{ task.name }}
              </p>
              <p class="description">
                {{ task.description }}
              </p>
            </div>
          </div>
        
      </div>

    <div class="row">
      <p class="mb-5">
        {{ task.description }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-calendar" />
        <span class="label"> Data de início: </span>
        {{ formatDateBr(task.date_due) }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-calendar" />
        <span class="label"> Prazo final: </span>
        {{ formatDateBr(task.date_start) }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-calendar" />
        <span class="label"> Data de conclusão: </span>
        {{ formatDateBr(task.date_conclusion) }}
      </p>
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
    />
  </div>
</template>

<script>
import axios from "axios";
import { formatDateBr } from "@/utils/date/dateUtils";
import { formatDuration } from "@/utils/date/dateUtils";
import { getStatusClass } from "@/utils/card/cardUtils";
import { getStatusIcon } from "@/utils/card/cardUtils";
import JourneysList from "@/components/lists/JourneysList.vue";

export default {
  name: "TaskShow",
  components: {
    JourneysList,
  },
  data() {
    return {
      journeysData: [],
      task: [],
      taskId: "",
    };
  },
  emits: ["new-journey-event", "journey-updated"],
  methods: {
    formatDateBr,
    formatDuration,
    getStatusClass,
    getStatusIcon,
    getTask() {
      axios
        .get(`http://localhost:8191/api/tasks/${this.taskId}`)
        .then((response) => {
          this.task = response.data.data;
          this.journeysData = this.task.journeys;
          this.taskLoaded = true; // Marque a tarefa como carregada
        })
        .catch((error) => console.log(error));
    },
    setTaskId(taskId) {
      this.taskId = taskId;
    },
    async deleteTask() {
      axios
        .delete(`http://localhost:8191/api/tasks/${this.taskId}`)
        .then((response) => {
          this.data = response.data;
          // this.newTaskEvent(this.data);
          const successMessage = "Task excluído com sucesso";
          this.$router.push({ name: "tasksIndex", query: { successMessage } });
        })
        .catch((error) => {
          console.error("Erro ao deletar task:", error);
          // Lidar com o erro, se necessário
        });
    },
    addJourneyCreated(newJourney) {
      // Add the new journey to the journeysData array
      this.journeysData.push(newJourney);
      this.updateTaskDuration();
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
.label {
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
  padding-right: 0px;
  height: 15vh;
}
.done {
  background-color: var(--verde-claro);
  border-color: var(--verde);
  color: var(--verde);
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
  font-size: 26px;
  font-weight: 900;
  padding-top: 10px;
  padding-bottom: 10px;
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
  color: var(--verde);
}
</style>
