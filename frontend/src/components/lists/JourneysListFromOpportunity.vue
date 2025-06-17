<template>
  <div class="section-container">
    <AddMessage v-if="messageStatus" :messageStatus="messageStatus" :messageText="messageText">
    </AddMessage>
    <div class="section-header">
      <div class="section-title">
      </div>
      <div class="section-action">
        <journey-create-form @new-journey-event="addJourneyCreated" />
      </div>
    </div>

    <div calss="list-line" v-if="localJourneys.length > 0">
      <div class="">
        <PaginateNav :paginationData="paginationData" @update-data="updatePaginationList" />
      </div>
      <div class="p-5" v-if="!localJourneys">
        Ainda não possui nenhuma jornada
      </div>
      <div v-else class="list-line" v-for="journey in localJourneys" v-bind:key="journey.id"
        :class="{ 'highlight': journey.id === newJourneyId }">
        <div class="date-column">
          <DateEditableInput name="start" v-model="journey.start" @save="updateJourney('start', $event, journey.id)" />
        </div>
        <div class="date-column">
          <TimeEditableInput class="ps-5" name="end" v-model="journey.end"
            @save="updateJourney('end', $event, journey.id)" />
        </div>
        <div class="date-column">
          <p class="time-bold ps-5">
            {{ formatDuration(journey.duration) }}
          </p>
        </div>
        <div v-if="task" class="task-column">
          <router-link :to="{ name: 'taskShow', params: { id: task.id } }">
            {{ task.name }}
          </router-link>
        </div>
        <div class="icons-column">
          <button v-if="!journey.end" class="button-circular stop" @click="stopJourney(journey.id)">
            <span class="stop">
              <font-awesome-icon icon="fa-solid fa-hand" />
            </span>
          </button>
          <button class="button-circular delete" @click="deleteItem(journey)">
            <span class="delete">
              <font-awesome-icon icon="fa-solid fa-trash-alt" />
            </span>
          </button>
        </div>
      </div>
      <PaginateNav :paginationData="paginationData" @update-data="updatePaginationList" />
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, JOURNEY_URL_PARAMETER } from "@/config/apiConfig";
// import CopyContentClipboard from "@/components/CopyContentClipboard.vue";
import { getColorClassForName, trimName } from "@/utils/card/cardUtils";
import { displayLocalTime } from "@/utils/date/dateUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import { formatTimeToLocal } from "@/utils/date/dateUtils";
import { convertDateTimeForServer } from "@/utils/date/dateUtils";
import { formatDuration } from "@/utils/date/dateUtils";
import { mapMutations } from 'vuex';
import DateEditableInput from "../fields/datetime/DateTimeEditableInput.vue";
import TimeEditableInput from "@/components/forms/inputs/time/TimeEditableInput.vue";
import JourneyCreateForm from "@/components/forms/JourneyCreateForm.vue";
import PaginateNav from "@/components/layout/PaginateNav.vue";

export default {
  name: "JourneysList",
  components: {
    DateEditableInput,
    JourneyCreateForm,
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
  },
  data() {
    return {
      // journeys: [],
      localJourneys: this.journeys,
      messageStatus: "",
      messageText: "",
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
  emits: ["new-journey-event", "journey-updated", "journey-deleted"],
  methods: {
    convertDateTimeForServer,
    displayLocalTime,
    formatDateBr,
    formatTimeToLocal,
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
    // getJourneys() {
    //   axios
    //     .get(`${BACKEND_URL}${JOURNEY_URL}`)
    //     .then((response) => {
    //       this.journeys = response.data.data;
    //     })
    //     .catch((error) => console.log(error));
    // },
    emitLastJourneyEnd() {
      if (this.journeys.length === 0) {
        this.$emit('last-journey-end', null)
        return null;
      }

      const sortedJourneys = [...this.journeys].sort((a, b) => {
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
      const endValue = this.convertDateTimeForServer(now);
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
      const index = this.journeys.findIndex(journey => journey.id === updatedJourney.id);
      if (index !== -1) {
        this.localJourneys.splice(index, 1, updatedJourney);
      }
    },
    updatePaginationList(newData) {
      this.localJourneys = newData;
    },
  },
  watch: {
    journeys: {
      handler(newVal) {
        this.localJourneys = newVal;
      },
      deep: true,
    },
  },
};
</script>

<style scoped>
.details {
  text-align: left;
  font-size: 1rem;
  font-weight: 400;
}

.time {
  text-align: center;
  font-size: 16px;
  font-weight: 400;
}

.time-bold {
  text-align: center;
  font-size: 16px;
  font-weight: 800;
  color: var(--purple);
}

.big-icon {
  font-size: 2.6rem;
  text-align: center;
  font-weight: 400;
  background-color: var(--gray);
  color: white;
}

.icon-clock {
  font-size: 1.8rem;
  font-weight: 400;
  color: var(--gray);
}

.icon-clock-new {
  font-size: 26px;
  font-weight: 900;
  color: white;
}

icon-clock-new:hover {
  font-size: 2.5rem;
  text-align: center;
  font-weight: 400;
  color: var(--purple);
}


.journey-container {
  border-style: solid;
  border-width: 2px;
  border-color: var(--primary);
  border-radius: 14px;
  padding: 1rem;
}



.comments {
  text-align: left;
  font-size: 14px;
  margin-top: 20px;
}

a {
  color: rgb(61, 61, 61);
}

a:link {
  text-decoration: none;
}

a:visited {
  text-decoration: none;
}

a:hover {
  text-decoration: none;
}

a:active {
  text-decoration: none;
}


.new {
  display: flex;
  justify-content: end;
  padding-top: 1rem;
}

.col-big {
  flex: 6;
}

.col-medium {
  flex: 2;
}

/* paginate */
/* Adapte as cores e estilos conforme necessário */

.pagination {
  display: flex;
  list-style: none;
  padding: 0;
}

.page-item {
  margin-right: 5px;
}

.page-link {
  display: block;
  padding: 10px;
  background-color: #3498db;
  color: #fff;
  text-align: center;
  text-decoration: none;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s;
}

.page-link:hover {
  background-color: #2980b9;
}

.page-item-disabled .page-link {
  background-color: #ccc;
  color: #666;
  cursor: not-allowed;
}

.prev-next-link {
  display: block;
  padding: 10px;
  background-color: #3498db;
  color: #fff;
  text-align: center;
  text-decoration: none;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s;
}

.prev-next-link:hover {
  background-color: #2980b9;
}

.prev-next-link-disabled {
  background-color: #ccc;
  color: #666;
  cursor: not-allowed;
}

.taskCard {
  background-color: var(--purple-light);
  color: black;
  border-radius: 12px;
  padding: 10px;
  margin-bottom: 10px;
  font-weight: 600;
  text-align: left;
}

.title {
  text-align: left;
}
</style>
