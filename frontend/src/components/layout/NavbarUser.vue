<template>
  <div class="sticky-top">
    <nav class="navbar navbar-expand-lg ">
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
              <font-awesome-icon icon="fas fa-calendar" />
              <span class="router-link-text">AGENDA</span>
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

          <router-link to="/opportunities">
            <li class="nav-item" @mouseover="toggleActive('opportunities')"
              :class="{ active: activeItem === 'opportunities' }">
              <font-awesome-icon icon="fas fa-bullseye" />
              <span class="router-link-text">OPORTUNIDADES</span>
            </li>
          </router-link>

          <router-link to="/proposals">
            <li class="nav-item" @mouseover="toggleActive('proposals')" :class="{ active: activeItem === 'proposals' }">
              <font-awesome-icon icon="fas fa-file-invoice-dollar" />
              <span class="router-link-text">PROPOSTAS</span>
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

          <li class="nav-item" @mouseover="showSubmenu = true" @mouseleave="showSubmenu = false"
            :class="{ active: activeItem === 'configuracoes' }">
            <font-awesome-icon icon="fas fa-cogs" />
            <span class="router-link-text">CONFIGURAÇÕES</span>
            <ul class="submenu" v-show="showSubmenu">
              <router-link :to="`/accounts/${accountId}`">
                <li class="nav-item" @mouseover="toggleActive('account')" :class="{ active: activeItem === 'account' }">
                  <font-awesome-icon icon="fas fa-user" />
                  <span class="router-link-text">CONTA</span>
                </li>
              </router-link>
              <router-link to="/services">
                <li class="nav-item" @mouseover="toggleActive('services')"
                  :class="{ active: activeItem === 'services' }">
                  <font-awesome-icon icon="fas fa-coins" />
                  <span class="router-link-text">SERVIÇOS</span>
                </li>
              </router-link>
              <router-link to="/costs">
                <li class="nav-item" @mouseover="toggleActive('costs')" :class="{ active: activeItem === 'costs' }">
                  <font-awesome-icon icon="fas fa-dollar-sign" />
                  <span class="router-link-text">CUSTOS</span>
                </li>
              </router-link>
            </ul>
          </li>
          <router-link to="/logout">
            <li class="nav-item" @click="logout" @mouseover="toggleActive('submitLogout')"
              :class="{ active: activeItem === 'logout' }">
              <font-awesome-icon icon="fas fa-sign-out" />
              <span class="router-link-text">SAIR</span>
            </li>
          </router-link>
        </ul>
        <div class="navbar-user-menu-container">
          <navbar-user-menu />
          <div class="play-container">
            <font-awesome-icon v-if="openJourney" icon="fas fa-play" class="play" />
            <font-awesome-icon v-else icon="fas fa-pause" class="off" />
          </div>
        </div>
      </div>
    </nav>
  </div>
</template>


<script>
import { mapActions } from 'vuex';
import { mapState } from 'vuex';
import NavbarUserMenu from './NavbarUserMenu.vue';

export default {
  data() {
    return {
      activeItem: null,
      showSubmenu: false,
    };
  },
  components: {
    NavbarUserMenu,
  },
  methods: {
    ...mapActions(['logout']),
    async submitLogout() {
      await this.logout();
      this.toggleActive('logout');
    },
    toggleActive(item) {
      this.activeItem = item;
    },
  },
  computed: {
    ...mapState([
      'accountId',
      'openJourney',
    ]),
  },
  mounted() {
    console.log('openJourney', this.openJourney);
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
  font-size: 0.9rem;
  color: white;
  list-style-type: none;

}

.nav-item a {
  text-decoration: none;
  color: white;
}

.nav-item:hover {
  /* color: var(--purple); */
}

.nav-item:hover a {}

.nav-item.active:hover {
  border-width: 1px;
  border-style: solid;
  border-radius: 16px 16px 16px 16px;
  border-color: white;
  padding: 4px 15px 4px 15px;
  margin: 0 4px 0 4px;
}

.navbar-user-menu-container {
  position: relative;
}

.off {
  font-size: 1.0rem;
  z-index: 1;
  color: var(--purple);
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
  color: white;
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

.submenu {
  display: none;
  position: absolute;
  background-color: var(--primary);
}

.nav-item:hover .submenu {
  display: block;
}

.play-container {
  position: absolute;
  top: 32px; /* Ajuste este valor conforme necessário */
  left: 42%;
  width: 22px;
  height: 22px; /* Adicione altura para centralizar verticalmente */
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  pointer-events: none; 
  background-color: white;
}

.play {
  font-size: 0.9rem;
  padding-left: 0.1rem;
  z-index: 1;
  animation: colorChange 2s infinite;
}

@keyframes colorChange {
  0% {
    color: var(--green);
  }
  50% {
    color: var(--green-light);
  }
  100% {
    color: var(--green);
  }
}

.task-doing {
  padding: 0.4rem 2rem;
  text-align: right;
  font-size: 0.8rem;
  font-weight: 800;
}
</style>