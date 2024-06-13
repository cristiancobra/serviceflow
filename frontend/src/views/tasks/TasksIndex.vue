<template>
  <div class="container mb-5">
    <TasksFilter
      @toggle="toggle"
      @filter-canceled="getTasksCanceled"
      @filter-doing="getTasksDoing"
      @filter-done="getTasksDone"
      @filter-late="getTasksLate"
      @filter-to-do="getTasksToDo"
    />

    <div v-bind:class="{ 'd-none': isActive }">
      <TaskCreateForm
      @new-task-event="addTaskCreated"
      @toogle-task-form=toggle()
       />
    </div>

    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <div class="row">
      <TasksList :tasks="filteredTasks" :columns="2" />
    </div>
  </div>
</template>

<script>
import { BACKEND_URL, TASK_URL, TASK_URL_PARAMETER } from "@/config/apiConfig";
import axios from "axios";
import TasksList from "@/components/lists/TasksList.vue";
import TaskCreateForm from "@/components/forms/TaskCreateForm.vue";
import TasksFilter from "@/components/filters/TasksFilter.vue";
import SuccessMessage from '../../components/forms/messages/SuccessMessage.vue';

export default {
  name: "TasksIndexView",
  components: {
    TaskCreateForm,
    TasksList,
    TasksFilter,
    SuccessMessage,
  },
  data() {
    return {
      isActive: true,
      isError: false,
      isSuccess: false,
      hasError: false,
      data: null,
      tasks: [],
      filteredTasks: [],
      newTask: null,
    };
  },
  methods: {
    toggle() {
      this.isActive = !this.isActive;
    },
    getTasks() {
      axios
      .get(`${BACKEND_URL}${TASK_URL}`)
        .then((response) => {
          this.tasks = response.data.data;
          this.filteredTasks = this.tasks;
        })
        .catch((error) => console.log(error));
    },
    addTaskCreated(newTask) {
      this.toggle();
      this.filteredTasks.unshift(newTask);
    },
    getTasksCanceled() {
      axios
        .get(`${BACKEND_URL}${TASK_URL_PARAMETER}filter-status?status=canceled`) // Faz a requisição filtrando por status "done"
        .then((response) => {
          this.filteredTasks = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    getTasksDoing() {
      axios
      .get(`${BACKEND_URL}${TASK_URL_PARAMETER}filter-status?status=doing`) // Faz a requisição filtrando por status "doing"
        .then((response) => {
          this.filteredTasks = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    getTasksDone() {
      axios
      .get(`${BACKEND_URL}${TASK_URL_PARAMETER}filter-status?status=done`) // Faz a requisição filtrando por status "done"
        .then((response) => {
          this.filteredTasks = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    getTasksLate() {
      axios
      .get(`${BACKEND_URL}${TASK_URL_PARAMETER}filter-date`) // Faz a requisição filtrando por status "late"
        .then((response) => {
          this.filteredTasks = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    getTasksToDo() {
      axios
      .get(`${BACKEND_URL}${TASK_URL_PARAMETER}filter-status?status=to-do`) // Faz a requisição filtrando por status "to-do"
        .then((response) => {
          this.filteredTasks = response.data.data;
        })
        .catch((error) => console.log(error));
    },
  },
  mounted() {
    this.getTasks();
  },
};
</script>

<style scoped>
.headers-line {
  margin-top: 40px;
  margin-bottom: 50px;
  margin-left: 80px;
  margin-right: 80px;
  display: flex;
  justify-content: center;
}
.slot {
  border-width: 2px;
  border-style: solid;
  border-color: white;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  margin: 0 4px 0 4px;
  color: white;
  font-weight: 800;
  width: 120px;
}
.new {
  border-radius: 20px 20px 20px 20px;
  background-color: white;
  border-color: #ff3eb5;
  color: #ff3eb5;
  margin-left: 50px;
  width: 60px;
  font-size: 16px;
}
.new:hover {
  background-color: #ff3eb5;
  color: white;
  margin-left: 50px;
  width: 60px;
}
.show {
  display: block;
  transition: display 2s;
}
</style>
