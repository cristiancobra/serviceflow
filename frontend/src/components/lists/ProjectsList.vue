<template>
  <div class="projects-container">
    <div class="row align-items-start">
      <div class="col-1">
        <font-awesome-icon icon="fa-solid fa-project-diagram" class="icon" />
      </div>
      <div class="col-7">
        <h2 class="title">PROJETOS</h2>
      </div>
      <div class="col-4 text-end">
        <button class="button button-new" @click="toggle()">+</button>
      </div>
    </div>
    <div class="row" v-bind:class="{ 'd-none': isActive }">
      <TaskCreateForm @new-task-event="addTaskCreated" @toogle-task-form=toggle() />
    </div>
    <div class="row" v-for="project in filteredProjects" v-bind:key="project.id">
      <div class="col-1 d-flex align-items-center justify-content-center" id="col-user">
        <font-awesome-icon icon="fa-solid fa-folder-open" class="primary big-icon" />
      </div>
      <div v-if="project.date_conclusion" class="col-2 status done">
        <font-awesome-icon icon="fas fa-check-circle" style="font-size: 2rem;" class="done mb-3" />
        <DateTimeValue v-model="project.date_conclusion" :classText="getDeadlineClass(project)"
          :classIcon='getDeadlineClass(project)' @save="updateTask('date_conclusion', $event, project.id)" />
      </div>
      <div v-else class="col-2 status canceled">
        <font-awesome-icon icon="fas fa-check-circle" style="font-size: 2rem;" class="canceled" />
      </div>
      <div class="col cards">
        <router-link :to="{ name: 'projectShow', params: { id: project.id } }">
          <div class="row title">
            <div class="col">
              <p class="cards-title">
                {{ project.name }}
              </p>
            </div>
            <div class="col-3 pt-2">
              <DateTimeValue v-model="project.date_due" :classText="project.date_conclusion ? 'canceled' : ''"
                :classIcon="project.date_conclusion ? 'canceled' : ''" />
            </div>
          </div>
        </router-link>
      </div>
      <div v-if="project.date_conclusion" class="col-3 line-list status done">
        <font-awesome-icon icon="fas fa-check-circle" style="font-size: 2rem;" class="done" />
        <DateTimeEditableInput v-model="project.date_conclusion" :classText="getDeadlineClass(project)" classIcon='default-text'
          @save="updateTask('date_conclusion', $event, project.id)" />
      </div>
      <div v-else class="col-3 line-list status canceled">
        <font-awesome-icon icon="fas fa-check-circle" style="font-size: 2rem;" class="canceled" />
        <DateTimeEditableInput v-model="project.date_due" :classText="getDeadlineClass(project)" classIcon='default-text'
          @save="updateTask('date_conclusion', $event, project.id)" />
      </div>
    </div>
  </div>
</template>

<script>
// import axios from "axios";
import { formatDuration } from "@/utils/date/dateUtils";
import { getDeadlineClass, getStatusClass, getPriorityClass, getStatusColor, getStatusIcon } from "@/utils/card/cardUtils";

export default {
  name: "ProjectsList",
  props: {
    projects: Array,
    columns: {
      type: Number,
      default: 1,
    },
  },
  data() {
    return {
      searchTerm: "",
    };
  },
  methods: {
    formatDuration,
    getDeadlineClass,
    getStatusClass,
    getStatusColor,
    getPriorityClass,
    getStatusIcon,
    getCombinedClasses(status, priority) {
      // Defina sua lógica para determinar as classes com base em status e prioridade
      const statusClass = getStatusClass(status);
      const priorityClass = getPriorityClass(priority);

      // Combine as classes
      return `${statusClass} ${priorityClass}`;
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
  },
  computed: {
    filteredProjects() {
      if (this.searchTerm === "") {
        return this.projects;
      } else {
        const lowerSearchTerm = this.searchTerm.toLowerCase();
        return this.projects.filter((project) => {
          return (
            project.name.toLowerCase().includes(lowerSearchTerm) ||
            (project.description &&
              project.description.toLowerCase().includes(lowerSearchTerm))
          );
        });
      }
    },
  },
};
</script>

<style scoped>
.icon {
  font-size: 1.8rem;
  font-weight: 400;
  color: var(--gray);
}

.projects-container {
  border-style: solid;
  border-width: 2px;
  border-color: var(--orange);
  border-radius: 14px;
  padding: 1rem;
}

.title {
  text-align: left;
}
</style>