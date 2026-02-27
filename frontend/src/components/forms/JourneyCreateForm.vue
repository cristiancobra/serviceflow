<template>
  <div class="journey-form-container pt-4 pb-4 mt-3 mb-3 px-4 bg-orange-50 border-l-4 border-orange-500 rounded-r-lg shadow-sm">
    <AddMessage
      :messageStatus="messageStatus"
      :messageText="messageText"
      @update:messageStatus="messageStatus = $event"
    />

    <div class="errorBox" v-bind:class="{ isActive: datesError }"></div>
    
    <form @submit.prevent="submitForm">
      <div class="flex items-center gap-4">
        <div class="flex-1">
          <label for="start" class="block text-sm font-semibold text-orange-700 mb-1">Início</label>
          <VueDatePicker v-model="form.start" class="w-full" />
        </div>
        <div class="flex-1">
          <label for="end" class="block text-sm font-semibold text-orange-700 mb-1">Fim</label>
          <VueDatePicker v-model="form.end" class="w-full" />
        </div>
        <div class="flex-[2]">
          <label for="details" class="block text-sm font-semibold text-orange-700 mb-1">Detalhes</label>
          <textarea
            name="description"
            rows="1"
            v-model="form.details"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
            id="details"
            placeholder="Adicione detalhes da jornada..."
          ></textarea>
        </div>
        <div class="flex items-end gap-2">
          <button 
            type="submit" 
            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition font-semibold"
          >
            Salvar
          </button>
          <button 
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold"
          >
            Cancelar
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import AddMessage from "@/components/forms/messages/AddMessage.vue";
import { BACKEND_URL, JOURNEY_URL } from "@/config/apiConfig";
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
  emits: ["new-journey-event", "close"],
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
      isActive: false,
      datesError: false,
      enableTime: true,
      newJourney: null,
      isFirstStartChange: true,
    };
  },
  methods: {
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
        const formWithTimezone = {
          ...this.form,
          user_timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
        };

        axios
          .post(`${BACKEND_URL}${JOURNEY_URL}`, formWithTimezone)
          .then((response) => {
            this.newJourney = response.data.data;
            this.$emit("new-journey-event", {
              journey: this.newJourney,
              taskId: this.taskId,
            });
            this.clearForm();
            this.$emit("close");
            this.setMessageStatus("success");
            if (this.newJourney.end == null) {
              this.setOpenJourney(this.newJourney.task);
            }
          });
      } catch (error) {
        this.setMessageStatus("error");
      }
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
.journey-form-container {
  width: 100%;
}

.errorBox {
  display: none;
}

.errorBox.isActive {
  display: block;
  color: red;
  padding: 10px;
  background-color: #ffe6e6;
  border: 1px solid red;
  border-radius: 5px;
  margin-bottom: 10px;
}
</style>
