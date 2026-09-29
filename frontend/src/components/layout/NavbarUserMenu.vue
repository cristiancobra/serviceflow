<template>
  <div class="user-menu" @mouseleave="closeDropdown">
    <div class="user-info" @click="toggleDropdown">
      <img
        v-if="userData && userData.photo"
        :src="urlImagePhoto"
        alt="User Image"
        :class="['user-image', { 'user-image-working': openJourney }]"
      />
      <font-awesome-icon v-else icon="fas fa-user" :class="['user-faicon', { 'user-faicon-working': openJourney }]" />
    </div>
    <div
      v-if="dropdownVisible"
      class="absolute top-full right-0 z-[1000] min-w-[230px] max-w-[300px] bg-primary border border-base-300 shadow-md"
    >
     
      <router-link :to="`/users/${userData.id}`" class="block p-2.5 text-white no-underline hover:bg-base-200 hover:text-primary">
        PERFIL DO USUÁRIO
      </router-link>
      <router-link :to="`/accounts/${accountId}`" class="block p-2.5 text-white no-underline hover:bg-base-200 hover:text-primary">
        CONTA
      </router-link>
      <router-link to="/journeys" class="block p-2.5 text-white no-underline hover:bg-base-200 hover:text-primary">JORNADAS</router-link>
      
      <!-- Últimas 5 tarefas -->
      <div v-if="recentJourneys.length > 0" class="recent-tasks-section">
        <div class="section-divider">TAREFAS RECENTES</div>
        <router-link
          v-for="journey in recentJourneys"
          :key="journey.id"
          :to="getJourneyLink(journey)"
          class="flex items-center px-2.5 py-2 text-[0.9rem] text-white no-underline hover:bg-base-200 hover:text-primary"
        >
          <font-awesome-icon icon="fas fa-history" class="text-base-content/50 text-sm mr-2" />
          <span class="truncate">{{ truncateTaskName(journey.task_name) }}</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import { IMAGES_PATH, JOURNEY_RECENT, BACKEND_URL, JOURNEY_URL_PARAMETER } from "@/config/apiConfig";
import axios from "axios";

export default {
  data() {
    return {
      dropdownVisible: false,
      recentJourneys: [],
      checkInterval: null, // Intervalo para verificar jornadas abertas
    };
  },
  computed: {
    ...mapState({
      userData: (state) => state.userData,
      openJourney: (state) => state.openJourney,
      accountId: (state) => state.accountId,
    }),
    urlImagePhoto() {
      return `${IMAGES_PATH}${this.userData.photo}`;
    },
  },
  watch: {
    openJourney: {
      handler(newVal, oldVal) {
        console.log("openJourney mudou:", { oldVal, newVal });
      },
      immediate: true,
      deep: true
    }
  },
  methods: {
    ...mapActions(['checkOpenJourneys']),
    ...mapMutations(['setOpenJourney']),
    toggleDropdown() {
      this.dropdownVisible = !this.dropdownVisible;
      if (this.dropdownVisible) {
        console.log('Dropdown aberto, carregando jornadas recentes...');
        this.loadRecentJourneys();
      }
    },
    closeDropdown() {
      this.dropdownVisible = false;
    },
    handleClickOutside(event) {
      if (!this.$el.contains(event.target)) {
        this.closeDropdown();
      }
    },
    async loadRecentJourneys() {
      try {
        const url = `api/${JOURNEY_RECENT}`;
        console.log('Fazendo requisição para:', url);
        const response = await axios.get(url);
        console.log('Resposta recebida:', response.data);
        this.recentJourneys = response.data;
        console.log('recentJourneys atualizado:', this.recentJourneys);
      } catch (error) {
        console.error('Erro ao carregar jornadas recentes:', error);
        console.error('Detalhes do erro:', error.response?.data);
        console.error('URL chamada:', error.config?.url);
      }
    },
    getJourneyLink(journey) {
      if (journey.opportunity_id) {
        return { 
          name: 'opportunityShow', 
          params: { id: journey.opportunity_id }, 
          query: { scrollTo: 'tasks' } 
        };
      } else if (journey.project_id) {
        return { 
          name: 'projectShow', 
          params: { id: journey.project_id }, 
          query: { scrollTo: 'tasks' } 
        };
      }
      return '/tasks';
    },
    truncateTaskName(name) {
      if (name.length > 35) {
        return name.substring(0, 35) + '...';
      }
      return name;
    },
  },
  mounted() {
    document.addEventListener("click", this.handleClickOutside);
    
    // Verificar jornadas abertas ao montar o componente
    this.checkOpenJourneys();
    
    // Verificar a cada 30 segundos se há mudanças nas jornadas
    this.checkInterval = setInterval(() => {
      this.checkOpenJourneys();
    }, 30000); // 30 segundos
  },
  beforeUnmount() {
    document.removeEventListener("click", this.handleClickOutside);
    
    // Limpar o intervalo ao desmontar
    if (this.checkInterval) {
      clearInterval(this.checkInterval);
    }
  },
};
</script>

<style scoped>
.user-menu {
  position: absolute;
  top: 1%;
  right: 1%;
  z-index: 10;
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  color: white;
}

.user-image {
  width: 48px;
  height: 50px;
  border-style: solid;
  border-color: var(--color-primary);
  border-width: 5px;
  border-radius: 50%;
  margin-right: 0px;
  transition: border-color 0.3s ease;
}

.user-image-working {
  border-color: var(--color-success);
  box-shadow: 0 0 10px color-mix(in oklab, var(--color-success) 60%, transparent);
  animation: border-pulse 2s ease-in-out infinite;
}

.user-faicon {
  border-style: solid;
  border-color: white;
  border-width: 3px;
  border-radius: 50%;
  font-size: 1.8rem;
  padding: 0.6rem;
  transition: border-color 0.3s ease;
}

.user-faicon-working {
  border-color: var(--color-success);
  box-shadow: 0 0 10px color-mix(in oklab, var(--color-success) 60%, transparent);
  animation: border-pulse 2s ease-in-out infinite;
}




.recent-tasks-section {
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  margin: 5px 0;
}

.section-divider {
  padding: 8px 10px;
  font-size: 0.75rem;
  font-weight: bold;
  color: rgba(255, 255, 255, 0.7);
  background-color: rgba(0, 0, 0, 0.1);
}



.play-container {
  position: absolute;
  font-size: 0.8rem;
  bottom: -3px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1;
}

.working {
  color: var(--color-success);
  filter: drop-shadow(0 0 3px rgba(16, 185, 129, 0.8));
  animation: pulse-working 2s ease-in-out infinite;
}

.stopped {
  color: color-mix(in oklab, var(--color-base-content) 60%, transparent);
  opacity: 0.6;
}

@keyframes pulse-working {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.7;
    transform: scale(1.1);
  }
}

@keyframes border-pulse {
  0%, 100% {
    border-color: var(--color-success);
    box-shadow: 0 0 10px color-mix(in oklab, var(--color-success) 60%, transparent);
  }
  50% {
    border-color: var(--color-success);
    box-shadow: 0 0 20px color-mix(in oklab, var(--color-success) 90%, transparent);
  }
}
</style>