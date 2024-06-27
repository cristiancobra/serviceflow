<template>
  <nav class="navbar navbar-expand-lg sticky-top">
    <a class="navbar-brand" href="#">
      <img :src="require('@/assets/logo-serviceflow-BRANCO.png')" class="logo" alt="logo-serviceflow" />
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
            <span class="router-link-text">PAINEL</span>
          </li>
        </router-link>

        <router-link to="/leads">
          <li class="nav-item" @mouseover="toggleActive('contacts')" :class="{ active: activeItem === 'contacts' }">
            <font-awesome-icon icon="fas fa-user" />
            <span class="router-link-text">CONTATOS</span>
          </li>
        </router-link>

        <router-link to="/companies">
          <li class="nav-item" @mouseover="toggleActive('companies')" :class="{ active: activeItem === 'companies' }">
            <font-awesome-icon icon="fas fa-briefcase" />
            <span class="router-link-text">EMPRESAS</span>
          </li>
        </router-link>

        <router-link to="/services">
          <li class="nav-item" @mouseover="toggleActive('services')" :class="{ active: activeItem === 'services' }">
            <font-awesome-icon icon="fas fa-coins" />
            <span class="router-link-text">SERVIÇOS</span>
          </li>
        </router-link>

        <router-link to="/projects">
          <li class="nav-item" @mouseover="toggleActive('projects')" :class="{ active: activeItem === 'projects' }">
            <font-awesome-icon icon="fas fa-project-diagram" />
            <span class="router-link-text">PROJETOS</span>
          </li>
        </router-link>

        <router-link to="/tasks">
          <li class="nav-item" @mouseover="toggleActive('tasks')" :class="{ active: activeItem === 'tasks' }">
            <font-awesome-icon icon="fas fa-tasks" />
            <span class="router-link-text">TAREFAS</span>
          </li>
        </router-link>

        <router-link to="/journeys">
          <li class="nav-item" @mouseover="toggleActive('journeys')" :class="{ active: activeItem === 'journeys' }">
            <font-awesome-icon icon="fas fa-clock" />
            <span class="router-link-text">JORNADAS</span>
          </li>
        </router-link>

        <router-link v-if="openJourney" :to="{ name: 'tasksShow', params: { id: openJourney.task_id } }">
          <li class="nav-item" @mouseover="toggleActive('openTask')" :class="{ active: activeItem === 'openTask' }">
          <font-awesome-icon icon="fas fa-exclamation-triangle alert" />
            <span class="router-link-text">FAZENDO</span>
          </li>
        </router-link>

        <router-link to="/logout">
          <li class="nav-item" @click="logout" @mouseover="toggleActive('logout')"
            :class="{ active: activeItem === 'logout' }">
            <font-awesome-icon icon="fas fa-sign-out" />
            <span class="router-link-text">SAIR</span>
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
  color: white;
  text-decoration: none;
}

.navbar {
  background-color: var(--primary);
  /* background-image: linear-gradient(to bottom, var(--primary), rgba(255, 255, 255, 0.4)); */
}

.nav-item {
  border-width: 2px;
  border-style: none;
  border-radius: 16px 16px 16px 16px;
  padding: 4px 15px 4px 15px;
  margin: 0 4px 0 4px;
  font-weight: 200;
  font-size: 1rem;
}

.nav-item a {
  text-decoration: none;
  color: white;
}

.nav-item:hover {
  /* color: var(--purple); */
}

.nav-item:hover a {
  
}

.nav-item.active:hover {
  border-width: 1px;
  border-style: solid;
  border-radius: 16px 16px 16px 16px;
  border-color: white;
  padding: 4px 15px 4px 15px;
  margin: 0 4px 0 4px;
}

.logo {
  margin-left: 2rem;
  width: 146px;
  height: 30px;
}

.router-link-text {
  padding-left: 5px;
}

.nav-item .router-link-text {
  /* display: none; */
  transition: display 0.3s ease;
}

.nav-item:hover .router-link-text {
  display: inline;
}

nav a.router-link-exact-active {
  border-width: 1px;
  border-style: solid;
  border-radius: 16px 16px 16px 16px;
  border-color: white;
}

nav a.router-link-exact-active:hover {
  border-style: none;
}
</style>