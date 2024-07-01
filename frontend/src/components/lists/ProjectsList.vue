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
      <ProjectCreateForm @new-project-event="addProjectCreated" @toogle-project-form=toggle() />
    </div>
    <div class="row">
      <div class="col-12 mb-3 mt-3">
        <input type="text" class="form-control search-container" v-model="searchTerm"
          placeholder="Digite para buscar" />
      </div>
    </div>
    <div class="row" v-for="project in filteredProjects" v-bind:key="project.id">
      <div class="col-1 d-flex align-items-center justify-content-center" id="col-user">
        <font-awesome-icon icon="fa-solid fa-folder-open" class="primary big-icon" />
      </div>
      <div v-if="project.date_conclusion" class="col-1 status done">
        <font-awesome-icon icon="fas fa-check-circle" style="font-size: 2rem;" class="done mb-3" />
      </div>
      <div v-else class="col-1 status canceled">
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
          </div>
        </router-link>
      </div>
      <div class="col-3 line-list d-flex align-items-center justify-content-center">
        <DateTimeValue v-if="project.date_conclusion" v-model="project.date_conclusion" classText="done"
          classIcon='done' @save="updateProject('date_conclusion', $event, project.id)" />
        <DateTimeEditableInput v-else v-model="project.date_due" :classText="getDeadlineClass(project)"
          :classIcon='getDeadlineClass(project)' @save="updateProject('date_due', $event, project.id)" />
      </div>
    </div>
  </div>
</template>

<script>
import { formatDuration } from "@/utils/date/dateUtils";
import { getDeadlineClass, getStatusClass, getPriorityClass, getStatusColor, getStatusIcon } from "@/utils/card/cardUtils";
import { BACKEND_URL, PROJECT_URL, PROJECT_URL_PARAMETER, PROJECTS_PRIORIZED_URL } from "@/config/apiConfig";
import axios from "axios";
import DateTimeEditableInput from "../fields/datetime/DateTimeEditableInput.vue";
import DateTimeValue from "../fields/datetime/DateTimeValue.vue";

export default {
  name: "ProjectsList",
  components: {
    DateTimeEditableInput,
    DateTimeValue,
  },
  props: {
    columns: {
      type: Number,
      default: 1,
    },
    template: {
      type: String,
    }
  },
  data() {
    return {
      projects: [],
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
    addProjectCreated(newProject) {
      this.projects.unshift(newProject);
    },
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
    getProjectsPriorized() {
      axios
        .get(`${BACKEND_URL}${PROJECTS_PRIORIZED_URL}`)
        .then((response) => {
          this.projects = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    async updateProject(fieldName, editedValue, projectId) {
      const updatedField = {};
      updatedField[fieldName] = editedValue;
      try {
        const response = await axios.put(
          `${BACKEND_URL}${PROJECT_URL_PARAMETER}${projectId}`,
          updatedField
        );
        const updatedProject = response.data.data;
        this.updateProjectsList(updatedProject, projectId);
        console.log("updatedProjec", updatedProject)
      } catch (error) {
        console.error("Erro ao atualizar a tarefa:", error);
      }
    },
    getProjects() {
      axios
        .get(`${BACKEND_URL}${PROJECT_URL}`)
        .then((response) => {
          this.projects = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    toggle() {
      this.isActive = !this.isActive;
    },
    updateProjectsList(updatedProject, projectId) {
      const index = this.projects.findIndex(project => project.id === projectId);
      this.projects.splice(index, 1, updatedProject);
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
  mounted() {
    if (this.template === 'priorized') {
      this.getProjectsPriorized();
    }

    if (this.template === 'index') {
      this.getProjects();
    }

  }
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