<template>
  <div class="user-menu" @mouseleave="closeDropdown">
    <div class="user-info" @click="toggleDropdown">
      <img
        v-if="userData && userData.photo"
        :src="urlImagePhoto"
        alt="User Image"
        class="user-image"
      />
      <font-awesome-icon v-else icon="fas fa-user" class="user-faicon" />
    </div>
    <div class="play-container">
      <template v-if="openJourney">
        <font-awesome-icon icon="fas fa-play" class="play" />
      </template>
      <template v-else>
        <font-awesome-icon icon="fas fa-pause" class="text-white" />
      </template>
    </div>
    <div v-if="dropdownVisible" class="dropdown-menu">
      <router-link
        v-if="openJourney"
        :to="taskLink"
        class="dropdown-item"
      >
        <font-awesome-icon icon="fas fa-play" class="play" />
        {{ taskDisplayName }}
      </router-link>
      
      <router-link :to="`/users/${userData.id}`" class="dropdown-item">
        CONTA
      </router-link>
      <router-link to="/journeys" class="dropdown-item">JORNADAS</router-link>
      
      <!-- Últimas 5 tarefas -->
      <div v-if="recentJourneys.length > 0" class="recent-tasks-section">
        <div class="section-divider">TAREFAS RECENTES</div>
        <router-link
          v-for="journey in recentJourneys"
          :key="journey.id"
          :to="getJourneyLink(journey)"
          class="dropdown-item recent-task-item"
        >
          <font-awesome-icon icon="fas fa-history" class="text-gray-400 text-sm mr-2" />
          <span class="truncate">{{ truncateTaskName(journey.task_name) }}</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
import { IMAGES_PATH, JOURNEY_RECENT } from "@/config/apiConfig";
import axios from "axios";

export default {
  data() {
    return {
      dropdownVisible: false,
      recentJourneys: [],
    };
  },
  computed: {
    ...mapState({
      userData: (state) => state.userData,
      openJourney: (state) => state.openJourney,
    }),
    taskLink() {    
      if (!this.openJourney) {
        return null;
      }
      
      if (this.openJourney.opportunity_id) {
        return { 
          name: 'opportunityShow', 
          params: { id: this.openJourney.opportunity_id }, 
          query: { scrollTo: 'tasks' } 
        };
      } else if (this.openJourney.project_id) {
        return { 
          name: 'projectShow', 
          params: { id: this.openJourney.project_id }, 
          query: { scrollTo: 'tasks' } 
        };
      }
      
      return null;
    },
    taskDisplayName() {
      return this.openJourney.task?.name || this.openJourney.name || 'Tarefa sem nome';
    },
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
      return '/journeys';
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
    if (this.openJourney === false) {
      this.checkOpenJourneys();
    }
  },
  beforeUnmount() {
    document.removeEventListener("click", this.handleClickOutside);
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
  border-color: var(--primary);
  border-width: 5px;
  border-radius: 50%;
  margin-right: 0px;
}

.user-faicon {
  border-style: solid;
  border-color: white;
  border-width: 3px;
  border-radius: 50%;
  font-size: 1.8rem;
  padding: 0.6rem;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background-color: var(--primary);
  border: 1px solid #ccc;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  display: block;
  min-width: 230px;
  max-width: 300px;
}

.dropdown-item {
  display: block;
  padding: 10px;
  text-decoration: none;
  color: white;
}

.dropdown-item:hover {
  background-color: #f0f0f0;
  color: var(--primary);
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

.recent-task-item {
  display: flex;
  align-items: center;
  padding: 8px 10px;
  font-size: 0.9rem;
}

.recent-task-item .truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.play-container {
  position: absolute;
  font-size: 1.2rem;
  bottom: -3px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1;
}

.play {
  color: var(--green);
  animation: blink 4s infinite;
}

@keyframes blink {
  0%,
  100% {
    color: var(--green);
  }
  50% {
    color: var(--green-light);
  }
}
</style>