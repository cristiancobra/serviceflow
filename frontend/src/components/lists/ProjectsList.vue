<template>
  <div>
    <div class="row">
      <div class="col-12 mb-3">
        <input type="text" class="form-control search-container" v-model="searchTerm"
          placeholder="Digite para buscar" />
      </div>
    </div>
    <div class="row">
      <div class="col-12 g-5" v-for="project in filteredProjects" v-bind:key="project.id">
        <div class="card" :class="getCombinedClasses(project.status, project.priority)">
          <router-link :to="{ name: 'projectShow', params: { id: project.id } }">
            <div class="row ms-1">
              <div class="row">
                <div class="col-10">
                  <p class="card-title">
                    {{ project.name }}
                  </p>
                  <p v-html="trimDescription(project.description)" class="description"></p>
                </div>
                <div class="col-2 status" :class="getStatusClass(project.status)">
                  <font-awesome-icon :icon="getStatusIcon(project.status)" />
                </div>
              </div>
              <div class="icon" :class="getStatusClass(project.status)">
                <div class="me-3">
                  <font-awesome-icon icon="fa-solid fa-clock" style="color: rgb(48, 48, 48)" />
                </div>
                <div class="me-5">
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
  props: ["projects"],
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
    console.log("projects", this.projects);
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.card-title {
  text-align: left;
  font-size: 1.1rem;
  font-weight: 800;
  margin: 5px;
  margin-top: 10px;
  color: black;
}

.description {
  text-align: left;
  font-size: 14px;
  margin-top: 20px;
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

.card {
  border-style: solid;
  border-width: 2px;
  border-color: gray;
  border-radius: 6px;
  padding: 10px;
  padding-right: 0px;
  min-height: 15vh;
  position: relative;
  overflow: hidden;
}

.card:hover {
  border-width: 5px;
  border-radius: 14px;
}

.card::before {
  content: "";
  position: absolute;
  top: auto;
  bottom: 0;
  left: auto;
  /* Define left como auto */
  right: -24%;
  width: 60%;
  height: 14%;
  transform: scaleX(-1) translateY(100%) rotate(45deg);
  /* Rotação de 45 graus */
  background-color: white;
  z-index: 1;
  /* Colocar a linha atrás do quadrado */
}

.card.low::before {
  background-color: var(--gray);
}

.card.medium::before {
  background-color: var(--blue);
}

.card.high::before {
  background-color: var(--red);
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

.canceled {
  background-color: var(--gray-light);
  border-color: var(--gray);
  color: var(--gray);
}

.wait {
  background-color: var(--gray-light);
  border-color: var(--gray);
  color: var(--gray);
}

.status {
  display: flex;
  font-size: 3rem;
}

.icon {
  display: flex;
  justify-content: start;
  align-content: flex-start;
  font-size: 1.2rem;
  margin-top: 10px;
}

.label {
  margin-top: 0px;
  margin-bottom: -0px;
  font-size: 1.2rem;
  font-weight: 800;
}

.search-container {
  border-width: 2px;
  border-style: solid;
  border-color: lightgray;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  margin: 0 4px 0 4px;
  color: black;
}
</style>
