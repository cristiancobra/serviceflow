<template>
  <div class="container">
    <div class="row errorBox" v-bind:class="{ 'd-none': datesError }"></div>
    <form @submit.prevent="submitForm">
      <div class="row form" v-bind:class="{ 'd-none': isActive }">
        <div class="col-md-9 mb-3">
          <label for="details" class="form-label">Detalhes</label>
          <textarea
            name="description"
            rows="6"
            cols="50"
            v-model="form.details"
            class="form-control"
            id="details"
          ></textarea>
        </div>
        <div class="col-md-3 mb-3 text-center">
          <label for="start" class="form-label">Início</label>
          <VueDatePicker v-model="form.start" />
          <label for="end" class="form-label">Fim</label>
          <VueDatePicker v-model="form.end" />
          <br />
          <button type="submit mt-5" class="button-new orange">Enviar</button>
        </div>
      </div>
    </form>
    <div class="row">
      <div class="col-md-12 d-flex justify-content-end">
        <button class="button-new me-3" @click="toggle()">
          <span class="icon-new">
            <font-awesome-icon icon="fa-solid fa-plus" />
          </span>
          NOVA
        </button>
        <form @submit.prevent="submitQuickForm">
          <button class="button-new">
            <span class="icon-new">
              <font-awesome-icon icon="fa-solid fa-bolt" />
            </span>
            INICIAR AGORA
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { BACKEND_URL, JOURNEY_URL } from "@/config/apiConfig";
import axios from "axios";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

export default {
  name: "JourneyCreateForm",
  components: {
    VueDatePicker,
  },
  data() {
    return {
      form: {
        task_id: this.$route.params.id,
        details: null,
        start: null,
        end: null,
      },
      quickForm: {
        task_id: this.$route.params.id,
      },
      isActive: true,
      datesError: false,
      enableTime: true,
      newJourney: null,
    };
  },
  methods: {
    clearForm() {
      this.form.details = null;
      this.form.start = null;
      this.form.end = null;
    },
    checkDates() {
      if (this.form.end && this.form.start) {
        const endDate = new Date(this.form.end);
        const startDate = new Date(this.form.start);

        if (endDate > startDate) {
          this.datesError = true;
        } else {
          this.datesError = false;
        }
      }
    },
    convertToUTC(localDate) {
      // Converta uma data local para UTC
      const utcDate = new Date(localDate);
      const timezoneOffset = utcDate.getTimezoneOffset();
      utcDate.setMinutes(utcDate.getMinutes() - timezoneOffset); // Adicione o offset do fuso horário
      return utcDate;
    },
    newJourneyEvent(newJourney) {
      console.log(newJourney);
      this.$emit("new-journey-event", newJourney);
    },
    async submitForm() {
      this.form.start = this.convertToUTC(this.form.start);

      if (this.form.end) {
        this.form.end = this.convertToUTC(this.form.end);
      }

      this.checkDates();

      axios
        .post(`${BACKEND_URL}${JOURNEY_URL}`, this.form)
        .then((response) => {
          this.newJourney = response.data.journey;
          this.newJourneyEvent(this.newJourney);
          this.clearForm();
        });
    },
    async submitQuickForm() {
      axios
        .post(`${BACKEND_URL}${JOURNEY_URL}`, this.quickForm)
        .then((response) => {
          this.newJourney = response.data.journey;
          this.newJourneyEvent(this.newJourney);
        });
    },
    toggle() {
      this.isActive = !this.isActive;
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
.start {
  text-align: center;
  font-size: 16px;
  font-weight: 400;
}
.icon {
  font-size: 1.8rem;
  text-align: center;
  font-weight: 400;
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

.comments {
  text-align: left;
  font-size: 14px;
  margin-top: 20px;
}
.form {
  padding: 1rem;
  background-color: var(--orange-light);
  border-color: var(--orange);
  border-style: solid;
  border-radius: 6px;
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
.orange {
  background-color: var(--orange);
  color: white;
}
.row {
  margin-top: 2rem;
  justify-content: end;
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
}
</style>
