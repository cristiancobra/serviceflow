<template>
  <div class="list-container mb-5 mt-0">
    <div class="row align-items-start">
      <div class="col-1">
        <font-awesome-icon icon="fa-solid fa-tasks" class="icon" />
      </div>
      <div class="col-8">
        <h2 class="title">TAREFAS</h2>
      </div>
      <div class="col-3 d-flex justify-content-end">
        <TaskCreateForm @new-task-event="addTaskCreated" />
      </div>
    </div>
    <div class="row">
      <div class="col-12 mb-3 mt-3">
        <input type="text" class="form-control search-container" v-model="searchTerm"
          placeholder="Digite para buscar" />
      </div>
    </div>
    <div class="row list-line" v-for="task in filteredTasks" v-bind:key="task.id">
      <div class="col-9 d-flex">
        <router-link :to="{ name: 'tasksShow', params: { id: task.id } }">
          <div class="d-flex">
            <font-awesome-icon icon="fa-solid fa-user" class="primary pe-2" />
            <font-awesome-icon icon="fas fa-check-circle" class="pe-2"
              :class="isValidDate(task.date_conclusion) ? 'done' : 'canceled'" />
            <p class="cards-title">
              {{ task.name }}
            </p>
            <div v-if="task.project" class="project m-0 p-1 ps-2 pe-2 ms-2">
              <font-awesome-icon icon="fa-solid fa-folder-open" />
              <p class="m-0 p-0 ps-1">
                {{ task.project.name }}
              </p>
            </div>
            <div v-if="task.opportunity" class="opportunity m-0 p-1 ps-2 pe-2 ms-2">
              <font-awesome-icon icon="fa-solid fa-bullseye" />
              <p class="m-0 p-0 ps-1">
                {{ task.opportunity.name }}
              </p>
              </div>
            </div>
        </router-link>
      </div>
      <div class="col-3">
        <DateTimeValue v-if="isValidDate(task.date_conclusion)" v-model="task.date_conclusion" classText="done"
          classIcon='done' @save="updateTask('date_conclusion', $event, task.id)" />
        <DateTimeEditableInput v-else v-model="task.date_due" :classText="getDeadlineClass(task)"
          :classIcon="getDeadlineClass(task)" @save="updateTask('date_due', $event, task.id)" />
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { convertUtcToLocal, formatDuration } from "@/utils/date/dateUtils";
import { getStatusColor, getPriorityClass, getDeadlineClass, getStatusIcon } from "@/utils/card/cardUtils";
import { BACKEND_URL, TASK_URL_PARAMETER, TASK_PRIORIZED_URL, TASK_BY_PROJECT_URL, TASK_BY_OPPORTUNITY_URL } from "@/config/apiConfig";
import { fetchIndexData } from "@/utils/requests/httpUtils";
import TaskCreateForm from "@/components/forms/TaskCreateForm.vue";
import DateTimeEditableInput from "../fields/datetime/DateTimeEditableInput.vue";
import DateTimeValue from "../fields/datetime/DateTimeValue.vue";

export default {
  name: "TasksList",
  props: {
    opportunityId: {
      type: Number,
      required: false,
    },
    projectId: {
      type: Number,
      required: false,
    },
    template: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      formatedDate: '',
      formatedTime: '',
      isActive: true,
      searchTerm: "",
      tasks: [],
    };
  },
  components: {
    DateTimeEditableInput,
    DateTimeValue,
    TaskCreateForm,
  },
  methods: {
    convertUtcToLocal,
    formatDuration,
    getStatusColor,
    getPriorityClass,
    getDeadlineClass,
    getStatusIcon,
    addTaskCreated(newTask) {
      this.toggle();
      this.tasks.unshift(newTask);
    },
    formatDateDue(date) {
      if (date === '1969-12-31 18:00:00' && date === '1969-12-31 21:00:00' && date === '1970-01-01 00:00:00') {
        return "";
      }
    },
    trimDescription(description) {
      if (description) {
        return description.substring(0, 110);
      }
    },
    getColumnClass(columns) {
      switch (columns) {
        case 1:
          return "col-12 g-4";
        case 2:
          return "col-6 g-4";
        default:
          return "col-12";
      }
    },
    getCombinedClasses(status, priority) {
      const statusClass = getPriorityClass(status);
      const priorityClass = getPriorityClass(priority);

      return `${statusClass} ${priorityClass}`;
    },
    async getTasks() {
      try {
        this.tasks = await fetchIndexData(`tasks`);
        console.log("Tarefas:", this.tasks);
      } catch (error) {
        console.error("Erro ao acessar tarefas:", error);
      }
    },
    async getTasksFromProject(page = 1) {

      this.tasksUrl = `${BACKEND_URL}${TASK_BY_PROJECT_URL}project_id=${this.projectId}&per_page=10&page=${page}`;

      try {
        const response = await axios.get(this.tasksUrl);

        this.tasks = response.data.data.map(task => {
          return { ...task, editing: false }; // Adiciona a propriedade editing a cada task
        });

        this.paginationData = {
          links: response.data.links,
          meta: response.data.meta,
        };

      } catch (error) {
        console.error("Erro ao acessar tarefas:", error);
      }
    },
    async getTasksFromOpportunity(page = 1) {

      this.tasksUrl = `${BACKEND_URL}${TASK_BY_OPPORTUNITY_URL}opportunity_id=${this.opportunityId}&per_page=10&page=${page}`;

      try {
        const response = await axios.get(this.tasksUrl);

        this.tasks = response.data.data.map(task => {
          return { ...task, editing: false }; // Adiciona a propriedade editing a cada task
        });

        this.paginationData = {
          links: response.data.links,
          meta: response.data.meta,
        };

      } catch (error) {
        console.error("Erro ao acessar tarefas:", error);
      }
    },
    getTasksPriorized() {
      axios
        .get(`${BACKEND_URL}${TASK_PRIORIZED_URL}`)
        .then((response) => {
          this.tasks = response.data.data;
          // this.filteredTasks = this.tasks; // Inicialmente, as tarefas filtradas são iguais a todas as tarefas
        })
        .catch((error) => console.log(error));
    },
    isValidDate(date) {
      if (date != '1969-12-31 18:00:00'
        && date != '1969-12-31 21:00:00'
        && date != '1970-01-01 00:00:00'
        && date != null
      ) {
        return true;
      }
    },
    async updateTask(fieldName, editedValue, taskId) {
      const updatedField = {};
      updatedField[fieldName] = editedValue;

      try {
        const response = await axios.put(
          `${BACKEND_URL}${TASK_URL_PARAMETER}${taskId}`,
          updatedField
        );

        const updatedTask = response.data.data;

        this.updateTasksList(updatedTask, taskId);

      } catch (error) {
        console.error("Erro ao atualizar a tarefa:", error);
      }

    },
    updateTasksList(updatedTask, taskId) {
      const index = this.tasks.findIndex(task => task.id === taskId);
      this.tasks.splice(index, 1, updatedTask);
    },
    toggle() {
      this.isActive = !this.isActive;
    },
  },
  computed: {
    filteredTasks() {
      if (this.searchTerm === "") {
        return this.tasks;
      } else {
        const lowerSearchTerm = this.searchTerm.toLowerCase();
        return this.tasks.filter((task) => {
          return (
            task.name.toLowerCase().includes(lowerSearchTerm) ||
            (task.description &&
              task.description.toLowerCase().includes(lowerSearchTerm))
          );
        });
      }
    },
  },
  mounted() {
    if (this.template === 'priorized') {
      this.getTasksPriorized();
    }

    if (this.template === 'index') {
      this.getTasks();
    }

    if (this.template === 'project') {
      this.getTasksFromProject();
    }

    if (this.template === 'opportunity') {
      this.getTasksFromOpportunity();
    }
  },
};
</script>

<style scoped>
a {
  text-decoration: none;
}

.small-date {
  font-size: 0.8rem;
  font-weight: 400;
}
</style>
