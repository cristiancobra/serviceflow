<template>
  <div class="app-container">
    <NavbarUser v-if="showNavbar" />
    <div class="main">
      <router-view />
    </div>
    <!-- Overlay compartilhado dos modais globais (empilháveis lado a lado) -->
    <div
      v-show="openModals.length > 0"
      class="fixed inset-0 z-50 flex items-center justify-center flex-wrap gap-4 p-4 overflow-y-auto bg-black/30 backdrop-blur-sm"
    >
      <component
        v-for="modal in openModals"
        :key="modal.id"
        :is="modalRegistry[modal.component]"
        v-bind="modal.props"
        :compact="openModals.length > 1"
        v-on="modalListeners(modal)"
      />
    </div>
  </div>
</template>


<script>
import { mapActions, mapState, mapMutations } from 'vuex';
import NavbarUser from "./components/layout/NavbarUser.vue";
import modalRegistry from "@/components/modals/registry.js";

export default {
  data() {
    return {
      showNavbar: null,
      modalRegistry,
    };
  },
  components: {
    NavbarUser,
  },
  computed: {
    ...mapState(['openModals']),
  },
  methods: {
    ...mapActions(['checkAuthentication']),
    ...mapMutations(['setUpdatedTask', 'closeModal']),
    async startAuthCheck() {
      this.checkAuthentication();
      setInterval(async () => {
        await this.checkAuthentication();
      }, 60000); // Verifica a cada 60 segundos
    },
    handleTaskUpdated(updatedTask) {
      this.setUpdatedTask(updatedTask);
    },
    modalListeners(modal) {
      return {
        close: () => this.closeModal(modal.id),
        'task-updated': this.handleTaskUpdated,
        ...modal.listeners,
      };
    },
  },
  watch: {
    $route(to) {
      this.showNavbar = to.name !== 'login';
    }
  },
  created() {
    this.showNavbar = this.$route.name !== 'login'; // Inicializa a condição da navbar
    this.startAuthCheck(); // Inicia a verificação periódica de autenticação
  },
};
</script>

<style>
.app-container {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  width: 100%;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  background-color: var(--background-light);
}

.main {
  margin-left: 8%;
  margin-right: 8%;
  /* margin-bottom: 5vh; falta o rodape */
  padding: 0%;
  transition: all 0.5s;
}

.router-view {
  flex: 1;
}
</style>