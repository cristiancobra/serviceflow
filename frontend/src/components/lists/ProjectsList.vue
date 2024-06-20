<template>
  <div>
    <div class="row">
      <div class="col-12 mb-3">
        <input type="text" class="form-control search-container" v-model="searchTerm"
          placeholder="Digite para buscar" />
      </div>
    </div>
    <div class="row">
      <div :class="getColumnClass(columns)" v-for="project in filteredProjects" v-bind:key="project.id">
        <div class="cards p-0" :class="getStatusClass(project.status)">
          <router-link :to="{ name: 'projectShow', params: { id: project.id } }">
            <div class="row">
              <div class="col-11">
                <div class="row m-2">
                  <p class="cards-project-title" :class="getStatusClass(project.status)">
                    {{ project.name }}
                  </p>
                </div>
                <div class="row">
                  <p v-html="trimDescription(project.description)" class="description"></p>
                </div>
                <div class="row m-2">
                  <div class="icon" :class="getStatusClass(project.status)">
                    <div class="me-3">
                      <font-awesome-icon icon="fa-solid fa-clock" style="color: rgb(48, 48, 48)" />
                    </div>
                    <div class="me-3">
                      {{ formatDuration(project.duration_time) }}
                    </div>
                    <div class="me-3" v-if="project.duration_days">
                      <font-awesome-icon icon="fa-solid fa-calendar-alt" style="color: rgb(48, 48, 48)" />
                    </div>
                    <div class="">
                      {{ project.duration_days }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-1 big-icon" :class="getPriorityClass(project.priority)">
                <font-awesome-icon :icon="getStatusIcon(project.status)" />
              </div>
            </div>
          </router-link>
        </div>
        <router-view />
      </div>
    </div>
  </div>
</template>

<script>
// import axios from "axios";
import { formatDuration } from "@/utils/date/dateUtils";
import { getStatusClass } from "@/utils/card/cardUtils";
import { getPriorityClass } from "@/utils/card/cardUtils";
import { getStatusIcon } from "@/utils/card/cardUtils";

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
    getStatusClass,
    getPriorityClass,
    getStatusIcon,
    trimDescription(description) {
      if (description) {
        return description.substring(0, 110);
      }
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