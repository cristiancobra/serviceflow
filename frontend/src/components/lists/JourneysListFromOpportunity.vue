<template>
  <div class="mt-3 mb-10">
    <AddMessage
      v-if="messageStatus"
      :messageStatus="messageStatus"
      :messageText="messageText"
    >
    </AddMessage>

    <div class="" v-if="localJourneys.length > 0">
      <div class="">
        <PaginateNav
          :paginationData="paginationData"
          @update-data="updatePaginationList"
        />
      </div>
      <div class="p-5" v-if="!localJourneys">
        Ainda não possui nenhuma jornada
      </div>
      <div
        v-else
        class="flex items-center mt-2 mb-2 ml-6 px-4 py-1 bg-info/10 border-l-4 border-info rounded-r-lg shadow-sm hover:bg-info/10 transition-colors duration-200"
        v-for="journey in localJourneys"
        v-bind:key="journey.id"
        :class="{ highlight: journey.id === newJourneyId }"
      >

        <div class="flex items-center mr-5">
          <img
            v-if="journey.user?.photo"
            :src="`${imagesPath}${journey.user.photo}`"
            :alt="journey.user.name"
            class="user-image mr-2"
          />
          <template v-else>
            <font-awesome-icon icon="fa-solid fa-user" class="text-info mr-2" />
            <span class="font-semibold text-info">{{ journey.user?.name || 'Usuário' }}</span>
          </template>
        </div>
        
        <div class="flex items-center mr-5">
          <font-awesome-icon icon="fa-solid fa-play" class="text-info mr-2" />
          <DateEditableInput
            name="start"
            v-model="journey.start"
            @save="updateJourney('start', $event, journey.id)"
          />
        </div>
        <div class="flex items-center mr-5">
          <font-awesome-icon icon="fa-solid fa-stop" class="text-info mr-2" />
          <TimeEditableInput
            class="ps-5"
            name="end"
            v-model="journey.end"
            @save="updateJourney('end', $event, journey.id)"
          />
        </div>
        <div class="flex items-center mr-5">
          <font-awesome-icon icon="fa-solid fa-hourglass-half" class="text-info mr-2" />
          <p class="time-bold ps-5">
            {{ formatDuration(journey.duration) }}
          </p>
        </div>
        <div class="flex items-center mr-5 text-base-content/70 min-w-0">
          <TextEditableField
            name="details"
            class="text-sm truncate"
            v-model="journey.details"
            emptyText="sem detalhamento"
            @save="updateJourney('details', $event, journey.id)"
          />
        </div>
        <div class="flex items-center mr-5 ml-auto gap-2">
          <journey-toggle-button
            v-if="!journey.end"
            running
            size="w-8 h-8"
            icon-size=""
            @stop="stopJourney(journey.id)"
          />
          <delete-icon-button
            size="w-8 h-8"
            title="Excluir jornada"
            confirm-message="Tem certeza que deseja excluir esta jornada?"
            @confirm="deleteItem(journey)"
          />
        </div>
      </div>
      <PaginateNav
        :paginationData="paginationData"
        @update-data="updatePaginationList"
      />
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, JOURNEY_URL_PARAMETER, IMAGES_PATH } from "@/config/apiConfig";
import { getColorClassForName, trimName } from "@/utils/card/cardUtils";
import { displayLocalTime } from "@/utils/date/dateUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import { formatDuration } from "@/utils/date/dateUtils";
import { mapMutations } from "vuex";
import DateEditableInput from "../fields/datetime/DateTimeEditableInput.vue";
import TimeEditableInput from "@/components/forms/inputs/time/TimeEditableInput.vue";
import PaginateNav from "@/components/layout/PaginateNav.vue";
import TextEditableField from "@/components/fields/text/TextEditableField.vue";
import DeleteIconButton from "@/components/buttons/DeleteIconButton.vue";
import JourneyToggleButton from "@/components/buttons/JourneyToggleButton.vue";

export default {
  name: "JourneysList",
  components: {
    DateEditableInput,
    TextEditableField,
    PaginateNav,
    TimeEditableInput,
    DeleteIconButton,
    JourneyToggleButton,
  },
  props: {
    journeys: {
      type: Array,
      default: () => [],
    },
    template: {
      type: String,
    },
    newJourneyId: {
      type: [String, Number],
      default: null,
    },
    taskId: {
      type: [String, Number],
      default: null,
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
    };
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
      this.$emit("update-task-duration");
      this.emitLastJourneyEnd();
    },
    cancelEditDateTime(event, journey) {
      // Verifica se a tecla pressionada é "Esc" (código 27)
      if (event.keyCode === 27) {
        journey.editing = false;
        // Remove o event listener após cancelar a edição
        document.removeEventListener("keydown", (event) =>
          this.cancelEditDateTime(event, journey)
        );
      }
    },
    deleteItem(item) {
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
        this.$emit("last-journey-end", null);
        return null;
      }

      const sortedJourneys = [...this.journeys].sort((a, b) => {
        if (a.end < b.end) return -1;
        if (a.end > b.end) return 1;
        return 0;
      });

      const lastJourney = sortedJourneys[sortedJourneys.length - 1];

      if (!lastJourney.end) {
        this.$emit("last-journey-end", null);
        return null;
      }

      try {
        this.$emit("last-journey-end", lastJourney.end);
      } catch (error) {
        console.error("Erro ao converter a data:", error);
        return null;
      }
    },
    ...mapMutations(["setOpenJourney"]),
    async stopJourney(journeyId) {
      const fieldName = "end";
      const now = new Date();
      // Enviar no formato ISO (JavaScript) - o backend converterá com convertJavascriptDate
      const endValue = now.toISOString();
      this.updateJourney(fieldName, endValue, journeyId);
      this.$store.commit("setOpenJourney", null);
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
        this.$emit("update-task-duration", this.taskId);
        this.emitLastJourneyEnd();
      } catch (error) {
        console.error("Erro ao atualizar a jornada:", error);
      }
    },
    updateJourneyInList(updatedJourney) {
      const index = this.journeys.findIndex(
        (journey) => journey.id === updatedJourney.id
      );
      if (index !== -1) {
        this.localJourneys.splice(index, 1, updatedJourney);
      }
    },
    updatePaginationList(newData) {
      this.localJourneys = newData;
    },
  },
  mounted() {
    // Logs removidos - dados agora chegam corretamente
  },
  watch: {
    journeys: {
      handler(newVal) {
        this.localJourneys = newVal;
      },
      deep: true,
      immediate: true,
    },
  },
  computed: {
    imagesPath() {
      return IMAGES_PATH;
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
  color: var(--color-primary);
}

.big-icon {
  font-size: 2.6rem;
  text-align: center;
  font-weight: 400;
  background-color: color-mix(in oklab, var(--color-base-content) 50%, transparent);
  color: white;
}

.journey-container {
  border-style: solid;
  border-width: 2px;
  border-color: var(--color-primary);
  border-radius: 14px;
  padding: 1rem;
}

.comments {
  text-align: left;
  font-size: 14px;
  margin-top: 20px;
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
  background-color: var(--color-info);
  color: #fff;
  text-align: center;
  text-decoration: none;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s;
}

.page-link:hover {
  background-color: var(--color-info);
}

.page-item-disabled .page-link {
  background-color: var(--color-base-300);
  color: color-mix(in oklab, var(--color-base-content) 60%, transparent);
  cursor: not-allowed;
}

.prev-next-link {
  display: block;
  padding: 10px;
  background-color: var(--color-info);
  color: #fff;
  text-align: center;
  text-decoration: none;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s;
}

.prev-next-link:hover {
  background-color: var(--color-info);
}

.prev-next-link-disabled {
  background-color: var(--color-base-300);
  color: color-mix(in oklab, var(--color-base-content) 60%, transparent);
  cursor: not-allowed;
}

.highlight {
  background-color: color-mix(in oklab, var(--color-warning) 15%, var(--color-base-100));
  animation: highlightPulse 2s ease-in-out;
  border-left: 4px solid var(--color-warning);
  /* padding-left: 12px; */
}

.user-image {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
}

@keyframes highlightPulse {
  0% {
    background-color: color-mix(in oklab, var(--color-warning) 15%, var(--color-base-100));
    box-shadow: 0 0 15px color-mix(in oklab, var(--color-warning) 80%, transparent);
  }
  50% {
    background-color: color-mix(in oklab, var(--color-warning) 15%, var(--color-base-100));
    box-shadow: 0 0 25px color-mix(in oklab, var(--color-warning) 60%, transparent);
  }
  100% {
    background-color: color-mix(in oklab, var(--color-warning) 15%, var(--color-base-100));
    box-shadow: 0 0 0 color-mix(in oklab, var(--color-warning) 0%, transparent);
  }
}

</style>
