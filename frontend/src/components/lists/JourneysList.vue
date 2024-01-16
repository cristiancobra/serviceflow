<template>
  <div class="row">
    <div class="table-header row">
      <div class="offset-1 col-1">DATA</div>
      <div class="col-5">OBSERVAÇÕES</div>
      <div class="col-1">INÍCIO</div>
      <div class="col-1">FIM</div>
      <div class="col-1">DURAÇÃO</div>
      <div class="col-2">AÇÕES</div>
    </div>

    <div class="p-5" v-if="journeys.length === 0">
      Ainda não possui nenhuma jornada
    </div>

    <div
      v-else
      class="row journey-line"
      v-for="journey in journeys"
      v-bind:key="journey.id"
    >
      <div class="col-1 big-icon">
        <font-awesome-icon icon="fa-solid fa-clock" />
        {{ journey.id }}
      </div>

      <div id="date" class="col-1" style="position: relative">
        <p class="start">
          {{ formatDateBr(journey.start) }}
        </p>
      </div>

      <div id="details" class="col-5">
        <TextEditableInput
          name="details"
          class="details"
          v-model="journey.details"
          @save="updateJourney('details', $event, journey.id)"
        />
      </div>

      <div id="start" class="col-1">
        <p class="time" v-if="journey.start !== null && journey.start !== ''">
          {{ formatTimeBr(journey.start) }}
        </p>
      </div>

      <div id="end" class="col-1">
        <p class="time" v-if="journey.end !== null && journey.end !== ''">
          {{ formatTimeBr(journey.end) }}
        </p>
      </div>

      <div id="duration" class="col-1">
        <p
          class="time-bold"
          v-if="journey.duration !== null && journey.duration !== ''"
        >
          {{ formatDuration(journey.duration) }}
        </p>
      </div>

      <div id="stop-button" class="col-2 text-end">
        <button class="button delete" @click="deleteJourney(journey)">
          <span class="delete">
            <font-awesome-icon icon="fa-solid fa-trash-alt" />
          </span>
        </button>

        <button
          v-if="!journey.end"
          class="button stop"
          @click="stopJourney(journey)"
        >
          <span class="stop">
            <font-awesome-icon icon="fa-solid fa-hand" />
          </span>
        </button>
      </div>

      <router-view />
    </div>

    <JourneyCreateForm @new-journey-event="addJourneyCreated" />
  </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, JOURNEY_URL } from "@/config/apiConfig";
// import CopyContentClipboard from "@/components/CopyContentClipboard.vue";
import { formatDateBr } from "@/utils/date/dateUtils";
import { formatTimeBr } from "@/utils/date/dateUtils";
import { formatDuration } from "@/utils/date/dateUtils";
import JourneyCreateForm from "@/components/forms/JourneyCreateForm.vue";
import TextEditableInput from "@/components/forms/inputs/text/TextEditableInput.vue";

export default {
  name: "JourneysList",
  components: {
    // CopyContentClipboard,
    TextEditableInput,
    JourneyCreateForm,
  },
  props: ["journeys"],
  data() {
    return {
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
      editedJourneys: this.journeys.map((journey) => ({ ...journey })), // Crie uma cópia dos dados para editar
    };
  },
  emits: ["new-journey-event", "journey-updated", "journey-deleted"],
  methods: {
    formatDateBr,
    formatTimeBr,
    formatDuration,
    // saveJourney(journey, field) {
    //   const updatedJourney = { ...journey };

    //   if (journey.activeField === field) {
    //     journey.editing = false;
    //     journey.editingField = null;

    //     this.updatedJourney.id = journey.id;
    //     this.updatedJourney.details = journey.details;
    //     this.updatedJourney.start = journey.start;
    //     this.updatedJourney.end = journey.end;

    //     axios
    //       .put(
    //         `http://localhost:8191/api/journeys/${journey.id}`,
    //         this.updatedJourney
    //       )
    //       .then((response) => {
    //         this.editedJourneys = response.data.data;
    //         this.editedJourneys.push(updatedJourney);
    //         this.$emit("journey-updated", updatedJourney);
    //       });
    //   }
    // },
    async updateJourney(fieldName, editedValue, journeyId) {
      const editedJourney = {
        id: journeyId,
        [fieldName]: editedValue,
      };

      try {
        const response = await axios.put(
          `${BACKEND_URL}${JOURNEY_URL}/${journeyId}`,
          editedJourney
        );

        this.journey = response.data.data;

        this.$emit("journey-updated", this.journey);
      } catch (error) {
        console.error("Erro ao atualizar a jornada:", error);
      }
    },
    createJourney() {
      const taskId = {
        task_id: this.$route.params.id,
      };

      axios
        .post("http://localhost:8191/api/journeys", taskId)
        .then((response) => {
          this.data = response.data;
          this.newJourneyEvent(this.data);
          // this.clearForm();
        });
    },
    deleteJourney(journeyToDelete) {
      const confirmation = confirm(
        "Tem certeza que deseja excluir esta jornada?"
      );

      if (confirmation) {
        axios
          .delete(`http://localhost:8191/api/journeys/${journeyToDelete.id}`)
          .then((response) => {
            if (response.status === 200) {
              this.$emit("journey-deleted", journeyToDelete.id);
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
    // emitUpdateEvent(updatedJourney) {
    //   const updatedJourneyIndex = this.editedJourneys.findIndex(
    //     (journey) => journey.id === updatedJourney.id
    //   );

    //   if (updatedJourneyIndex !== -1) {
    //     this.$emit("journey-updated", updatedJourneyIndex, updatedJourney);
    //   }
    // },
    stopJourney(journey) {
      this.stopedJourney.id = journey.id;
      this.stopedJourney.end = new Date();

      axios
        .put(
          `http://localhost:8191/api/journeys/${journey.id}`,
          this.stopedJourney
        )
        .then((response) => {
          this.updatedJourney = response.data.journey;
          this.$emit("journey-updated", this.updatedJourney);
        });
    },
    addJourneyCreated(newJourney) {
      this.$emit("new-journey-event", newJourney);
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
</style>
