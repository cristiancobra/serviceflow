<template>
  <div class="navbar-container">
    <nav class="navbar">
      <a class="navbar-brand" href="#">
        <img :src="require('@/assets/logo-serviceflow-BRANCO.png')" class="logo" alt="logo-serviceflow" />
      </a>
      <button class="navbar-toggler" :class="{ open: isNavbarOpen }" @click="toggleNavbar"> <!-- Adiciona a classe condicional 'open' -->
        <span class="navbar-toggler-icon"></span>
      </button>

      <div :class="['navbar-collapse', { 'show': isNavbarOpen }]">
        <ul class="navbar-nav">
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


          <li class="nav-item" @mouseover="showSubmenu('financeiro')" @mouseleave="hideSubmenu('financeiro')"
            :class="{ active: activeItem === 'financeiro' }">
            <font-awesome-icon icon="fas fa-cogs" />
            <span class="router-link-text">FINANCEIRO</span>
            <ul class="submenu" v-show="submenus.financeiro">
              <router-link to="/financial">
                <li class="nav-item" @mouseover="toggleActive('financial-report')"
                  :class="{ active: activeItem === 'financial-report' }">
                  <font-awesome-icon icon="fas fa-chart-line" />
                  <span class="router-link-text">RELATÓRIOS</span>
                </li>
              </router-link>
              <router-link to="/proposals">
                <li class="nav-item" @mouseover="toggleActive('proposals')"
                  :class="{ active: activeItem === 'proposals' }">
                  <font-awesome-icon icon="fas fa-file-invoice-dollar" />
                  <span class="router-link-text">PROPOSTAS</span>
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

          <li class="nav-item" @mouseover="showSubmenu('configuracoes')" @mouseleave="hideSubmenu('configuracoes')"
            :class="{ active: activeItem === 'configuracoes' }">
            <font-awesome-icon icon="fas fa-cogs" />
            <span class="router-link-text">CONFIGURAÇÕES</span>
            <ul class="submenu" v-show="submenus.configuracoes">
              <router-link :to="`/accounts/${accountId}`">
                <li class="nav-item" @mouseover="toggleActive('account')" :class="{ active: activeItem === 'account' }">
                  <font-awesome-icon icon="fas fa-user" />
                  <span class="router-link-text">CONTA</span>
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
      isNavbarOpen: false,
      submenus: {
        configuracoes: false,
        financeiro: false,
      },
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
    toggleNavbar() {
      this.isNavbarOpen = !this.isNavbarOpen;
    },
    showSubmenu(submenu) {
      this.submenus[submenu] = true;
    },
    hideSubmenu(submenu) {
      this.submenus[submenu] = false;
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

<style scoped>
.navbar-container {
  position: sticky;
  top: 0;
  color: white;
  background-color: var(--primary);
  z-index: 1000;
  height: auto;
}

.navbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-left: 5rem;
  padding-right: 5rem;
  padding-top: 0rem;
  padding-bottom: 0rem;
}

.navbar-brand .logo {
  height: 30px;
}

.navbar-toggler {
  background: none;
  border: none;
  cursor: pointer;
  display: none;
}

.navbar-toggler-icon {
  width: 60px;
  height: 6px;
  background-color: #fff;
  display: block;
  position: relative;
  transition: transform 0.3s ease; 
}

.navbar-toggler-icon::before,
.navbar-toggler-icon::after {
  content: '';
  width: 60px;
  height: 6px;
  background-color: #fff;
  display: block;
  position: absolute;
  left: 0;
  transition: transform 0.3s ease; 
}

.navbar-toggler-icon::before {
  top: -18px;
}

.navbar-toggler-icon::after {
  top: 20px;
}
/* botao fechar */
.navbar-toggler.open .navbar-toggler-icon {
  transform: rotate(45deg); /* Rotaciona o ícone principal */
}

.navbar-toggler.open .navbar-toggler-icon::before {
  transform: rotate(90deg) translateX(-18px); /* Rotaciona e desloca a linha superior */
}

.navbar-toggler.open .navbar-toggler-icon::after {
  transform: rotate(90deg) translateX(20px); /* Rotaciona e desloca a linha inferior */
}

.navbar-collapse {
  display: flex;
  flex-direction: row;
  align-items: center;
}

.navbar-collapse.show {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.navbar-nav {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: row;
}

.navbar-nav a {
  text-decoration: none;
  color: white;
}

.navbar-nav a:hover {
  text-decoration: none;
}

.nav-item {
  position: relative;
  display: flex;
  margin: 0.5rem 0;
  color: white;
  text-decoration: none;
  padding: 0.7rem;
}

.nav-item.active {
  border-color: white;
  border-style: solid;
  border-width: 1px;
  border-radius: 30px;
}

.router-link-text {
  color: #fff;
  text-decoration: none;
  margin-left: 0.5rem;
}

.submenu {
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  background-color: var(--primary);
  padding: 0;
  margin: 0;
  list-style: none;
  z-index: 1000;
  width: 200px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.nav-item:hover .submenu {
  display: block;
}

.submenu .nav-item {
  margin: 0;
  padding: 0.5rem 1rem;
  white-space: nowrap;
}

.submenu .nav-item:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

/* tela celular */
@media screen and (max-width: 768px) {
  .navbar {
    padding-left: 4rem;
    padding-right: 4rem;
    padding-top: 3rem;
    padding-bottom: 3rem;
  }

  .navbar-toggler {
    display: block;
    /* Mostra o toggler em telas pequenas */
  }

  .navbar-collapse {
    display: none;
    /* Esconde a navbar em telas pequenas */
  }

  .navbar-collapse.show {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }

  .navbar-nav {
    flex-direction: column;

  }

  .navbar-brand .logo {
    height: 70px;
    /* Ajusta o tamanho do logo em telas pequenas */
  }
}
</style>