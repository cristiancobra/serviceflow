<template>
  <div class="container mb-5">
    <div class="headers-line">
      <div class="col-1 slot done">concluidos</div>
      <div class="col-1 slot doing">andamento</div>
      <div class="col-1 slot late">atrasados</div>
      <div class="col-1 slot new" @click="toggle()">+</div>
    </div>

    <div v-bind:class="{ hidden: isActive }">
      <ProjectCreateForm @new-project-event="addProjectCreated($event)" @toogle-task-form=toggle() />
    </div>

    <div class="row">
      <ProjectsList :projects="filteredProjects" :columns="2" />
    </div>
  </div>
</template>

<script>
import { BACKEND_URL, PROJECT_URL } from "@/config/apiConfig";
import axios from "axios";
import ProjectsList from "@/components/lists/ProjectsList.vue";
import ProjectCreateForm from "@/components/forms/ProjectCreateForm.vue";

export default {
  name: "ProjectsIndex",
  components: {
    ProjectCreateForm,
    ProjectsList,
  },
  data() {
    return {
      isActive: true,
      hasError: false,
      data: null,
      filteredProjects: [],
      projects: [],
      newProject: {
        id: null,
        name: null,
        description: null,
        company_id: null,
        contact_id: null,
        user_id: null,
        date_start: null,
        date_due: null,
      },
    };
  },
  methods: {
    toggle() {
      setTimeout(() => {
        this.isActive = !this.isActive;
      }, 100);
    },
    getProjects() {
      axios
        .get(`${BACKEND_URL}${PROJECT_URL}`)
        .then((response) => {
          this.projects = response.data.data;
          this.filteredProjects = this.projects;
          console.log("projects2", this.projects);
        })
        .catch((error) => console.log(error));
    },
    addProjectCreated(newProject) {
      this.toggle();
      this.filteredProjects.unshift(newProject);
    },
  },
  mounted() {
    this.getProjects();
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

.done {
  background-color: white;
  border-color: #2cb48d;
  color: #2cb48d;
}

.done:hover {
  background-color: #2cb48d;
  color: white;
}

.doing {
  background-color: white;
  border-color: #e78d1f;
  color: #e78d1f;
}

.doing:hover {
  background-color: #e78d1f;
  color: white;
}

.late {
  background-color: white;
  border-color: #b1388d;
  color: #b1388d;
}

.late:hover {
  background-color: #b1388d;
  color: white;
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

.projects-container {
  margin-left: 180px;
  margin-right: 180px;
  margin-bottom: 60px;
}

.hidden {
  display: none;
  transition: display 8s;
}

.show {
  display: block;
  transition: display 2s;
}
</style>
