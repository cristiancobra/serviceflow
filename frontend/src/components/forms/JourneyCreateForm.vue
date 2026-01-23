<template>
  <div>
    <AddMessage
      :messageStatus="messageStatus"
      :messageText="messageText"
      @update:messageStatus="messageStatus = $event"
    />

    <div class="errorBox" v-bind:class="{ isActive: datesError }"></div>
    <div class="form" v-bind:class="{ isActive: isActive }">
      <form @submit.prevent="submitForm">
        <div class="row mt-0">
          <div class="col">
            <label for="start">Início</label>
            <VueDatePicker v-model="form.start" />
          </div>
          <div class="col">
            <label for="end">Fim</label>
            <VueDatePicker v-model="form.end" />
          </div>
        </div>
        <div class="row">
          <div class="col">
            <label for="details">Detalhes</label>
            <textarea
              name="description"
              rows="2"
              cols="50"
              v-model="form.details"
              class="form-control"
              id="details"
            ></textarea>
          </div>
        </div>
        <div class="row d-flex justify-content-center">
          <button type="submit mt-5" class="button-new orange">Enviar</button>
        </div>
      </form>
    </div>
    <div class="flex flex-row items-center space-x-2 me-3">
      <button
        class="w-7 h-7 flex items-center justify-center rounded-full bg-primary text-white hover:bg-secondary transition duration-200 ease-in-out"
        @click="toggle()"
      >
        <font-awesome-icon icon="fa-solid fa-plus" />
      </button>
      <form @submit.prevent="submitQuickForm">
        <button
          class="w-7 h-7 flex items-center justify-center rounded-full bg-primary text-white hover:bg-secondary transition duration-200 ease-in-out"
        >
          <font-awesome-icon icon="fa-solid fa-bolt" />
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import AddMessage from "@/components/forms/messages/AddMessage.vue";
import { BACKEND_URL, JOURNEY_URL } from "@/config/apiConfig";
import { convertDateTimeForServer } from "@/utils/date/dateUtils";
import { mapMutations } from "vuex";
import axios from "axios";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

export default {
  name: "JourneyCreateForm",
  components: {
    VueDatePicker,
    AddMessage,
  },
  props: {
    taskId: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      form: {
        task_id: this.taskId,
        details: null,
        start: null,
        end: null,
      },
      messageStatus: "",
      messageText: "",
      quickForm: {
        task_id: this.taskId,
      },
      isActive: false,
      datesError: false,
      enableTime: true,
      newJourney: null,
      isFirstStartChange: true,
    };
  },
  methods: {
    convertDateTimeForServer,
    clearForm() {
      this.form.details = null;
      this.form.start = null;
      this.form.end = null;
      this.isFirstStartChange = true;
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
    handleStartChange(newVal) {
      if (this.isFirstStartChange && newVal) {
        this.form.end = newVal;
        this.isFirstStartChange = false;
      }
    },
    ...mapMutations(["setOpenJourney"]),
    setMessageStatus(status) {
      this.messageStatus = status;

      if (status === "error") {
        this.messageText = "Erro ao adicionar jornada!";
      } else if (status === "success") {
        this.messageText = "Jornada adicionada com sucesso!";
      }

      setTimeout(() => {
        this.messageStatus = "";
      }, 20000);
    },
    async submitForm() {
      this.checkDates();

      try {
        // Adicionar timezone do navegador
        const formWithTimezone = {
          ...this.form,
          user_timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
        };

        axios
          .post(`${BACKEND_URL}${JOURNEY_URL}`, formWithTimezone)
          .then((response) => {
            this.newJourney = response.data.data;
            this.$emit("new-journey-event", this.newJourney);
            this.clearForm();
            this.toggle();
            this.setMessageStatus("success");
            if (this.newJourney.end == null) {
              console.log("form-end", this.form.end);
              this.setOpenJourney(this.newJourney.task);
            }
          });
      } catch (error) {
        this.setMessageStatus("error");
      }
    },
    async submitQuickForm() {
      try {
        this.quickForm.start = new Date();
        this.quickForm.task_id = this.taskId;
        
        // Adicionar timezone do navegador
        const quickFormWithTimezone = {
          ...this.quickForm,
          user_timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
        };

        axios
          .post(`${BACKEND_URL}${JOURNEY_URL}`, quickFormWithTimezone)
          .then((response) => {
            this.newJourney = response.data.data;
            this.$emit("new-journey-event", {
              journey: this.newJourney,
              taskId: this.taskId,
            });
            this.setMessageStatus("success");
            this.setOpenJourney(this.newJourney.task);
          });
      } catch (error) {
        this.setMessageStatus("error");
      }
    },
    toggle() {
      this.isActive = !this.isActive;
    },
  },
  watch: {
    "form.start": function (newVal) {
      this.handleStartChange(newVal);
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

.form.isActive {
  display: flex;
}

.icon {
  font-size: 1.8rem;
  text-align: center;
  font-weight: 400;
}

.icon-col {
  font-size: 16px;
  display: inline-block;
  align-items: center;
  /* Centraliza verticalmente */
  justify-content: center;
  /* Centraliza horizontalmente */
  width: 35px;
  height: 35px;
  margin-right: 12px;
  margin-top: -8px;
  padding: 10px;
  background-color: #f1f1f1;
  border-radius: 50%;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  /* Reduz a intensidade do sombreamento */
  transition: font-size 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.icon-col:hover {
  font-size: 20px;
  background-color: #f6f6f6;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
  transform: perspective(500px) rotateX(10deg);
  transform-origin: center center;
  /* Inicia a transformação a partir do centro */
}

.comments {
  text-align: left;
  font-size: 14px;
  margin-top: 20px;
}

.form {
  display: none;
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
