<template>
  <div class="tasks-container mb-5 mt-0">
    <div class="row align-items-start">
      <div class="col-1">
        <font-awesome-icon icon="fa-solid fa-tasks" class="icon" />
      </div>
      <div class="col-7">
        <h2 class="title">TAREFAS</h2>
      </div>
      <div class="col-4 text-end">
        <button class="button button-new" @click="toggle()">+</button>
      </div>
    </div>
    <div class="row" v-bind:class="{ 'd-none': isActive }">
      <TaskCreateForm @new-task-event="addTaskCreated" @toogle-task-form=toggle() />
    </div>
    <div class="row">
      <div class="col-12 mb-3 mt-3">
        <input type="text" class="form-control search-container" v-model="searchTerm"
          placeholder="Digite para buscar" />
      </div>
    </div>
    <div class="row">
      <div class="col ps-4 pe-4 pt-0 pb-0" :class="getColumnClass(columns)" v-for="task in filteredTasks"
        v-bind:key="task.id">
        <router-link :to="{ name: 'tasksShow', params: { id: task.id } }">
          <div class="row cards" :class="getCardColor(task.priority, task.status)">
            <div class="col-11 p-2">
              <div class="row" id="card-row-1">
                <div class="col-7 text-start">
                  <p class="cards-title">
                    {{ task.name }}
                  </p>
                </div>
                <div class="col text-end">
                  <DateValue v-model="task.date_due" />
                </div>
              </div>
              <div v-if="task.project" class="row pt-1" id="project-row">
                <div class="col text-start d-flex">
                  <font-awesome-icon icon="fa-solid fa-folder-open" />
                  <p class="cards-project-h2 ps-2">
                    {{ task.project.name }}
                  </p>
                </div>
              </div>
              <div class="row" id="card-row-2">
                <div class="card-icon d-flex mt-1">
                  <div class="me-3">
                    <font-awesome-icon icon="fa-solid fa-clock" class="card-icon" />
                  </div>
                  <div class="default-text me-5">
                    {{ formatDuration(task.duration_time) }}
                  </div>
                  <div class="me-3" v-if="task.duration_days">
                    <font-awesome-icon icon="fa-solid fa-calendar-alt" class="card-icon" />
                  </div>
                  <div class="default-text">
                    {{ task.duration_days }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-1 featured-icon" :class="getPriorityClass(task.priority)">
              <font-awesome-icon :icon="getStatusIcon(task.status)" />
            </div>
          </div>
        </router-link>
        <router-view />
      </div>
    </div>
  </div>
</template>

<script>
// import axios from "axios";
import { convertUtcToLocal, formatDuration } from "@/utils/date/dateUtils";
import { getCardColor, getPriorityClass, getStatusClass, getStatusIcon } from "@/utils/card/cardUtils";
import TaskCreateForm from "@/components/forms/TaskCreateForm.vue";
import DateValue from "../fields/date/DateValue.vue";

export default {
  name: "TasksList",
  props: {
    tasks: Array,
    columns: {
      type: Number,
      default: 1,
    },
  },
  data() {
    return {
      formatedDate: '',
      formatedTime: '',
      isActive: true,
      searchTerm: "",
    };
  },
  components: {
    DateValue,
    TaskCreateForm,
  },
  methods: {
    convertUtcToLocal,
    formatDuration,
    getCardColor,
    getPriorityClass,
    getStatusClass,
    getStatusIcon,
    addTaskCreated(newTask) {
      this.toggle();
      this.filteredTasks.unshift(newTask);
    },
    formatDateDue(date) {
      if (date !== '1969-12-31 18:00:00' && date !== '1969-12-31 21:00:00') {
        return this.convertUtcToLocal(date);
      }
      return 'sem prazo';
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
      // Defina sua lógica para determinar as classes com base em status e prioridade
      const statusClass = getPriorityClass(status);
      const priorityClass = getPriorityClass(priority);

      // Combine as classes
      return `${statusClass} ${priorityClass}`;
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
};
</script>

<style scoped>
a {
  text-decoration: none;
}

.icon {
  font-size: 1.8rem;
  font-weight: 400;
  color: var(--gray);
}

.tasks-container {
  border-style: solid;
  border-width: 2px;
  border-color: var(--primary);
  border-radius: 14px;
  padding: 1rem;
}

.title {
  text-align: left;
}
</style>
