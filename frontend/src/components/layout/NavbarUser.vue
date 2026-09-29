<template>
  <div class="navbar-container">
    <nav class="navbar relative justify-between px-20 py-0 max-md:px-16 max-md:py-12">
      <a href="#">
        <img
          :src="logoServiceflow"
          class="h-[22px] max-md:h-[60px]"
          alt="logo-serviceflow"
        />
      </a>
      <button
        class="menu-toggle hidden max-md:block bg-transparent border-none cursor-pointer"
        :class="{ open: isNavbarOpen }"
        @click="toggleNavbar"
      >
        <!-- Adiciona a classe condicional 'open' -->
        <span class="menu-toggle-icon"></span>
      </button>

      <div :class="isNavbarOpen ? 'flex flex-col items-start' : 'flex flex-row items-center max-md:hidden'">
        <ul class="flex flex-row max-md:flex-col list-none p-0 m-0 [&_a]:text-white [&_a]:no-underline">
          <router-link to="/">
            <li
              class="group relative flex my-[0.4rem] px-[0.2rem] py-2 text-white text-[0.8rem]"
              @mouseover="toggleActive('home')"
              :class="{ 'border border-primary rounded-[30px]': activeItem === 'home' }"
            >
              <font-awesome-icon icon="fas fa-calendar" class="router-link-text" />
              <span class="router-link-text"></span>
            </li>
          </router-link>

          <router-link to="/leads">
            <li
              class="group relative flex my-[0.4rem] px-[0.2rem] py-2 text-white text-[0.8rem]"
              @mouseover="toggleActive('contacts')"
              :class="{ 'border border-primary rounded-[30px]': activeItem === 'contacts' }"
            >
              <font-awesome-icon icon="fas fa-user" class="router-link-text" />
              <span class="router-link-text"></span>
            </li>
          </router-link>

          <router-link to="/companies">
            <li
              class="group relative flex my-[0.4rem] px-[0.2rem] py-2 text-white text-[0.8rem]"
              @mouseover="toggleActive('companies')"
              :class="{ 'border border-primary rounded-[30px]': activeItem === 'companies' }"
            >
              <font-awesome-icon icon="fas fa-briefcase" class="router-link-text" />
              <span class="router-link-text"></span>
            </li>
          </router-link>

          <router-link to="/opportunities">
            <li
              class="group relative flex my-[0.4rem] px-[0.2rem] py-2 text-white text-[0.8rem]"
              @mouseover="toggleActive('opportunities')"
              :class="{ 'border border-primary rounded-[30px]': activeItem === 'opportunities' }"
            >
              <font-awesome-icon icon="fas fa-bullseye" class="router-link-text" />
              <span class="router-link-text"></span>
            </li>
          </router-link>

          <li
            class="group relative flex my-[0.4rem] px-[0.2rem] py-2 text-white text-[0.8rem]"
            @mouseover="showSubmenu('financeiro')"
            @mouseleave="hideSubmenu('financeiro')"
            :class="{ 'border border-primary rounded-[30px]': activeItem === 'financeiro' }"
          >
            <font-awesome-icon icon="fas fa-coins" class="router-link-text" />
            <span class="router-link-text"></span>
            <ul class="hidden group-hover:block absolute top-full left-0 z-[1000] w-[200px] list-none p-0 m-0 bg-primary shadow-md" v-show="submenus.financeiro">
              <router-link to="/financial">
                <li
                  class="relative flex m-0 px-4 py-2 whitespace-nowrap text-white text-[0.8rem] hover:bg-white/10"
                  @mouseover="toggleActive('financial-report')"
                  :class="{ 'border border-primary rounded-[30px]': activeItem === 'financial-report' }"
                >
                  <font-awesome-icon icon="fas fa-chart-line" />
                  <span class="text-white ps-2">RELATÓRIOS</span>
                </li>
              </router-link>
              <router-link to="/opportunities-hours-report">
                <li
                  class="relative flex m-0 px-4 py-2 whitespace-nowrap text-white text-[0.8rem] hover:bg-white/10"
                  @mouseover="toggleActive('opportunities-hours-report')"
                  :class="{ 'border border-primary rounded-[30px]': activeItem === 'opportunities-hours-report' }"
                >
                  <font-awesome-icon icon="fas fa-hourglass-half" />
                  <span class="text-white ps-2">PREVISTO X REALIZADO</span>
                </li>
              </router-link>
              <router-link to="/proposals">
                <li
                  class="relative flex m-0 px-4 py-2 whitespace-nowrap text-white text-[0.8rem] hover:bg-white/10"
                  @mouseover="toggleActive('proposals')"
                  :class="{ 'border border-primary rounded-[30px]': activeItem === 'proposals' }"
                >
                  <font-awesome-icon icon="fas fa-file-invoice-dollar" />
                  <span class="text-white ps-2">PROPOSTAS</span>
                </li>
              </router-link>
              <router-link to="/invoices">
                <li
                  class="relative flex m-0 px-4 py-2 whitespace-nowrap text-white text-[0.8rem] hover:bg-white/10"
                  @mouseover="toggleActive('invoices')"
                  :class="{ 'border border-primary rounded-[30px]': activeItem === 'invoices' }"
                >
                  <font-awesome-icon icon="fas fa-receipt" />
                  <span class="text-white ps-2">FATURAS</span>
                </li>
              </router-link>
              <router-link to="/contas-a-pagar">
                <li
                  class="relative flex m-0 px-4 py-2 whitespace-nowrap text-white text-[0.8rem] hover:bg-white/10"
                  @mouseover="toggleActive('contas-a-pagar')"
                  :class="{ 'border border-primary rounded-[30px]': activeItem === 'contas-a-pagar' }"
                >
                  <font-awesome-icon icon="fas fa-file-invoice-dollar" />
                  <span class="text-white ps-2">CONTAS A PAGAR</span>
                </li>
              </router-link>
              <router-link to="/transactions">
                <li
                  class="relative flex m-0 px-4 py-2 whitespace-nowrap text-white text-[0.8rem] hover:bg-white/10"
                  @mouseover="toggleActive('transactions')"
                  :class="{ 'border border-primary rounded-[30px]': activeItem === 'transactions' }"
                >
                  <font-awesome-icon icon="fas fa-exchange-alt" />
                  <span class="text-white ps-2">MOVIMENTAÇÕES</span>
                </li>
              </router-link>
              <router-link to="/bank-accounts">
                <li
                  class="relative flex m-0 px-4 py-2 whitespace-nowrap text-white text-[0.8rem] hover:bg-white/10"
                  @mouseover="toggleActive('bank-accounts')"
                  :class="{ 'border border-primary rounded-[30px]': activeItem === 'bank-accounts' }"
                >
                  <font-awesome-icon icon="fas fa-building-columns" />
                  <span class="text-white ps-2">CONTAS BANCÁRIAS</span>
                </li>
              </router-link>
              <router-link to="/credit-cards">
                <li
                  class="relative flex m-0 px-4 py-2 whitespace-nowrap text-white text-[0.8rem] hover:bg-white/10"
                  @mouseover="toggleActive('credit-cards')"
                  :class="{ 'border border-primary rounded-[30px]': activeItem === 'credit-cards' }"
                >
                  <font-awesome-icon icon="fas fa-credit-card" />
                  <span class="text-white ps-2">CARTÕES DE CRÉDITO</span>
                </li>
              </router-link>
              <router-link to="/recurring-expenses">
                <li
                  class="relative flex m-0 px-4 py-2 whitespace-nowrap text-white text-[0.8rem] hover:bg-white/10"
                  @mouseover="toggleActive('recurring-expenses')"
                  :class="{ 'border border-primary rounded-[30px]': activeItem === 'recurring-expenses' }"
                >
                  <font-awesome-icon icon="fas fa-rotate" />
                  <span class="text-white ps-2">DESPESAS RECORRENTES</span>
                </li>
              </router-link>
              <router-link to="/services">
                <li
                  class="relative flex m-0 px-4 py-2 whitespace-nowrap text-white text-[0.8rem] hover:bg-white/10"
                  @mouseover="toggleActive('services')"
                  :class="{ 'border border-primary rounded-[30px]': activeItem === 'services' }"
                >
                  <font-awesome-icon icon="fas fa-coins" />
                  <span class="text-white ps-2">SERVIÇOS</span>
                </li>
              </router-link>
              <router-link to="/costs">
                <li
                  class="relative flex m-0 px-4 py-2 whitespace-nowrap text-white text-[0.8rem] hover:bg-white/10"
                  @mouseover="toggleActive('costs')"
                  :class="{ 'border border-primary rounded-[30px]': activeItem === 'costs' }"
                >
                  <font-awesome-icon icon="fas fa-dollar-sign" />
                  <span class="text-white ps-2">CUSTOS</span>
                </li>
              </router-link>
            </ul>
          </li>

          <router-link to="/projects">
            <li
              class="group relative flex my-[0.4rem] px-[0.2rem] py-2 text-white text-[0.8rem]"
              @mouseover="toggleActive('projects')"
              :class="{ 'border border-primary rounded-[30px]': activeItem === 'projects' }"
            >
              <font-awesome-icon icon="fas fa-project-diagram" class="router-link-text" />
              <span class="router-link-text"></span>
            </li>
          </router-link>

          <router-link to="/tasks">
            <li
              class="group relative flex my-[0.4rem] px-[0.2rem] py-2 text-white text-[0.8rem]"
              @mouseover="toggleActive('tasks')"
              :class="{ 'border border-primary rounded-[30px]': activeItem === 'tasks' }"
            >
              <font-awesome-icon icon="fas fa-tasks" class="router-link-text" />
              <span class="router-link-text"></span>
            </li>
          </router-link>

          <li
            class="group relative flex my-[0.4rem] px-[0.2rem] py-2 text-white text-[0.8rem]"
            @mouseover="toggleActive('links')"
            @click="openModal({ component: 'LinksModal', id: 'links' })"
            :class="{ 'border border-primary rounded-[30px]': activeItem === 'links' }"
          >
            <font-awesome-icon icon="fas fa-link" class="router-link-text" />
            <span class="router-link-text"></span>
          </li>

          <router-link to="/logout">
            <li
              class="group relative flex my-[0.4rem] px-[0.2rem] py-2 text-white text-[0.8rem]"
              @click="logout"
              @mouseover="toggleActive('submitLogout')"
              :class="{ 'border border-primary rounded-[30px]': activeItem === 'logout' }"
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
            <font-awesome-icon icon="fas fa-play" class="flex-shrink-0 text-success" />
            <span class="truncate font-semibold text-primary">{{ taskDisplayName }}</span>
            <journey-timer class="flex-shrink-0 text-sm text-primary/80" />
          </button>
        </div>

        <navbar-user-menu />
      </div>
    </nav>
  </div>
</template>


<script>
import { mapActions, mapState, mapMutations } from "vuex";
import NavbarUserMenu from "./NavbarUserMenu.vue";
import JourneyTimer from "@/components/journeys/JourneyTimer.vue";
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
    JourneyTimer,
  },
  methods: {
    ...mapActions(["logout"]),
    ...mapMutations(["openModal"]),
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
    openTaskModal() {
      if (this.openJourney && this.openJourney.task_id) {
        const taskId = this.openJourney.task_id;
        this.openModal({ component: "TaskDetailModal", props: { taskId }, id: `task-${taskId}` });
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
  color:  var(--color-primary);
  backdrop-filter: blur(10px);
  z-index: 1000;
  height: auto;
}

/* Ícone hambúrguer: as três linhas são o próprio span + ::before/::after */
.menu-toggle-icon {
  width: 60px;
  height: 6px;
  background-color: var(--color-base-100);
  display: block;
  position: relative;
  transition: transform 0.3s ease;
}

.menu-toggle-icon::before,
.menu-toggle-icon::after {
  content: "";
  width: 60px;
  height: 6px;
  background-color: var(--color-base-100);
  display: block;
  position: absolute;
  left: 0;
  transition: transform 0.3s ease;
}

.menu-toggle-icon::before {
  top: -18px;
}

.menu-toggle-icon::after {
  top: 20px;
}
/* botao fechar */
.menu-toggle.open .menu-toggle-icon {
  transform: rotate(45deg); /* Rotaciona o ícone principal */
}

.menu-toggle.open .menu-toggle-icon::before {
  transform: rotate(90deg) translateX(-18px); /* Rotaciona e desloca a linha superior */
}

.menu-toggle.open .menu-toggle-icon::after {
  transform: rotate(90deg) translateX(20px); /* Rotaciona e desloca a linha inferior */
}

.router-link-text {
  color:  var(--color-primary);
  text-decoration: none;
  margin-left: 0.5rem;
  font-size: 0.8rem;
  font-weight: 400;
}
</style>
