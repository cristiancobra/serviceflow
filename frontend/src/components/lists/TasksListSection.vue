<template>
  <div class="section-container">
    <div class="section-header">
      <div class="section-title">
        <font-awesome-icon icon="fa-solid fa-tasks" class="icon" />
        <h2>TAREFAS</h2>
      </div>
      <div class="section-action">
        <button-new-form target="task" @open-modal="handleOpenModal" />
        <task-create-form
        v-model="openTaskForm"
        @new-task-event="addTaskCreated"
        @update:modelValue="openModal = null"
         />
      </div>
    </div>

    <section class="section-container">
      <SearchInput v-model="searchTerm" placeholder="Buscar tarefas" />

      <section class="">
        <div v-for="localTask in filteredTasks" v-bind:key="localTask.id">
          <div class="list-line flex items-center space-x-10 pt-1 pb-1">
            <div class="icons-column">
              <img
                v-if="userData.photo"
                :src="urlImagePhoto"
                :alt="userData.name"
                class="user-image"
              />
              <font-awesome-icon
                v-else
                icon="fa-solid fa-user"
                class="primary pe-2"
              />
              <font-awesome-icon
                icon="fas fa-check-circle"
                class="checked-icon"
                :class="
                  isValidDate(localTask.date_conclusion) ? 'done' : 'canceled'
                "
              />
            </div>

            <div class="flex flex-row flex-[6] items-center justify-start m-0">
              <text-editable-field
                name="name"
                v-model="localTask.name"
                placeholder="descrição detalhada da tarefa"
                @save="updateTask('name', $event, localTask.id)"
              />
            </div>

            <div class="flex items-center justify-center text-center mr-4">
              {{ formatDuration(localTask.duration_time) }}
            </div>

            <div class="date-column">
              <font-awesome-icon
                icon="fa-solid fa-exclamation-circle"
                class="text-error me-2"
              />
              <date-time-editable-input
                v-model="localTask.date_due"
                :classText="getDeadlineClass(localTask.date_due)"
                :classIcon="getDeadlineClass(localTask.date_due)"
                @save="updateTask('date_due', $event, localTask.id)"
              />
            </div>

            <div class="date-column">
              <font-awesome-icon
                icon="fa-solid fa-check-circle"
                class="text-success me-2"
              />
              <date-time-editable-input
                name="date_conclusion"
                v-model="localTask.date_conclusion"
                @save="updateTask('date_conclusion', $event, localTask.id)"
              />
            </div>

            <!-- Botão de Toggle -->
            <button
              class="w-7 h-7 flex items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-700 transition"
              @click="toggleCancelLine"
            >
              <font-awesome-icon
                :icon="
                  showCancelLine
                    ? 'fa-solid fa-minus'
                    : 'fa-solid fa-times-circle'
                "
              />
            </button>

            <journey-create-form
              :taskId="localTask.id"
              @new-journey-event="addJourneyCreated"
            />

            <add-last-journey-date-button
              :task="localTask"
              :showEndTaskButton="localTask.date_conclusion === null"
              @add-last-journey-date="updateDateConclusion(localTask)"
              @update-task="updateTask"
            />
            <button
              v-if="localTask.journeys && localTask.journeys.length > 0"
              class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-500 text-white hover:bg-blue-700 transition relative"
              @click="toggleJourneys(localTask.id)"
            >
              <font-awesome-icon icon="fa-solid fa-eye" />
              <span
                class="absolute -top-1 -right-2 bg-red-400 text-xs rounded-full px-2 py-0.5"
              >
                {{ localTask.journeys.length }}
              </span>
            </button>
            <button
              v-else
              class="w-7 h-7 flex items-center justify-center rounded-full bg-gray-300 text-gray-500 cursor-not-allowed"
              disabled
            >
              <font-awesome-icon icon="fa-solid fa-eye-slash" />
            </button>
          </div>

          <div
            id="cancel-line"
            class="flex items-center space-x-20 pt-4 pb-4"
            v-if="showCancelLine"
          >
            cancelada:
            <font-awesome-icon
              icon="fa-solid fa-times-circle"
              class="text-primary ms-3 me-2"
            />
            <date-time-editable-input
              name="date_canceled"
              v-model="localTask.date_canceled"
              @save="updateTask('date_canceled', $event, localTask.id)"
            />

            <cancellation-reason-select-input
              name="cancellation_reason"
              v-model="localTask.cancellation_reason"
              @update:modelValue="
                updateTask('cancellation_reason', $event, localTask.id)
              "
            />
          </div>

          <div
            class="journeys-line"
            v-if="
              localTask.journeys &&
              localTask.journeys.length > 0 &&
              showJourneys[localTask.id]
            "
          >
            <journeys-list-from-opportunity
              :journeys="localTask.journeys"
              :newJourneyId="newJourneyId"
              @update-task-duration="updateTaskDuration()"
              @last-journey-end="updateEndTaskButtonVisibility"
            />
          </div>
        </div>
      </section>
    </section>
  </div>
</template>
  
  <script>
import axios from "axios";
import { convertUtcToLocal, formatDuration } from "@/utils/date/dateUtils";
import {
  getColorClassForName,
  getStatusColor,
  getPriorityClass,
  getDeadlineClass,
  getStatusIcon,
  trimName,
} from "@/utils/card/cardUtils";
import {
  BACKEND_URL,
  IMAGES_PATH,
  TASK_URL_PARAMETER,
} from "@/config/apiConfig";
import AddLastJourneyDateButton from "@/components/tasks/buttons/AddLastJourneyDateButton.vue";
import ButtonNewForm from "../buttons/ButtonNewForm.vue";
import CancellationReasonSelectInput from "@/components/forms/selects/CancellationReasonSelectInput.vue";
import DateTimeEditableInput from "@/components/fields/datetime/DateTimeEditableInput.vue";
import JourneyCreateForm from "@/components/forms/JourneyCreateForm.vue";
import JourneysListFromOpportunity from "@/components/lists/JourneysListFromOpportunity.vue";
import TaskCreateForm from "@/components/forms/TaskCreateForm.vue";
import TextEditableField from "@/components/fields/text/TextEditableField.vue";
import SearchInput from "@/components/filters/SearchInput.vue";
import { mapState } from "vuex";

export default {
  name: "TasksList",
  props: {
    tasks: {
      type: Array,
      required: false,
    },
  },
  data() {
    return {
      completedTasks: 0,
      formatedDate: "",
      formatedTime: "",
      localTasks: this.tasks,
      openModal: null,
      percentage: 0,
      searchTerm: "",
      showCancelLine: false,
      showGroupColumn: false,
      showJourneys: {},
      totalTasks: 0,
      newJourneyId: null,
    };
  },
  components: {
    AddLastJourneyDateButton,
    ButtonNewForm,
    CancellationReasonSelectInput,
    DateTimeEditableInput,
    JourneyCreateForm,
    JourneysListFromOpportunity,
    TaskCreateForm,
    TextEditableField,
    SearchInput,
  },
  methods: {
    convertUtcToLocal,
    TextEditableField,
    formatDuration,
    getColorClassForName,
    getStatusColor,
    getPriorityClass,
    getDeadlineClass,
    getStatusIcon,
    trimName,
    addTaskCreated(newTask) {
      this.localTasks.unshift(newTask);
      this.openModal = false;
    },
    addJourneyCreated({ journey, taskId }) {
      const task = this.localTasks.find((t) => t.id === taskId);

      if (task) {
        // Garante que journeys exista como array
        if (!task.journeys) task.journeys = [];

        this.setJourneysVisible(taskId);
        task.journeys.unshift(journey);
        this.highlightNewJourney(journey.id);
      } else {
        console.warn("Tarefa não encontrada para o ID:", taskId);
      }
    },
    formatDateGroup(date) {
      const dateParts = date.split("-");
      const day = parseInt(dateParts[2], 10);
      const month = parseInt(dateParts[1], 10) - 1;
      const year = parseInt(dateParts[0], 10);
      const dateObject = new Date(year, month, day);

      const today = new Date();
      today.setHours(0, 0, 0, 0);

      const weekday = dateObject.toLocaleDateString("pt-BR", {
        weekday: "long",
      });
      const formattedDate = `${day.toString().padStart(2, "0")}/${(month + 1)
        .toString()
        .padStart(2, "0")}`;

      const isToday = dateObject.getTime() === today.getTime();

      return isToday
        ? `${formattedDate} - ${weekday} - HOJE`
        : `${formattedDate} - ${weekday}`;
    },
    formatDateDue(date) {
      if (
        date === "1969-12-31 18:00:00" &&
        date === "1969-12-31 21:00:00" &&
        date === "1970-01-01 00:00:00"
      ) {
        return "";
      }
    },
    trimDescription(description) {
      if (description) {
        return description.substring(0, 110);
      }
    },
    getCombinedClasses(status, priority) {
      const statusClass = getPriorityClass(status);
      const priorityClass = getPriorityClass(priority);

      return `${statusClass} ${priorityClass}`;
    },
    handleOpenModal(target) {
      if (!target) {
        console.warn("Target não informado no botão");
        return;
      }

      this.openModal = target;
    },
    highlightNewJourney(journeyId) {
      this.newJourneyId = journeyId;
      setTimeout(() => {
        this.newJourneyId = null;
      }, 2000);
    },
    isValidDate(date) {
      if (
        date != "1969-12-31 18:00:00" &&
        date != "1969-12-31 21:00:00" &&
        date != "1970-01-01 00:00:00" &&
        date != null
      ) {
        return true;
      }
    },
    setJourneysVisible(taskId) {
      if (!this.showJourneys) {
        this.showJourneys = {}; // Garante que showJourneys seja inicializado
      }
      this.showJourneys[taskId] = true;
    },
    toggleCancelLine() {
      this.showCancelLine = !this.showCancelLine;
    },
    toggleJourneys(taskId) {
      this.showJourneys[taskId] = !this.showJourneys[taskId];
    },
    updateDateConclusion(task) {
      if (task.journeys && task.journeys.length > 0) {
        // Ordena as jornadas e pega a data de término da mais recente
        const sortedJourneys = [...task.journeys].sort(
          (a, b) => new Date(b.end) - new Date(a.end)
        );
        const journeyEnd = sortedJourneys[0].end;

        // Passa o valor de journeyEnd para o método updateTask
        this.updateTask("date_conclusion", journeyEnd, task.id);
        this.showEndTaskButton = false;
        this.messageStatus = "success";
        this.messageText = `Tarefa finalizada com data da última jornada`;
      } else {
        console.log(
          "Nenhuma jornada encontrada para calcular a data de conclusão."
        );
      }
    },
    async updateTask(fieldName, editedValue, localTaskId) {
      const updatedField = {};
      updatedField[fieldName] = editedValue;

      try {
        const response = await axios.put(
          `${BACKEND_URL}${TASK_URL_PARAMETER}${localTaskId}`,
          updatedField
        );

        const updatedTask = response.data.data;

        this.updateTasksList(updatedTask, localTaskId);
      } catch (error) {
        console.error("Erro ao atualizar a tarefa:", error);
      }
    },
    updateTasksList(updatedTask, localTaskId) {
      const index = this.localTasks.findIndex(
        (localTask) => localTask.id === localTaskId
      );
      this.localTasks.splice(index, 1, updatedTask);
    },
  },
  computed: {
    ...mapState({
      userData: (state) => state.userData,
    }),
    urlImagePhoto() {
      return `${IMAGES_PATH}${this.userData.photo}`;
    },
    filteredTasks() {
      if (!this.localTasks || !Array.isArray(this.localTasks)) {
        return [];
      }

      if (!this.searchTerm) {
        return this.localTasks;
      }

      return this.localTasks.filter((task) =>
        task.name.toLowerCase().includes(this.searchTerm.toLowerCase())
      );
    },
    groupedTasks() {
      if (this.localTasks && this.localTasks.length > 0) {
        return this.localTasks.reduce((groups, localTask) => {
          const date = localTask.date_due
            ? localTask.date_due.split(" ")[0]
            : "Sem Data";
          if (!groups[date]) {
            groups[date] = [];
          }
          groups[date].push(localTask);
          return groups;
        }, {});
      } else {
        return {};
      }
    },
    openTaskForm() {
      return this.openModal === "task";
    },
    openProposalForm() {
      return this.openModal === "proposal";
    },
  },
  watch: {
    tasks: {
      handler(newVal) {
        this.localTasks = newVal;
      },
      deep: true,
    },
  },
};
</script>