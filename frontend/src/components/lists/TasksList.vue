<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-tasks" class="page-icon" />
        <h1>TAREFAS</h1>
      </div>
      <div class="page-action">
        <TaskCreateForm @new-task-event="addTaskCreated" />
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
          :class="{ 'highlight': localTask.id === newTaskId }"
          v-bind:key="localTask.id"
        >
          <div class="icons-column">
            <img
              v-if="userData.photo"
              :src="urlImagePhoto"
              :alt="userData.name"
              class="user-image"
            />
            <font-awesome-icon v-else icon="fa-solid fa-user" class="primary" />
            <font-awesome-icon
              icon="fas fa-check-circle"
              class="checked-icon"
              :class="
                isValidDate(localTask.date_conclusion) ? 'done' : 'canceled'
              "
            />
          </div>

          <div class="task-column">
            <router-link
              v-if="getGroupLink(localTask)"
              :to="getGroupLink(localTask)"
              :class="getGroupColorClass(localTask)"
              style="display: flex"
            >
              <p class="text-black text-sm">
                {{ localTask.name }}
              </p>
            </router-link>
          </div>

          <div class="group-column" v-if="showGroupColumn">
            <router-link
              v-if="getGroupLink(localTask)"
              :to="getGroupLink(localTask)"
              :class="getGroupColorClass(localTask)"
              style="display: flex"
            >
              <font-awesome-icon
                :icon="getGroupIcon(localTask)"
                :class="getGroupColorClass(localTask)"
              />
              <p class="text-sm font-semibold ps-2" :class="getGroupColorClass(localTask)">
                {{ getGroupName(localTask) }}
              </p>
            </router-link>
            <div v-else>
              <p>----</p>
            </div>
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
    // project: {
    //   type: Object,
    //   required: false,
    // },
    template: {
      type: String,
      required: true,
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
      newTaskId: null,
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
      this.highlightNewTask(newTask.id);
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
    highlightNewTask(taskId) {
      this.newTaskId = taskId;
      setTimeout(() => {
        this.newTaskId = null;
      }, 2000);
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
    getGroupLink(localTask) {
      if (localTask.opportunity) {
        return {
          name: "opportunityShow",
          params: { id: localTask.opportunity.id },
        };
      } else if (localTask.project) {
        return {
          name: "projectShow",
          params: { id: localTask.project.id },
        };
      } else {
        return null;
      }
    },
    getGroupColorClass(localTask) {
      if (localTask.opportunity) {
        return getColorClassForName(localTask.opportunity.name);
      } else if (localTask.project) {
        return getColorClassForName(localTask.project.name);
      } else {
        return "";
      }
    },
    getGroupIcon(localTask) {
      if (localTask.opportunity) {
        return "fa-solid fa-bullseye";
      } else if (localTask.project) {
        return "fa-solid fa-folder-open";
      } else {
        return "";
      }
    },
    getGroupName(localTask) {
      if (localTask.opportunity) {
        return trimName(localTask.opportunity.name);
      } else if (localTask.project) {
        return trimName(localTask.project.name);
      } else {
        return "----";
      }
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
    localTasks: {
      handler(newVal) {
        this.localTasks = newVal;
      },
      deep: true,
    },
  },
  mounted() {
    if (this.template === "index") {
      this.showGroupColumn = true;
      this.getTasks();
    }
    if (this.template === "home") {
      this.showGroupColumn = true;
      this.getTasksPriorized();
    }
    // if (this.template === 'opportunity') {
    //   if (this.opportunity) {
    //     this.localTasks = this.opportunity.localTasks;
    //   }
    //   this.showTaskDuration = true;
    //   this.localTaskColumnClass = "col-7";
    // }
    // if (this.template === 'project') {
    //   if (this.project) {
    //     this.localTasks = this.project.localTasks;
    //   }
    //   this.showTaskDuration = true;
    //   this.localTaskColumnClass = "col-7";
    // }
  },
};
</script>

<style scoped>
a {
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
}
</style>
