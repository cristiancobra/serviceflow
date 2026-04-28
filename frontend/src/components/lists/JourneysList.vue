<template>
  <div class="section-container">
    <AddMessage v-if="messageStatus" :messageStatus="messageStatus" :messageText="messageText">
    </AddMessage>
    <div class="section-header">
      <div class="section-title">
        <font-awesome-icon icon="fa-solid fa-clock" class="icon" />
        <h2>Jornadas</h2>
      </div>
      <div class="section-action">
      </div>
    </div>

    <div class="search-container">
      <input
        type="text"
        class="search-input"
        v-model="searchTerm"
        placeholder="Digite para buscar"
      />
    </div>

    <section class="list-container">
      <div class="mb-4" v-if="filteredJourneys.length > 0">
        <PaginateNav :paginationData="paginationData" @update-data="updatePaginationList" />
      </div>
      
      <div class="p-5 text-center text-gray-500" v-if="!localJourneys || localJourneys.length === 0">
        Ainda não possui nenhuma jornada
      </div>
      
      <div class="p-5 text-center text-gray-500" v-else-if="filteredJourneys.length === 0">
        Nenhuma jornada encontrada com os critérios de busca
      </div>
      
      <div v-else class="list-line" v-for="journey in filteredJourneys" v-bind:key="journey.id"
        :class="{ 'highlight': journey.id === newJourneyId }">
        <div class="flex flex-[2] items-center justify-start mr-4">
          <date-editable-input name="start" v-model="journey.start" @save="updateJourney('start', $event, journey.id)" />
        </div>
        <div class="flex flex-[2] items-center justify-start mr-4 pl-5">
          <time-editable-input name="end" v-model="journey.end"
            @save="updateJourney('end', $event, journey.id)" />
        </div>
        <div class="flex flex-[2] items-center justify-start mr-4 pl-5">
          <p class="time-bold">
            {{ formatDuration(journey.duration) }}
          </p>
        </div>
        <div v-if="journey.task" class="flex flex-[6] items-center justify-start">
          <router-link v-if="getTaskLink(journey.task)" :to="getTaskLink(journey.task)" class="text-black text-sm">
            {{ journey.task.name }}
          </router-link>
          <span v-else class="text-black text-sm">{{ journey.task.name }}</span>
        </div>
        <div v-if="journey.task" class="flex flex-[4] items-center justify-start mr-4">
          <router-link :class="getColorClassForName(journey.task.opportunity.name)" class="flex items-center"
            v-if="journey.task.opportunity"
            :to="{ 
              name: 'opportunityShow', 
              params: { id: journey.task.opportunity.id },
              query: { tab: 'tasks' },
              hash: '#task-' + journey.task.id
            }">
            <font-awesome-icon icon="fa-solid fa-bullseye"
              :class="getColorClassForName(journey.task.opportunity.name)" />
            <p class="text-sm font-semibold ps-2" :class="getColorClassForName(journey.task.opportunity.name)">
              {{ trimName(journey.task.opportunity.name) }}
            </p>
          </router-link>
          <router-link class="flex items-center" v-else-if="journey.task.project"
            :to="{ 
              name: 'projectShow', 
              params: { id: journey.task.project.id },
              hash: '#task-' + journey.task.id
            }">
            <font-awesome-icon icon="fa-solid fa-folder-open"
              :class="getColorClassForName(journey.task.project.name)" />
            <p class="text-sm font-semibold ps-2" :class="getColorClassForName(journey.task.project.name)">
              {{ trimName(journey.task.project.name) }}
            </p>
          </router-link>
          <div v-else>
            <p class="text-sm text-gray-400">
              ----
            </p>
          </div>
        </div>
        <div class="flex flex-1 items-center justify-start gap-2">
          <button 
            v-if="!journey.end" 
            class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-500 text-white hover:bg-blue-700 transition"
            @click="stopJourney(journey.id)"
            title="Parar jornada"
          >
            <font-awesome-icon icon="fa-solid fa-hand" class="text-sm" />
          </button>
          <button 
            class="w-7 h-7 flex items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-700 transition"
            @click="deleteItem(journey)"
            title="Excluir jornada"
          >
            <font-awesome-icon icon="fa-solid fa-trash-alt" class="text-sm" />
          </button>
        </div>
      </div>
      <div class="mt-4">
        <PaginateNav :paginationData="paginationData" @update-data="updatePaginationList" />
      </div>
    </section>
  </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, JOURNEY_URL_PARAMETER } from "@/config/apiConfig";
import { getColorClassForName, trimName } from "@/utils/card/cardUtils";
import { displayLocalTime } from "@/utils/date/dateUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import { formatDuration } from "@/utils/date/dateUtils";
import { mapMutations } from 'vuex';
import DateEditableInput from "../fields/datetime/DateTimeEditableInput.vue";
import TimeEditableInput from "@/components/forms/inputs/time/TimeEditableInput.vue";
import PaginateNav from "@/components/layout/PaginateNav.vue";

export default {
  name: "JourneysList",
  components: {
    DateEditableInput,
    PaginateNav,
    TimeEditableInput,
  },
  props: {
    journeys: {
      type: Array,
      default: () => [],
    },
    template: {
      type: String,
    },
    paginationData: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      localJourneys: [],
      messageStatus: "",
      messageText: "",
      searchTerm: "",
      updatedJourney: {
        id: null,
        details: null,
        duration: null,
        start: null,
        end: null,
      },
      stopedJourney: {
        id: null,
        end: null,
      },
      editedJourneys: [],
      newJourneyId: null,
    };
  },
  computed: {
    filteredJourneys() {
      if (!this.searchTerm) {
        return this.localJourneys;
      }
      const term = this.searchTerm.toLowerCase();
      return this.localJourneys.filter(journey => {
        const taskName = journey.task?.name?.toLowerCase() || '';
        const opportunityName = journey.task?.opportunity?.name?.toLowerCase() || '';
        const projectName = journey.task?.project?.name?.toLowerCase() || '';
        return taskName.includes(term) || opportunityName.includes(term) || projectName.includes(term);
      });
    }
  },
  emits: ["new-journey-event", "journey-updated", "journey-deleted"],
  methods: {
    displayLocalTime,
    formatDateBr,
    formatDuration,
    getColorClassForName,
    trimName,
    addJourneyCreated(newJourney) {
      this.localJourneys.unshift(newJourney);
      this.highlightNewJourney(newJourney.id);
      this.$emit("update-task-duration");
      this.emitLastJourneyEnd();
    },
    highlightNewJourney(journeyId) {
      this.newJourneyId = journeyId;
      setTimeout(() => {
        this.newJourneyId = null;
      }, 2000);

    },

    cancelEditDateTime(event, journey) {
      // Verifica se a tecla pressionada é "Esc" (código 27)
      if (event.keyCode === 27) {
        journey.editing = false;
        // Remove o event listener após cancelar a edição
        document.removeEventListener('keydown', (event) => this.cancelEditDateTime(event, journey));
      }
    },
    deleteItem(item) {
      const confirmation = confirm(
        "Tem certeza que deseja excluir esta jornada?"
      );

      if (confirmation) {
        axios
          .delete(`${BACKEND_URL}${JOURNEY_URL_PARAMETER}${item.id}`)
          .then((response) => {
            if (response.status === 200) {
              // this.$emit("journey-deleted", journeyToDelete.id);
              this.deleteItemList(item.id);
              this.messageStatus = "deleted";
              this.messageText = "Jornada excluída com sucesso!";
              this.emitLastJourneyEnd();
            } else {
              alert("Falha ao excluir a jornada. Por favor, tente novamente.");
            }
          })
          .catch((error) => {
            console.error("Erro ao excluir a jornada:", error);
            alert("Erro ao excluir a jornada. Por favor, tente novamente.");
          });
      }
    },
    deleteItemList(itemId) {
      // Atualize a lista de jornadas após a exclusão
      this.localJourneys = this.localJourneys.filter(
        (journey) => journey.id !== itemId
      );
    },
    emitLastJourneyEnd() {
      if (this.localJourneys.length === 0) {
        this.$emit('last-journey-end', null)
        return null;
      }

      const sortedJourneys = [...this.localJourneys].sort((a, b) => {
        if (a.end < b.end) return -1;
        if (a.end > b.end) return 1;
        return 0;
      });

      const lastJourney = sortedJourneys[sortedJourneys.length - 1];

      if (!lastJourney.end) {
        this.$emit('last-journey-end', null)
        return null;
      }

      try {
        this.$emit('last-journey-end', lastJourney.end)
      } catch (error) {
        console.error('Erro ao converter a data:', error);
        return null;
      }
    },
    ...mapMutations(['setOpenJourney']),
    async stopJourney(journeyId) {
      const fieldName = 'end';
      const now = new Date();
      // Enviar no formato ISO (JavaScript) - o backend converterá com convertJavascriptDate
      const endValue = now.toISOString();
      this.updateJourney(fieldName, endValue, journeyId);
      this.$store.commit('setOpenJourney', null);
    },
    async updateJourney(fieldName, editedValue, journeyId) {
      const editedJourney = {
        id: journeyId,
        [fieldName]: editedValue,
      };

      try {
        const response = await axios.put(
          `${BACKEND_URL}${JOURNEY_URL_PARAMETER}${journeyId}`,
          editedJourney
        );

        this.updatedJourney = response.data.data;
        this.updateJourneyInList(this.updatedJourney);
        this.emitLastJourneyEnd();
      } catch (error) {
        console.error("Erro ao atualizar a jornada:", error);
      }
    },
    updateJourneyInList(updatedJourney) {
      const index = this.localJourneys.findIndex(journey => journey.id === updatedJourney.id);
      if (index !== -1) {
        this.localJourneys.splice(index, 1, updatedJourney);
      }
    },
    updatePaginationList(newData) {
      this.localJourneys = newData;
    },
    getTaskLink(task) {
      if (task.opportunity) {
        return { 
          name: 'opportunityShow', 
          params: { id: task.opportunity.id },
          query: { tab: 'tasks' },
          hash: '#task-' + task.id
        };
      } else if (task.project) {
        return { 
          name: 'projectShow', 
          params: { id: task.project.id },
          hash: '#task-' + task.id
        };
      } else {
        return null;
      }
    },
  },
  watch: {
    journeys: {
      handler(newVal) {
        this.localJourneys = newVal || [];
      },
      deep: true,
      immediate: true,
    },
  },
};
</script>

<style scoped>
.time-bold {
  text-align: center;
  font-size: 16px;
  font-weight: 800;
  color: var(--purple);
}

a {
  text-decoration: none;
  color: inherit;
}

a:visited {
  color: inherit;
}

a:hover {
  color: inherit;
}

a:active {
  color: inherit;
}
</style>
