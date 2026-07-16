<template>
  <div class="app-container">
    <NavbarUser v-if="showNavbar" />
    <div class="main">
      <router-view />
    </div>
    <!-- Modal global de detalhes da tarefa -->
    <task-detail-modal
      :modelValue="showTaskModal"
      @update:modelValue="updateTaskModalState"
      :taskId="selectedTaskId"
      @task-updated="handleTaskUpdated"
    />
  </div>
</template>


<script>
import { mapActions, mapState, mapMutations } from 'vuex';
import NavbarUser from "./components/layout/NavbarUser.vue";
import TaskDetailModal from "@/components/modals/details/TaskDetailModal.vue";

export default {
  data() {
    return {
      showNavbar: null,
      showTaskModal: false,
    };
  },
  components: {
    NavbarUser,
    TaskDetailModal,
  },
  computed: {
    ...mapState(['selectedTaskId']),
  },
  methods: {
    ...mapActions(['checkAuthentication']),
    ...mapMutations(['setSelectedTaskId', 'setUpdatedTask']),
    async startAuthCheck() {
      this.checkAuthentication();
      setInterval(async () => {
        await this.checkAuthentication();
      }, 60000); // Verifica a cada 60 segundos
    },
    handleTaskUpdated(updatedTask) {
      this.setUpdatedTask(updatedTask);
    },
    updateTaskModalState(isOpen) {
      this.showTaskModal = isOpen;
      if (!isOpen) {
        this.setSelectedTaskId(null);
      }
    }
  },
  watch: {
    selectedTaskId(newVal) {
      if (newVal) {
        this.showTaskModal = true;
      } else {
        this.showTaskModal = false;
      }
    },
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