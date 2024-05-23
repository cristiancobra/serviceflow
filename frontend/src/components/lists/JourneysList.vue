<template>
  <div class="mb-5">

    <AddMessage v-if="messageStatus" :messageStatus="messageStatus" :messageText="messageText">
    </AddMessage>

    <JourneyCreateForm @new-journey-event="addJourneyCreated" />

    <div id="list" v-if="journeys.length > 0">
      <div class="row">
        <PaginateNav :paginationData="paginationData" @update-data="updatePaginationList" />
      </div>

      <div class="table-header">
        <div class="offset-1 col-1">DATA</div>
        <div class="col-5">OBSERVAÇÕES</div>
        <div class="col-1">INÍCIO</div>
        <div class="col-1">FIM</div>
        <div class="col-1">DURAÇÃO</div>
        <div class="col-2">AÇÕES</div>
      </div>

      <div class="p-5" v-if="!journeys">Ainda não possui nenhuma jornada</div>

      <div v-else class="line" v-for="journey in journeys" v-bind:key="journey.id">
        <div class="col-1 big-icon">
          <font-awesome-icon icon="fa-solid fa-clock" />
        </div>

        <div id="date" class="col-1" style="position: relative">
          <p class="start">
            {{ formatDateBr(journey.start) }}
          </p>
        </div>

        <div id="details" class="col-5">
          <TextEditableInput name="details" class="details" v-model="journey.details"
            @save="updateJourney('details', $event, journey.id)" />
        </div>

        <div id="start" class="col-1">
          <p class="time" v-if="journey.start !== null && journey.start !== ''">
            {{ displayLocalTime(journey.start) }}
          </p>
        </div>

        <div id="end" class="col-1">
          <p class="time" v-if="journey.end !== null && journey.end !== ''">
            {{ displayLocalTime(journey.end) }}
          </p>
        </div>

        <div id="duration" class="col-1">
          <p class="time-bold" v-if="journey.duration !== null && journey.duration !== ''">
            {{ formatDuration(journey.duration) }}
          </p>
        </div>

        <div id="stop-button" class="col-2 text-end">

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
import { formatDateTimeForServer } from "@/utils/date/dateUtils";
import { formatDuration } from "@/utils/date/dateUtils";
import JourneyCreateForm from "@/components/forms/JourneyCreateForm.vue";
import PaginateNav from "@/components/layout/PaginateNav.vue";
import TextEditableInput from "@/components/forms/inputs/text/TextEditableInput.vue";

export default {
  name: "JourneysList",
  components: {
    TextEditableInput,
    JourneyCreateForm,
    PaginateNav,
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
    };
  },
  emits: ["new-journey-event", "journey-updated", "journey-deleted"],
  methods: {
    displayLocalTime,
    formatDateBr,
    formatTimeToLocal,
    formatDuration,
    formatDateTimeForServer,
    addJourneyCreated(newJourney) {
      // Add the new journey to the journeysData array
      this.journeys.unshift(newJourney);
      this.$emit("update-task-duration");
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

        this.$emit("journey-updated", this.journey);
      } catch (error) {
        console.error("Erro ao atualizar a jornada:", error);
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

        this.journeys = response.data.data;

        this.paginationData = {
          links: response.data.links,
          meta: response.data.meta,
        };

      } catch (error) {
        console.error("Erro ao acessar jornadas:", error);
      }
    },
    async stopJourney(journey) {
      this.stopedJourney.id = journey.id;
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
  font-size: 16px;
  font-weight: 600;
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
  font-size: 1.8rem;
  text-align: center;
  font-weight: 400;
  color: var(--purple);
}

.button {
  width: 42px;
  height: 42px;
  padding: 0;
  margin-left: 14px;
  text-align: center;
  font-size: 20px;
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

.new {
  display: flex;
  justify-content: end;
  padding-top: 1rem;
}

.table-header {
  display: flex;
  text-align: center;
  background-color: var(--purple);
  color: white;
  border-style: none;
  border-radius: 20px;
  padding-top: 10px;
  padding-left: 20px;
  padding-bottom: 10px;
  margin-bottom: 16px;
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
</style>
