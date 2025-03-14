<template>
  <div class="page-container">
    <div class="page-header">
      <div class="title-container">
        <font-awesome-icon icon="fa-solid fa-tasks" class="icon" />
        <h1>TAREFAS</h1>
      </div>
      <div class="action-container">
        <TaskCreateForm @new-task-event="addTaskCreated" />
      </div>
    </div>

    <div class="search-container">
      <input type="text" class="search-input" v-model="searchTerm" placeholder="Digite para buscar" />
    </div>

    <div v-for="(tasks, date) in groupedTasks" :key="date">
      <p class="date-group" :class="getDeadlineClass(date)">
        {{ formatDateGroup(date) }}
      </p>
      <div class="list-line" :class="{ showTasks: true, 'd-none': isHidden }" v-for="task in tasks"
        v-bind:key="task.id">
        <div class="icons-column">
          <img v-if="userData.photo" :src="urlImagePhoto" :alt="userData.name" class="user-image" />
          <font-awesome-icon v-else icon="fa-solid fa-user" class="primary pe-2" />
          <font-awesome-icon icon="fas fa-check-circle" class="checked-icon"
            :class="isValidDate(task.date_conclusion) ? 'done' : 'canceled'" />
        </div>
        <div class="group-column" v-if="showGroupColumn">
          <router-link :class="getColorClassForName(task.opportunity.name)" style="display: flex;" v-if="task.opportunity"
            :to="{ name: 'opportunityShow', params: { id: task.opportunity.id } }">
            <p class="group-name">
              <font-awesome-icon icon="fa-solid fa-bullseye" :class="getColorClassForName(task.opportunity.name)" />
              {{ trimName(task.opportunity.name) }}
            </p>
          </router-link>
          <router-link style="display: flex;" v-else-if="task.project"
            :to="{ name: 'projectShow', params: { id: task.project.id } }">
            <font-awesome-icon icon="fa-solid fa-folder-open" :class="getColorClassForName(task.project.name)" />
            <p class="group-name" :style="{ color: getColorClassForName(task.project.name) }">
              {{ trimName(task.project.name) }}
            </p>
          </router-link>
          <div v-else class="">
            <p>
              ----
            </p>
          </div>
        </div>

        <div class="task-column">
          <router-link :to="{ name: 'taskShow', params: { id: task.id } }" class="">
            <p class="name">
              {{ task.name }}
            </p>
          </router-link>
        </div>

        <div class="date-column">
          <DateTimeValue v-if="isValidDate(task.date_conclusion)" v-model="task.date_conclusion" classText="done"
            classIcon='done' @save="updateTask('date_conclusion', $event, task.id)" />
          <DateTimeEditableInput v-else v-model="task.date_due" :classText="getDeadlineClass(task.date_due)"
            :classIcon="getDeadlineClass(task.date_due)" @save="updateTask('date_due', $event, task.id)" />
          <div class="" v-if="showTaskDuration">
            <p class="">
              {{ formatDuration(task.duration_time) }}
            </p>
          </div>
        </div>

      </div>
    </div>

  </div>
</template>

<script>
import axios from "axios";
import { convertUtcToLocal, formatDuration } from "@/utils/date/dateUtils";
import { getColorClassForName, getStatusColor, getPriorityClass, getDeadlineClass, getStatusIcon, trimName } from "@/utils/card/cardUtils";
import { BACKEND_URL, IMAGES_PATH, TASK_URL_PARAMETER, TASK_PRIORIZED_URL } from "@/config/apiConfig";
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
      formatedDate: '',
      formatedTime: '',
      showGroupColumn: false,
      showTaskDuration: false,
      percentage: 0,
      searchTerm: "",
      localTasks: this.tasks,
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
      const dateParts = date.split('-');
      const day = parseInt(dateParts[2], 10);
      const month = parseInt(dateParts[1], 10) - 1;
      const year = parseInt(dateParts[0], 10);
      const dateObject = new Date(year, month, day);
      const weekday = dateObject.toLocaleDateString('pt-BR', { weekday: 'long' });
      const formattedDate = `${day.toString().padStart(2, '0')}/${(month + 1).toString().padStart(2, '0')}`;

      return `${formattedDate} - ${weekday}`;
    },
    formatDateDue(date) {
      if (date === '1969-12-31 18:00:00' && date === '1969-12-31 21:00:00' && date === '1970-01-01 00:00:00') {
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
      if (date != '1969-12-31 18:00:00'
        && date != '1969-12-31 21:00:00'
        && date != '1970-01-01 00:00:00'
        && date != null
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
      const index = this.localTasks.findIndex(task => task.id === taskId);
      this.localTasks.splice(index, 1, updatedTask);
    },
  },
  computed: {
    ...mapState({
      userData: state => state.userData
    }),
    urlImagePhoto() {
      return `${IMAGES_PATH}${this.userData.photo}`;
    },
    groupedTasks() {
      if (this.localTasks && this.localTasks.length > 0) {
        return this.localTasks.reduce((groups, task) => {
          const date = task.date_due ? task.date_due.split(' ')[0] : 'Sem Data';
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
    if (this.template === 'index') {
      this.showGroupColumn = true;
      this.getTasks();
    }
    if (this.template === 'home') {
      this.showGroupColumn = true;
      this.getTasksPriorized();
    }
    // if (this.template === 'opportunity') {
    //   if (this.opportunity) {
    //     this.tasks = this.opportunity.tasks;
    //   }
    //   this.showTaskDuration = true;
    //   this.taskColumnClass = "col-7";
    // }
    // if (this.template === 'project') {
    //   if (this.project) {
    //     this.localTasks = this.project.tasks;
    //   }
    //   this.showTaskDuration = true;
    //   this.taskColumnClass = "col-7";
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
