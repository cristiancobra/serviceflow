<template>
  <div class="journey-container mb-5 mt-0">

    <div class="row align-items-start">
      <div class="col-1">
        <font-awesome-icon icon="fa-solid fa-clock" class="icon"/>
      </div>
      <div class="col">
        <h2 class="title">JORNADAS</h2>
      </div>
    </div>

    <AddMessage v-if="messageStatus" :messageStatus="messageStatus" :messageText="messageText">
    </AddMessage>

    <JourneyCreateForm @new-journey-event="addJourneyCreated" />

    <div id="list" v-if="journeys.length > 0">
      <div class="row">
        <PaginateNav :paginationData="paginationData" @update-data="updatePaginationList" />
      </div>

      <div class="table-header">
        <div class="offset-2 col-4">INÍCIO</div>
        <div class="col-3">FIM</div>
        <div class="col-3">DURAÇÃO</div>
      </div>
      <div class="p-5" v-if="!journeys">Ainda não possui nenhuma jornada</div>
      <div v-else class="line row ms-1 me-1" v-for="journey in journeys" v-bind:key="journey.id"
        :class="{ 'highlight': journey.id === newJourneyId }">
        <div class="col-1 big-icon">
          <font-awesome-icon icon="fa-solid fa-clock" />
        </div>
        <div class="col-11">
          <div class="row pt-2">
            <div id="start" class="col-5" :class="{ 'col-big': journey.editing && journey.editing.start }"
              style="position: relative" @click="editDateTime(journey, 'start')">
              <DateEditableInput name="start" v-model="journey.start"
                @save="updateJourney('start', $event, journey.id)" />
            </div>
            <div id="end" class="col-5" :class="{ 'col-big': journey.editing && journey.editing.end }"
              style="position: relative" @click="editDateTime(journey, 'end')">
              <TimeEditableInput name="end" v-model="journey.end" @save="updateJourney('end', $event, journey.id)" />
            </div>
            <div id="duration" class="col-2">
              <p class="time-bold">
                {{ formatDuration(journey.duration) }}
              </p>
            </div>
          </div>
          <div class="row pb-2">
            <div id="details" class="col-9 big">
              <TextEditableInput name="details" class="details" v-model="journey.details"
                @save="updateJourney('details', $event, journey.id)" />
            </div>
            <div class="col-3 d-flex justify-content-end">
              <button v-if="!journey.end" class="button stop" @click="stopJourney(journey)">
                <span class="stop">
                  <font-awesome-icon icon="fa-solid fa-hand" />
                </span>
              </button>
              <button class="button delete" @click="deleteItem(journey)">
                <span class="delete">
                  <font-awesome-icon icon="fa-solid fa-trash-alt" />
                </span>
              </button>
            </div>
          </div>
        </div>
        <router-view />
      </div>

      <PaginateNav :paginationData="paginationData" @update-data="updatePaginationList" />

      <JourneyCreateForm @new-journey-event="addJourneyCreated" />

    </div>
  </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, JOURNEY_URL_PARAMETER, JOURNEY_BY_TASK_URL_QUERY, } from "@/config/apiConfig";
// import CopyContentClipboard from "@/components/CopyContentClipboard.vue";
import { displayLocalTime } from "@/utils/date/dateUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import { formatTimeToLocal } from "@/utils/date/dateUtils";
import { convertDateTimeForServer } from "@/utils/date/dateUtils";
import { formatDuration } from "@/utils/date/dateUtils";
import DateEditableInput from "../forms/inputs/date/DateEditableInput.vue";
import TimeEditableInput from "@/components/forms/inputs/time/TimeEditableInput.vue";
import JourneyCreateForm from "@/components/forms/JourneyCreateForm.vue";
import PaginateNav from "@/components/layout/PaginateNav.vue";
import TextEditableInput from "@/components/forms/inputs/text/TextEditableInput.vue";

export default {
  name: "JourneysList",
  components: {
    DateEditableInput,
    TextEditableInput,
    JourneyCreateForm,
    PaginateNav,
    TimeEditableInput,
  },
  props: ["taskId"],
  data() {
    return {
      journeys: [],
      messageStatus: "",
      messageText: "",
      updatedJourney: {
        id: null,
        details: null,
        start: null,
        end: null,
      },
      stopedJourney: {
        id: null,
        start: null,
        end: null,
      },
      editedJourneys: [],
      newJourneyId: null,
    };
  },
  emits: ["new-journey-event", "journey-updated", "journey-deleted"],
  methods: {
    displayLocalTime,
    formatDateBr,
    formatTimeToLocal,
    formatDuration,
    convertDateTimeForServer,
    addJourneyCreated(newJourney) {
      this.journeys.unshift(newJourney);
      this.highlightNewJourney(newJourney.id);
      this.$emit("update-task-duration");
    },
    highlightNewJourney(journeyId) {
      this.newJourneyId = journeyId;
      setTimeout(() => {
        this.newJourneyId = null;
      }, 2000);

    },
    async updateJourney(fieldName, editedValue, journeyId) {
      const editedJourney = {
        id: journeyId,
        task_id: this.$route.params.id,
        [fieldName]: editedValue,
      };

      try {
        const response = await axios.put(
          `${BACKEND_URL}${JOURNEY_URL_PARAMETER}${journeyId}`,
          editedJourney
        );

        this.updatedJourney = response.data.data;
        this.updateJourneyInList(this.updatedJourney);

        this.$emit("journey-updated", this.journey);
      } catch (error) {
        console.error("Erro ao atualizar a jornada:", error);
      }
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
      this.journeys = this.journeys.filter(
        (journey) => journey.id !== itemId
      );
    },
    async getJourneys(page = 1) {

      this.journeysUrl = `${BACKEND_URL}${JOURNEY_BY_TASK_URL_QUERY}task_id=${this.taskId}&per_page=10&page=${page}`;

      try {
        const response = await axios.get(this.journeysUrl);

        this.journeys = response.data.data.map(journey => {
          return { ...journey, editing: false }; // Adiciona a propriedade editing a cada journey
        });

        this.paginationData = {
          links: response.data.links,
          meta: response.data.meta,
        };

      } catch (error) {
        console.error("Erro ao acessar jornadas:", error);
      }
    },
    editDateTime(journey, field) {
      if (!journey.editing) {
        journey.editing = {};
      }
      journey.editing[field] = !journey.editing[field];

      if (journey.editing[field]) {
        document.addEventListener('keydown', (event) => this.cancelEditDateTime(event, journey, field));
      } else {
        document.removeEventListener('keydown', (event) => this.cancelEditDateTime(event, journey, field));
      }
    },
    async stopJourney(journey) {
      this.stopedJourney.id = journey.id;
      this.stopedJourney.task_id = this.$route.params.id;
      this.stopedJourney.start = journey.start;
      this.stopedJourney.end = new Date();

      try {
        const response = await axios.put(
          `${BACKEND_URL}${JOURNEY_URL_PARAMETER}${journey.id}`,
          this.stopedJourney
        );
        this.updatedJourney = response.data.data;

        this.updateJourneyInList(this.updatedJourney);
        this.$emit("journey-updated", this.updatedJourney);
      } catch (error) {
        console.error("Erro ao parar a jornada:", error);
      }
    },

    // Nova função para atualizar a lista de jornadas
    updateJourneyInList(updatedJourney) {
      const index = this.journeys.findIndex(journey => journey.id === updatedJourney.id);
      if (index !== -1) {
        this.journeys.splice(index, 1, updatedJourney);
      }
    },
    updatePaginationList(newData) {
      this.journeys = newData;
    },
  },
  watch: {
    taskId: {
      handler(dataLoaded) {
        if (dataLoaded) {
          this.getJourneys();
        }
      },
      immediate: true,
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

.icon {
  font-size: 1.8rem;
  font-weight: 400;
  color: var(--gray);
}

.button {
  width: 38px;
  height: 38px;
  padding: 0;
  margin-left: 14px;
  text-align: center;
  font-size: 18px;
  border-style: solid;
  border-width: 2px;
  border-radius: 50%;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
}

.icon-new {
  font-size: 26px;
  font-weight: 900;
  color: white;
}

icon-new:hover {
  font-size: 2.5rem;
  text-align: center;
  font-weight: 400;
  color: var(--purple);
}

.delete {
  font-weight: 900;
  color: var(--red);
  border-color: var(--red);
  background-color: var(--red-light);
}

.delete:hover {
  border-color: var(--red);
  background-color: var(--red);
}

.button:hover .delete {
  color: white;
  background-color: var(--red);
}

.journey-container {
  border-style: solid;
  border-color: var(--primary);
  border-radius: 14px;
  padding: 1rem;
}

.stop {
  font-weight: 900;
  color: var(--blue);
  border-color: var(--blue);
  background-color: var(--blue-light);
}

.stop:hover {
  border-color: var(--blue);
  background-color: var(--blue);
}

.button:hover .stop {
  color: white;
  background-color: var(--blue);
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

.highlight {
  animation: highlightAnimation 4s ease-out;
}

@keyframes highlightAnimation {
  0% {
    background-color: var(--done-color);
    border-color: var(--done-color);
    border-width: 5px;
  }

  100% {
    background-color: transparent;
  }
}

.highlight .big-icon {
  animation: highlightIcon 4s ease-out;
}

@keyframes highlightIcon {
  0% {
    color: white;
  }

  100% {
    color: var(--gray);
    /* ou a cor padrão que você deseja usar */
  }
}

.new {
  display: flex;
  justify-content: end;
  padding-top: 1rem;
}

.table-header {
  display: flex;
  justify-content: space-between;
  text-align: center;
  background-color: var(--purple);
  color: white;
  border-style: none;
  border-radius: 12px;
  padding-top: 10px;
  padding-bottom: 10px;
  margin-bottom: 16px;
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

.title {
  text-align: left;
}
</style>
