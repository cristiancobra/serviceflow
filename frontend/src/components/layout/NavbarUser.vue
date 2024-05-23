<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">
      <img :src="require('@/assets/logo-serviceflow-ROXO.png')" class="logo" alt="logo-serviceflow" />
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <router-link to="/">
          <li class="nav-item" @mouseover="toggleActive('home')" :class="{ active: activeItem === 'home' }">
            <font-awesome-icon icon="fas fa-home" />
            <span class="router-link-text">Home</span>
          </li>
        </router-link>

        <router-link to="/leads">
          <li class="nav-item" @mouseover="toggleActive('contacts')" :class="{ active: activeItem === 'contacts' }">
            <font-awesome-icon icon="fas fa-user" />
            <span class="router-link-text">Contatos</span>
          </li>
        </router-link>

        <router-link to="/companies">
          <li class="nav-item" @mouseover="toggleActive('companies')" :class="{ active: activeItem === 'companies' }">
            <font-awesome-icon icon="fas fa-briefcase" />
            <span class="router-link-text">Empresas</span>
          </li>
        </router-link>

        <router-link to="/services">
          <li class="nav-item" @mouseover="toggleActive('services')" :class="{ active: activeItem === 'services' }">
            <font-awesome-icon icon="fas fa-coins" />
            <span class="router-link-text">Serviços</span>
          </li>
        </router-link>

        <router-link to="/projects">
          <li class="nav-item" @mouseover="toggleActive('projects')" :class="{ active: activeItem === 'projects' }">
            <font-awesome-icon icon="fas fa-project-diagram" />
            <span class="router-link-text">Projetos</span>
          </li>
        </router-link>

        <router-link to="/tasks">
          <li class="nav-item" @mouseover="toggleActive('tasks')" :class="{ active: activeItem === 'tasks' }">
            <font-awesome-icon icon="fas fa-circle-check" />
            <span class="router-link-text">Tarefas</span>
          </li>
        </router-link>

        <router-link v-if="openJourney" :to="{ name: 'tasksShow', params: { id: openJourney.task_id } }">
          <li class="nav-item" @mouseover="toggleActive('openTask')" :class="{ active: activeItem === 'openTask' }">
          <font-awesome-icon icon="fas fa-exclamation-triangle alert" />
            <span class="router-link-text">Jornada aberta</span>
          </li>
        </router-link>

        <router-link to="/logout">
          <li class="nav-item" @click="logout" @mouseover="toggleActive('logout')"
            :class="{ active: activeItem === 'logout' }">
            <font-awesome-icon icon="fas fa-sign-out" />
            <span class="router-link-text">Logout</span>
          </li>
        </router-link>
      </ul>
    </div>
  </nav>
</template>


<script>
import axios from "axios";
import { BACKEND_URL, LOGOUT_URL, JOURNEY_CHECK_OPEN } from "@/config/apiConfig";

export default {
  data() {
    return {
      activeItem: null,
      openJourney: false,
    };
  },
  created() {
    this.checkOpenJourneys();
  },
  methods: {
    async checkOpenJourneys() {
      try {
        const response = await axios.get(`${BACKEND_URL}${JOURNEY_CHECK_OPEN}`);
        this.openJourney = response.data.openJourney;
        console.log('Jornadas abertas:', this.openJourney);
      } catch (error) {
        console.error('Erro ao verificar jornadas abertas:', error);
      }
    },
    async logout() {
      try {
        axios.defaults.withCredentials = true;

        await axios.post(`${BACKEND_URL}${LOGOUT_URL}`);

        localStorage.removeItem("access_token");

        this.$router.push({ name: "login" });

        // this.$root.isLogged = false;
      } catch (error) {
        console.error("Erro de logout:", error);
      }
    },
    toggleActive(item) {
      const navItem = document.querySelector(".nav-item");

      if (
        !navItem.classList.contains("router-link-active") &&
        !navItem.classList.contains("router-link-exact-active")
      ) {
        this.activeItem = item;
      }
    },
  },
};
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  text-align: center;
  color: #2c3e50;
  background-color: var(--background-light);
}

.alert {
  color: red !important;
  background-color: #fff3cd;
  border-color: #ffeeba;
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid transparent;
  border-radius: 4px;
}


nav {
  padding: 1rem;
}

nav a {
  font-weight: bold;
  color: var(--purple-light);
  text-decoration: none;
}

.nav-item {
  border-width: 2px;
  border-style: none;
  border-radius: 16px 16px 16px 16px;
  padding: 10px 15px 10px 15px;
  margin: 0 4px 0 4px;
  font-weight: 800;
  font-size: 1.2em;
}

.nav-item a {
  text-decoration: none;
  color: var(--purple-light);
}

.nav-item:hover {
  color: var(--purple);
}

.nav-item:hover a {
  color: var(--purple);
}

.nav-item.active:hover {
  border-width: 2px;
  border-style: solid;
  border-radius: 16px 16px 16px 16px;
  border-color: var(--purple);
  padding: 10px 15px 10px 15px;
  margin: 0 4px 0 4px;
  font-weight: 800;
}

.logo {
  width: 170px;
  height: 40px;
}

.router-link-text {
  padding-left: 5px;
}

.nav-item .router-link-text {
  display: none;
  transition: display 0.3s ease;
}

.nav-item:hover .router-link-text {
  display: inline;
}

nav a.router-link-exact-active {
  border-width: 2px;
  border-style: solid;
  border-radius: 16px 16px 16px 16px;
  border-color: var(--purple-light);
}

nav a.router-link-exact-active:hover {
  border-style: none;
}
</style>