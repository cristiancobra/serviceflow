<template>
  <div class="list-container mb-5 mt-0">
    <div class="row">
      <div class="col-10 d-flex justify-content-left">
        <font-awesome-icon icon="fa-solid fa-tasks" class="icon pe-3 primary" />
        <h2 class="title">TAREFAS</h2>
      </div>
      <div class="col-2 d-flex justify-content-end">
        <TaskCreateForm @new-task-event="addTaskCreated" />
      </div>
    </div>
    <div class="row mt-3 mb-4">
      <div class="col">
        <input type="text" class="form-control search-container" v-model="searchTerm"
          placeholder="Digite para buscar" />
      </div>
    </div>
    <div v-for="(filteredTasks, date) in groupedTasks" :key="date">
      <p class="date-group" :class="getDeadlineClass(date)">
        {{ formatDateGroup(date) }}
      </p>
      <div class="row list-line" :class="{ showTasks: true, 'd-none': isHidden }" v-for="task in filteredTasks"
        v-bind:key="task.id">
        <div class="col-1 d-flex justify-content-center">
          <font-awesome-icon icon="fa-solid fa-user" class="primary pe-2" />
          <font-awesome-icon icon="fas fa-check-circle" class="pe-2"
            :class="isValidDate(task.date_conclusion) ? 'done' : 'canceled'" />
        </div>

        <div class="col-4 d-flex justify-content-left">
          <router-link v-if="task.opportunity" :to="{ name: 'opportunityShow', params: { id: task.opportunity.id } }">
            <div class="d-flex">
              <font-awesome-icon icon="fa-solid fa-bullseye" :class="getColorClassForName(task.opportunity.name)" />
              <p class="m-0 p-0 ps-1 bold" :class="getColorClassForName(task.opportunity.name)">
                {{ trimName(task.opportunity.name) }}
              </p>
            </div>
          </router-link>
          <router-link v-else-if="task.project" :to="{ name: 'projectShow', params: { id: task.project.id } }">
            <div class="d-flex">
              <font-awesome-icon icon="fa-solid fa-folder-open" :class="getColorClassForName(task.project.name)" />
              <p class="m-0 p-0 ps-1 bold" :style="{ color: getColorClassForName(task.project.name) }">
                {{ trimName(task.project.name) }}
              </p>
            </div>
          </router-link>
        </div>

        <div class="col-5">
          <router-link :to="{ name: 'taskShow', params: { id: task.id } }"
            class="d-inline-flex flex-wrap align-items-center black">
            <font-awesome-icon icon="fa-solid fa-tasks" />
            <p class="name ps-2">
              {{ task.name }}
            </p>
          </router-link>
        </div>

        <div class="col-2">
          <DateTimeValue v-if="isValidDate(task.date_conclusion)" v-model="task.date_conclusion" classText="done"
            classIcon='done' @save="updateTask('date_conclusion', $event, task.id)" />
          <DateTimeEditableInput v-else v-model="task.date_due" :classText="getDeadlineClass(task.date_due)"
            :classIcon="getDeadlineClass(task.date_due)" @save="updateTask('date_due', $event, task.id)" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { convertUtcToLocal, formatDuration } from "@/utils/date/dateUtils";
import { getColorClassForName, getStatusColor, getPriorityClass, getDeadlineClass, getStatusIcon } from "@/utils/card/cardUtils";
import { BACKEND_URL, TASK_URL_PARAMETER, TASK_PRIORIZED_URL } from "@/config/apiConfig";
import TaskCreateForm from "@/components/forms/TaskCreateForm.vue";
import DateTimeEditableInput from "../fields/datetime/DateTimeEditableInput.vue";
import DateTimeValue from "../fields/datetime/DateTimeValue.vue";

export default {
  name: "TasksList",
  props: {
    opportunityId: {
      type: Number,
      required: false,
    },
    projectId: {
      type: Number,
      required: false,
    },
  },
  data() {
    return {
      formatedDate: '',
      formatedTime: '',
      isActive: true,
      percentage: 0,
      searchTerm: "",
      tasks: [],
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
    addTaskCreated(newTask) {
      this.toggle();
      this.tasks.unshift(newTask);
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
    trimName(description) {
      if (description) {
        return description.substring(0, 50);
      }
    },
    getColumnClass(columns) {
      switch (columns) {
        case 1:
          return "col-12 g-4";
        case 2:
          return "col-6 g-4";
        default:
          return "col-12";
      }
    },
    getCombinedClasses(status, priority) {
      const statusClass = getPriorityClass(status);
      const priorityClass = getPriorityClass(priority);

      return `${statusClass} ${priorityClass}`;
    },
    getTasksPriorized() {
      axios
        .get(`${BACKEND_URL}${TASK_PRIORIZED_URL}`)
        .then((response) => {
          this.tasks = response.data.data;
          // this.filteredTasks = this.tasks; // Inicialmente, as tarefas filtradas são iguais a todas as tarefas
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
      const index = this.tasks.findIndex(task => task.id === taskId);
      this.tasks.splice(index, 1, updatedTask);
    },
    toggle() {
      this.isActive = !this.isActive;
    },
  },
  computed: {
    filteredTasks() {
      if (this.searchTerm === "") {
        return this.tasks;
      } else {
        const lowerSearchTerm = this.searchTerm.toLowerCase();
        return this.tasks.filter((task) => {
          return (
            task.name.toLowerCase().includes(lowerSearchTerm) ||
            (task.description &&
              task.description.toLowerCase().includes(lowerSearchTerm))
          );
        });
      }
    },
    groupedTasks() {
      return this.filteredTasks.reduce((groups, task) => {
        if (task.date_due) {
          const date = task.date_due.split(' ')[0];
          if (!groups[date]) {
            groups[date] = [];
          }
          groups[date].push(task);
        }
        return groups; // Sempre retorna groups
      }, {});
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
