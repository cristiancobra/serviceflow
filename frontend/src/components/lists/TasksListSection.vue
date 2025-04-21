<template>
  <div class="section-container">
    <div class="section-header">
      <div class="section-title">
        <font-awesome-icon icon="fa-solid fa-localTasks" class="icon" />
        <h2>TAREFAS</h2>
      </div>
      <div class="section-action">
        <TaskCreateForm @new-localTask-event="addTaskCreated" />
      </div>
    </div>

    <section class="section-container">
      <div class="search-container">
        <input
          type="text"
          class="search-input"
          v-model="searchTerm"
          placeholder="Digite para buscar"
        />
      </div>

      <section class="list-container">
        <div
          v-for="localTask in localTasks"
          class="list-line"
          v-bind:key="localTask.id"
        >
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
          <div class="group-column" v-if="showGroupColumn">
            <router-link
              :class="getColorClassForName(localTask.opportunity.name)"
              style="display: flex"
              v-if="localTask.opportunity"
              :to="{
                name: 'opportunityShow',
                params: { id: localTask.opportunity.id },
              }"
            >
              <p class="group-name">
                <font-awesome-icon
                  icon="fa-solid fa-bullseye"
                  :class="getColorClassForName(localTask.opportunity.name)"
                />
                {{ trimName(localTask.opportunity.name) }}
              </p>
            </router-link>
            <router-link
              style="display: flex"
              v-else-if="localTask.project"
              :to="{
                name: 'projectShow',
                params: { id: localTask.project.id },
              }"
            >
              <font-awesome-icon
                icon="fa-solid fa-folder-open"
                :class="getColorClassForName(localTask.project.name)"
              />
              <p
                class="group-name"
                :style="{ color: getColorClassForName(localTask.project.name) }"
              >
                {{ trimName(localTask.project.name) }}
              </p>
            </router-link>
            <div v-else class="">
              <p>----</p>
            </div>
          </div>

          <div class="task-column">
            <router-link
              :to="{ name: 'taskShow', params: { id: localTask.id } }"
              class=""
            >
              <p class="name">
                {{ localTask.name }}
              </p>
            </router-link>
          </div>

          <div class="date-column">
            <DateTimeValue
              v-if="isValidDate(localTask.date_conclusion)"
              v-model="localTask.date_conclusion"
              classText="done"
              classIcon="done"
              @save="updateTask('date_conclusion', $event, localTask.id)"
            />
            <DateTimeEditableInput
              v-else
              v-model="localTask.date_due"
              :classText="getDeadlineClass(localTask.date_due)"
              :classIcon="getDeadlineClass(localTask.date_due)"
              @save="updateTask('date_due', $event, localTask.id)"
            />
            <div class="" v-if="showTaskDuration">
              <p class="">
                {{ formatDuration(localTask.duration_time) }}
              </p>
            </div>
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
  TASK_PRIORIZED_URL,
} from "@/config/apiConfig";
import { index } from "@/utils/requests/httpUtils";
import TaskCreateForm from "@/components/forms/TaskCreateForm.vue";
import DateTimeEditableInput from "../fields/datetime/DateTimeEditableInput.vue";
import DateTimeValue from "../fields/datetime/DateTimeValue.vue";
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
      formatedDate: "",
      formatedTime: "",
      showGroupColumn: false,
      showTaskDuration: false,
      percentage: 0,
      searchTerm: "",
      localTasks: this.localTasks,
      totalTasks: 0,
      completedTasks: 0,
    };
  },
  components: {
    DateTimeEditableInput,
    DateTimeValue,
    TaskCreateForm,
  },
  methods: {
    convertUtcToLocal,
    formatDuration,
    getColorClassForName,
    getStatusColor,
    getPriorityClass,
    getDeadlineClass,
    getStatusIcon,
    trimName,
    addTaskCreated(newTask) {
      this.localTasks.unshift(newTask);
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
    // getColumnClass(columns) {
    //   switch (columns) {
    //     case 1:
    //       return "col-12 g-4";
    //     case 2:
    //       return "col-6 g-4";
    //     default:
    //       return "col-12";
    //   }
    // },
    getCombinedClasses(status, priority) {
      const statusClass = getPriorityClass(status);
      const priorityClass = getPriorityClass(priority);

      return `${statusClass} ${priorityClass}`;
    },
    async getTasks() {
      try {
        this.localTasks = await index(`tasks`);
      } catch (error) {
        console.error("Erro ao acessar tarefas:", error);
      }
    },
    getTasksPriorized() {
      axios
        .get(`${BACKEND_URL}${TASK_PRIORIZED_URL}`)
        .then((response) => {
          this.localTasks = response.data.data;
        })
        .catch((error) => console.log(error));
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
  
  <style scoped>
/* a {
  text-decoration: none;
  color: inherit;
}

a:visited {
  color: inherit;
}

a:hover {
  color: inherit;
}

a:active {
  color: inherit;
}

.checked-icon {
  font-size: 1.4rem;
  padding-top: 0.2rem;
}

.date-group {
  font-size: 1rem;
  font-weight: 600;
  margin-top: 1.2rem;
  margin-bottom: 0.2rem;
  border-bottom-style: solid;
  border-bottom-width: 1px;
}

.progress {
  background-color: #e9ecef;
  border-radius: 1.5rem;
  height: 1.5rem;
}

.progress-bar {
  background-color: var(--primary);
  color: white;
  text-align: center;
  line-height: 1.5rem;
  font-weight: 600;
}

.small-date {
  font-size: 0.8rem;
  font-weight: 400;
} */
</style>
  