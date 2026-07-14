<template>
  <div class="navbar-container">
    <nav class="navbar">
      <a class="navbar-brand" href="#">
        <img
          :src="logoServiceflow"
          class="logo"
          alt="logo-serviceflow"
        />
      </a>
      <button
        class="navbar-toggler"
        :class="{ open: isNavbarOpen }"
        @click="toggleNavbar"
      >
        <!-- Adiciona a classe condicional 'open' -->
        <span class="navbar-toggler-icon"></span>
      </button>

      <div :class="['navbar-collapse', { show: isNavbarOpen }]">
        <ul class="navbar-nav">
          <router-link to="/">
            <li
              class="nav-item"
              @mouseover="toggleActive('home')"
              :class="{ active: activeItem === 'home' }"
            >
              <font-awesome-icon icon="fas fa-calendar" class="router-link-text" />
              <span class="router-link-text"></span>
            </li>
          </router-link>

          <router-link to="/leads">
            <li
              class="nav-item"
              @mouseover="toggleActive('contacts')"
              :class="{ active: activeItem === 'contacts' }"
            >
              <font-awesome-icon icon="fas fa-user" class="router-link-text" />
              <span class="router-link-text"></span>
            </li>
          </router-link>

          <router-link to="/companies">
            <li
              class="nav-item"
              @mouseover="toggleActive('companies')"
              :class="{ active: activeItem === 'companies' }"
            >
              <font-awesome-icon icon="fas fa-briefcase" class="router-link-text" />
              <span class="router-link-text"></span>
            </li>
          </router-link>

          <router-link to="/opportunities">
            <li
              class="nav-item"
              @mouseover="toggleActive('opportunities')"
              :class="{ active: activeItem === 'opportunities' }"
            >
              <font-awesome-icon icon="fas fa-bullseye" class="router-link-text" />
              <span class="router-link-text"></span>
            </li>
          </router-link>

          <li
            class="nav-item"
            @mouseover="showSubmenu('financeiro')"
            @mouseleave="hideSubmenu('financeiro')"
            :class="{ active: activeItem === 'financeiro' }"
          >
            <font-awesome-icon icon="fas fa-coins" class="router-link-text" />
            <span class="router-link-text"></span>
            <ul class="submenu" v-show="submenus.financeiro">
              <router-link to="/financial">
                <li
                  class="nav-item"
                  @mouseover="toggleActive('financial-report')"
                  :class="{ active: activeItem === 'financial-report' }"
                >
                  <font-awesome-icon icon="fas fa-chart-line" />
                  <span class="text-white ps-2">RELATÓRIOS</span>
                </li>
              </router-link>
              <router-link to="/proposals">
                <li
                  class="nav-item"
                  @mouseover="toggleActive('proposals')"
                  :class="{ active: activeItem === 'proposals' }"
                >
                  <font-awesome-icon icon="fas fa-file-invoice-dollar" />
                  <span class="text-white ps-2">PROPOSTAS</span>
                </li>
              </router-link>
              <router-link to="/invoices">
                <li
                  class="nav-item"
                  @mouseover="toggleActive('invoices')"
                  :class="{ active: activeItem === 'invoices' }"
                >
                  <font-awesome-icon icon="fas fa-receipt" />
                  <span class="text-white ps-2">FATURAS</span>
                </li>
              </router-link>
              <router-link to="/transactions">
                <li
                  class="nav-item"
                  @mouseover="toggleActive('transactions')"
                  :class="{ active: activeItem === 'transactions' }"
                >
                  <font-awesome-icon icon="fas fa-exchange-alt" />
                  <span class="text-white ps-2">MOVIMENTAÇÕES</span>
                </li>
              </router-link>
              <router-link to="/bank-accounts">
                <li
                  class="nav-item"
                  @mouseover="toggleActive('bank-accounts')"
                  :class="{ active: activeItem === 'bank-accounts' }"
                >
                  <font-awesome-icon icon="fas fa-building-columns" />
                  <span class="text-white ps-2">CONTAS BANCÁRIAS</span>
                </li>
              </router-link>
              <router-link to="/services">
                <li
                  class="nav-item"
                  @mouseover="toggleActive('services')"
                  :class="{ active: activeItem === 'services' }"
                >
                  <font-awesome-icon icon="fas fa-coins" />
                  <span class="text-white ps-2">SERVIÇOS</span>
                </li>
              </router-link>
              <router-link to="/costs">
                <li
                  class="nav-item"
                  @mouseover="toggleActive('costs')"
                  :class="{ active: activeItem === 'costs' }"
                >
                  <font-awesome-icon icon="fas fa-dollar-sign" />
                  <span class="text-white ps-2">CUSTOS</span>
                </li>
              </router-link>
            </ul>
          </li>

          <router-link to="/projects">
            <li
              class="nav-item"
              @mouseover="toggleActive('projects')"
              :class="{ active: activeItem === 'projects' }"
            >
              <font-awesome-icon icon="fas fa-project-diagram" class="router-link-text" />
              <span class="router-link-text"></span>
            </li>
          </router-link>

          <router-link to="/tasks">
            <li
              class="nav-item"
              @mouseover="toggleActive('tasks')"
              :class="{ active: activeItem === 'tasks' }"
            >
              <font-awesome-icon icon="fas fa-tasks" class="router-link-text" />
              <span class="router-link-text"></span>
            </li>
          </router-link>

          <router-link to="/links">
            <li
              class="nav-item"
              @mouseover="toggleActive('links')"
              :class="{ active: activeItem === 'links' }"
            >
              <font-awesome-icon icon="fas fa-link" class="router-link-text" />
              <span class="router-link-text"></span>
            </li>
          </router-link>

          <router-link to="/logout">
            <li
              class="nav-item"
              @click="logout"
              @mouseover="toggleActive('submitLogout')"
              :class="{ active: activeItem === 'logout' }"
            >
              <font-awesome-icon icon="fas fa-sign-out" class="router-link-text" />
              <span class="router-link-text"></span>
            </li>
          </router-link>
        </ul>

        <div v-if="openJourney" class="flex items-center gap-2 mt-2 px-3 py-1 rounded-full bg-white/80 border border-primary shadow-sm" style="max-width: 420px; margin: 0 auto;">
          <button
            @click="openTaskModal"
            class="flex-1 flex items-center gap-2 text-primary no-underline hover:opacity-80 transition-opacity min-w-0 bg-transparent border-0 cursor-pointer"
            style="min-width:0"
          >
            <font-awesome-icon icon="fas fa-play" class="flex-shrink-0 text-green-600" />
            <span class="truncate font-semibold text-primary">{{ taskDisplayName }}</span>
          </button>
          <button
            @click="stopJourney(openJourney.id)"
            class="w-7 h-7 flex-shrink-0 flex items-center justify-center rounded-full bg-blue-500 text-white hover:bg-blue-700 transition-all duration-200 hover:scale-105 text-sm shadow"
            title="Parar jornada"
            style="margin-left: 2px"
          >
            <font-awesome-icon icon="fas fa-hand" />
          </button>
        </div>

        <navbar-user-menu />
      </div>
    </nav>
  </div>
</template>


<script>
import { mapActions, mapState, mapMutations } from "vuex";
import { BACKEND_URL, JOURNEY_URL_PARAMETER } from "@/config/apiConfig";
import axios from "axios";
import NavbarUserMenu from "./NavbarUserMenu.vue";
import logoServiceflow from '@/assets/logo-serviceflow-ROXO.png';

export default {
  data() {
    return {
      logoServiceflow,
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
    ...mapActions(["logout"]),
    ...mapMutations(["setOpenJourney", "setSelectedTaskId"]),
    async submitLogout() {
      await this.logout();
      this.toggleActive("logout");
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
    async stopJourney(journeyId) {
      const now = new Date().toISOString();
      try {
        await axios.put(`${BACKEND_URL}${JOURNEY_URL_PARAMETER}${journeyId}`, { id: journeyId, end: now });
        this.setOpenJourney(null);
      } catch (error) {
        console.error("Erro ao parar a jornada:", error);
      }
    },
    openTaskModal() {
      if (this.openJourney && this.openJourney.task_id) {
        this.setSelectedTaskId(this.openJourney.task_id);
      }
    },
  },
  computed: {
    ...mapState(["accountId", "openJourney"]),
    taskDisplayName() {
      return this.openJourney?.task?.name || this.openJourney?.name || "Tarefa sem nome";
    },
  },
};
</script>

<style scoped>
.navbar-container {
  position: sticky;
  top: 0;
  color:  var(--primary);
  background-color: rgba(var(--primary-rgb), 0.8);
  backdrop-filter: blur(10px);
  z-index: 1000;
  height: auto;
}

.navbar {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-left: 5rem;
  padding-right: 5rem;
  padding-top: 0rem;
  padding-bottom: 0rem;
}

.navbar-brand .logo {
  height: 22px;
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
  content: "";
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
  margin: 0.4rem 0;
  color: white;
  font-size: 0.8rem;
  text-decoration: none;
  padding-left: 0.2rem;
  padding-right: 0.2rem;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

.nav-item.active {
  border-color: var(--primary);
  border-style: solid;
  border-width: 1px;
  border-radius: 30px;
}

.router-link-text {
  color:  var(--primary);
  text-decoration: none;
  margin-left: 0.5rem;
  font-size: 0.8rem;
  font-weight: 400;
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
    height: 60px;
    /* Ajusta o tamanho do logo em telas pequenas */
  }
}
</style>