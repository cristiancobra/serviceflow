<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-tasks" class="page-icon" />
        <h1 class="">AGENDA</h1>
      </div>
      <div class="page-action">
        <button type="button" class="btn btn-primary" @click="isCreateTaskModalVisible = true">
          <font-awesome-icon icon="fa-solid fa-plus" class="text-white" />
        </button>
        <task-create-form v-model="isCreateTaskModalVisible" @new-task-event="addTaskCreated" />
      </div>
    </div>
    <tasks-list-section 
      :tasks="localTasks" 
      :showOpportunityColumn="true" 
      sortOrder="asc" 
    />
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
import TaskCreateForm from "@/components/forms/TaskCreateForm.vue";
import TasksListSection from "@/components/lists/TasksListSection.vue";
import { mapState } from "vuex";

export default {
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
      showTaskDuration: false,
      percentage: 0,
      searchTerm: "",
      localTasks: this.tasks,
      totalTasks: 0,
      completedTasks: 0,
      newTaskId: null,
      isCreateTaskModalVisible: false,
    };
  },
  components: {
    TaskCreateForm,
    TasksListSection,
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
      this.isCreateTaskModalVisible = false;
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
    async getTasksPriorized() {
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
    async updateTask(fieldName, editedValue, taskId) {
      const updatedField = {};
      updatedField[fieldName] = editedValue;

      try {
        const response = await axios.put(
          `${BACKEND_URL}${TASK_URL_PARAMETER}${taskId}`,
          updatedField
        );

        const updatedTask = response.data.data;

        this.updateTasksList(updatedTask, taskId);
      } catch (error) {
        console.error("Erro ao atualizar a tarefa:", error);
      }
    },
    updateTasksList(updatedTask, taskId) {
      const index = this.localTasks.findIndex((task) => task.id === taskId);
      this.localTasks.splice(index, 1, updatedTask);
      this.localTasks = [...this.localTasks];
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
      if (!this.searchTerm || !this.localTasks) {
        return this.localTasks;
      }
      return this.localTasks.filter(task =>
        task.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
        (task.opportunity && task.opportunity.name.toLowerCase().includes(this.searchTerm.toLowerCase())) ||
        (task.project && task.project.name.toLowerCase().includes(this.searchTerm.toLowerCase()))
      );
    },
    groupedTasks() {
      if (this.filteredTasks && this.filteredTasks.length > 0) {
        // Ordena as tarefas por data antes de agrupá-las
        const sortedTasks = [...this.filteredTasks].sort((a, b) => {
          const dateA = new Date(a.date_due || "9999-12-31"); // Tarefas sem data vão para o final
          const dateB = new Date(b.date_due || "9999-12-31");
          return dateA - dateB;
        });

        return sortedTasks.reduce((groups, task) => {
          const date = task.date_due ? task.date_due.split(" ")[0] : "Sem Data";
          if (!groups[date]) {
            groups[date] = [];
          }
          groups[date].push(task);
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
  mounted() {
    this.getTasksPriorized();
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
