<template>
  <div class="row">
    <div class="table-header row">
      <div class="offset-1 col-1">DATA</div>
      <div class="col-6">OBSERVAÇÕES</div>
      <div class="col-1">INÍCIO</div>
      <div class="col-1">FIM</div>
      <div class="col-1">DURAÇÃO</div>
      <div class="col-1"></div>
    </div>

    <div class="p-5" v-if="journeys.length === 0">
      Ainda não possui nenhuma jornada
    </div>

    <div
      v-else
      class="row list-line"
      v-for="journey in journeys"
      v-bind:key="journey.id"
    >
      <div class="col-1 icon">
        <font-awesome-icon icon="fa-solid fa-clock" />
      </div>

      <div id="date" class="col-1" style="position: relative">
        <p class="start">
          {{ formatDateBr(journey.start) }}
        </p>
      </div>

      <div
        id="details"
        class="col-6"
        style="position: relative"
        @mouseover="showContextMenu(journey, 'details')"
        @mouseleave="hideContextMenu(journey, 'details')"
      >
        <p
          class="details"
          v-if="
            !journey.editing ||
            (journey.editing && journey.editingField !== 'details')
          "
        >
          {{ journey.details }}
        </p>
        <input
          v-else-if="journey.editing && journey.editingField === 'details'"
          type="text"
          class="form-control"
          v-model="journey.details"
          @keydown.enter="saveJourney(journey, 'details')"
          @keydown.esc="cancelEdit(journey)"
        />
        <div
          class="small-menu"
          v-show="journey.showContextMenu && journey.activeField === 'details'"
        >
          <div class="icon-col small-menu-item">
            <span class="icon" @click="startEdit(journey, 'details')">
              <font-awesome-icon icon="fa-solid fa-pencil" />
            </span>
          </div>

          <CopyContentClipboard :data="journey.details" />
        </div>
      </div>

      <div
        id="start"
        class="col-1"
        style="position: relative"
        @mouseover="showContextMenu(journey, 'start')"
        @mouseleave="hideContextMenu(journey, 'start')"
      >
        <p
          class="time"
          v-if="
            !journey.editing ||
            (journey.editing && journey.editingField !== 'start')
          "
        >
          {{ formatTimeBr(journey.start) }}
        </p>
        <input
          v-else-if="journey.editing && journey.editingField === 'start'"
          type="text"
          class="form-control"
          v-model="journey.start"
          @keydown.enter="saveJourney(journey, 'start')"
          @keydown.esc="cancelEdit(journey)"
        />
        <div
          class="small-menu"
          v-show="journey.showContextMenu && journey.activeField === 'start'"
        >
          <div class="icon-col small-menu-item">
            <span class="icon" @click="startEdit(journey, 'start')">
              <font-awesome-icon icon="fa-solid fa-pencil" />
            </span>
          </div>

          <CopyContentClipboard :data="journey.start" />
        </div>
      </div>

      <div
        id="end"
        class="col-1"
        style="position: relative"
        @mouseover="showContextMenu(journey, 'end')"
        @mouseleave="hideContextMenu(journey, 'end')"
      >
        <p
          class="time"
          v-if="
            (!journey.editing ||
              (journey.editing && journey.editingField !== 'end')) &&
            journey.end !== null &&
            journey.end !== ''
          "
        >
          {{ formatTimeBr(journey.end) }}
        </p>
        <input
          v-else-if="journey.editing && journey.editingField === 'end'"
          type="text"
          class="form-control"
          v-model="journey.end"
          @keydown.enter="saveJourney(journey, 'end')"
          @keydown.esc="cancelEdit(journey)"
        />
        <div
          class="small-menu"
          v-show="journey.showContextMenu && journey.activeField === 'end'"
        >
          <div class="icon-col small-menu-item">
            <span class="icon" @click="endEdit(journey, 'end')">
              <font-awesome-icon icon="fa-solid fa-pencil" />
            </span>
          </div>

          <CopyContentClipboard :data="journey.end" />
        </div>
      </div>

      <div id="duration" class="col-1">
        <p
          class="time-bold"
          v-if="journey.duration !== null && journey.duration !== ''"
        >
          {{ formatDuration(journey.duration) }}
        </p>
      </div>

      <div id="stop-button" class="col-1">
        <button v-if="!journey.end" class="button-stop" @click="stopJourney(journey)">
          <span class="icon-stop">
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
import CopyContentClipboard from "@/components/CopyContentClipboard.vue";
import { formatDateBr } from "@/utils/date/dateUtils";
import { formatTimeBr } from "@/utils/date/dateUtils";
import { formatDuration } from "@/utils/date/dateUtils";
import JourneyCreateForm from "@/components/forms/JourneyCreateForm.vue";

export default {
  name: "JourneysList",
  components: {
    CopyContentClipboard,
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
  emits: ["new-journey-event", "journey-updated"],
  methods: {
    formatDateBr,
    formatTimeBr,
    formatDuration,
    showContextMenu(journey, field) {
      journey.showContextMenu = true;
      journey.activeField = field;
    },
    hideContextMenu(journey) {
      journey.showContextMenu = false;
      journey.activeField = "";
    },
    startEdit(journey, field) {
      journey.editing = true;
      journey.editingField = field;
    },
    cancelEdit(journey) {
      journey.editing = false;
      journey.editingField = null;
    },
    cancelEditOnEsc(event) {
      if (event.key === "Escape") {
        const journey = this.journeys.find((journey) => journey.editing);
        if (journey) {
          this.cancelEdit(journey);
        }
      }
    },
    saveJourney(journey, field) {
      const updatedJourney = { ...journey };

      if (journey.activeField === field) {
        journey.editing = false;
        journey.editingField = null;

        this.updatedJourney.id = journey.id;
        this.updatedJourney.details = journey.details;
        this.updatedJourney.start = journey.start;
        this.updatedJourney.end = journey.end;

        axios
          .put(
            `http://localhost:8191/api/journeys/${journey.id}`,
            this.updatedJourney
          )
          .then((response) => {
            this.editedJourneys = response.data.data;
            this.editedJourneys.push(updatedJourney);
            this.$emit("journey-updated", updatedJourney);
          });
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
    stopJourney(journey) {
      this.stopedJourney.id = journey.id;
      this.stopedJourney.end = new Date();

      axios
        .put(`http://localhost:8191/api/journeys/${journey.id}`, this.stopedJourney)
        .then((response) => {
          this.updatedJourney = response.data.journey;
          this.$emit("journey-updated", this.updatedJourney);
        });
    },
    addJourneyCreated(newJourney) {
      // Add the new journey to the journeysData array
      this.$emit("new-journey-event", newJourney);
    },
  },
  mounted() {
    window.addEventListener("keydown", this.cancelEditOnEsc);
  },
  beforeUnmount() {
    window.removeEventListener("keydown", this.cancelEditOnEsc);
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
  color: var(--roxo);
}
.icon {
  font-size: 1.8rem;
  text-align: center;
  font-weight: 400;
  color: var(--roxo);
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
  color: var(--roxo);
}
.icon-stop {
  font-size: 24px;
  font-weight: 900;
  color: var(--blue);
}
.icon-col {
  font-size: 16px;
  display: inline-block;
  align-items: center; /* Centraliza verticalmente */
  justify-content: center; /* Centraliza horizontalmente */
  width: 35px;
  height: 35px;
  margin-right: 12px;
  margin-top: -8px;
  padding: 10px;
  background-color: #f1f1f1;
  border-radius: 50%;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Reduz a intensidade do sombreamento */
  transition: font-size 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}
.icon-col:hover {
  font-size: 20px;
  background-color: #f6f6f6;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
  transform: perspective(500px) rotateX(10deg);
  transform-origin: center center; /* Inicia a transformação a partir do centro */
}
.button-stop {
  width: 40px;
  height: 40px;
  padding: 0;
  text-align: center;
  border-style: solid;
  border-radius: 50%;
  background-color: var(--blue-light);
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
  color: var(--blue);
}
.button-stop:hover {
  border-color: var(--blue);
  background-color: var(--blue);
}
.button-stop:hover .icon-stop {
  color: white;
}
.comments {
  text-align: left;
  font-size: 14px;
  margin-top: 20px;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
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
.slot {
  display: block;
  text-align: center;
  border-style: solid;
  border-width: 2px;
  border-color: #007e8b;
  border-radius: 0 6px 6px 0;
  padding-top: 6px;
  padding-bottom: 6px;
  background-color: pink;
}
.slot.active {
  border-left-style: none;
  background-color: white;
}
.slot.inactive {
  background-color: lightgray;
}
.slots-column {
  margin-left: -12px;
}
.small-menu {
  position: absolute;
  top: 0;
  right: 0;
  display: block;
  z-index: 1;
  /* box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); */
  padding: 0px;
}
.small-menu.show {
  display: none;
}
.table-header {
  display: flex;
  text-align: center;
  background-color: var(--roxo);
  color: white;
  border-style: none;
  border-radius: 20px;
  padding-top: 10px;
  padding-left: 20px;
  padding-bottom: 10px;
}
</style>
