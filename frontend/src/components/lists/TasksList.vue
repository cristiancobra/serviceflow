<template>
  <div class="tasks-container mb-5 mt-0">
    <div class="row align-items-start">
      <div class="col-1">
        <font-awesome-icon icon="fa-solid fa-tasks" class="icon" />
      </div>
      <div class="col-7">
        <h2 class="title">TAREFAS</h2>
      </div>
      <div class="col-4 text-end">
        <button class="button button-new" @click="toggle()">+</button>
      </div>
    </div>
    <div class="row" v-bind:class="{ 'd-none': isActive }">
      <TaskCreateForm @new-task-event="addTaskCreated" @toogle-task-form=toggle() />
    </div>
    <div class="row">
      <div class="col-12 mb-3 mt-3">
        <input type="text" class="form-control search-container" v-model="searchTerm"
          placeholder="Digite para buscar" />
      </div>
    </div>

    <div class="row" v-for="task in tasks" v-bind:key="task.id">
      <div class="col-1 d-flex align-items-center justify-content-center" id="col-user">
        <font-awesome-icon icon="fa-solid fa-user" class="primary big-icon" />
      </div>
      <div v-if="task.date_conclusion" class="col-1 status done">
        <font-awesome-icon icon="fas fa-check-circle" style="font-size: 2rem;" class="done mb-3" />
      </div>
      <div v-else class="col-1 status canceled">
        <font-awesome-icon icon="fas fa-check-circle" style="font-size: 2rem;" class="canceled" />
      </div>
      <div class="col cards">
        <router-link :to="{ name: 'tasksShow', params: { id: task.id } }">
          <div class="row title">
            <div class="col">
              <p class="cards-title">
                {{ task.name }}
              </p>
            </div>
          </div>
          <div class="row">
            <div v-if="task.project" class="col d-flex ps-4">
              <font-awesome-icon icon="fa-solid fa-folder-open" class="primary" />
              <p class="cards-project-h2 ps-2">
                {{ task.project.name }}
              </p>
            </div>
            <div v-else class="col d-flex ps-4">
              <font-awesome-icon icon="fa-solid fa-folder-open" class="canceled" />
              <p class="cards-project-h2 ps-2">
                Sem projeto
              </p>
            </div>
          </div>
        </router-link>
      </div>
      <div class="col-3 line-list d-flex align-items-center justify-content-center">
        <DateTimeValue v-if="formatDateDue(task.date_conclusion)" v-model="task.date_conclusion" classText="done"
          classIcon='done' @save="updateTask('date_conclusion', $event, task.id)" />
        <DateTimeEditableInput v-else v-model="task.date_due" :classText="getDeadlineClass(task)"
          :classIcon='getDeadlineClass(task)' @save="updateTask('date_due', $event, task.id)" />
      </div>
    </div>

  </div>
</template>

<script>
import axios from "axios";
import { convertUtcToLocal, formatDuration } from "@/utils/date/dateUtils";
import { getStatusColor, getPriorityClass, getDeadlineClass, getStatusIcon } from "@/utils/card/cardUtils";
import { BACKEND_URL, TASK_URL, TASK_URL_PARAMETER, TASK_PRIORIZED_URL } from "@/config/apiConfig";
import TaskCreateForm from "@/components/forms/TaskCreateForm.vue";
import DateTimeEditableInput from "../fields/datetime/DateTimeEditableInput.vue";
import DateTimeValue from "../fields/datetime/DateTimeValue.vue";

export default {
  name: "TasksList",
  props: {
    template: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      formatedDate: '',
      formatedTime: '',
      isActive: true,
      searchTerm: "",
      tasks: [],
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
    getStatusColor,
    getPriorityClass,
    getDeadlineClass,
    getStatusIcon,
    addTaskCreated(newTask) {
      this.toggle();
      this.tasks.unshift(newTask);
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
    getTasks() {
      axios
        .get(`${BACKEND_URL}${TASK_URL}`)
        .then((response) => {
          this.tasks = response.data.data;
          // this.filteredTasks = this.tasks;
        })
        .catch((error) => console.log(error));
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
    async updateTask(fieldName, editedValue, taskId) {
      const updatedField = {};
      updatedField[fieldName] = editedValue;

      try {
        const response = await axios.put(
          `${BACKEND_URL}${TASK_URL_PARAMETER}${taskId}`,
          updatedField
        );

        const updatedTask = response.data.data;

        const index = this.tasks.findIndex(task => task.id === taskId);

        this.tasks.splice(index, 1, updatedTask);

      } catch (error) {
        console.error("Erro ao atualizar a tarefa:", error);
      }

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
  },
  mounted() {
    if (this.template === 'priorized') {
      this.getTasksPriorized();
    }

    if (this.template === 'index') {
      this.getTasks();
    }
    console.log(this.template);
  },
};
</script>

<style scoped>
a {
  text-decoration: none;
}

.icon {
  font-size: 1.8rem;
  font-weight: 400;
  color: var(--gray);
}

.small-date {
  font-size: 0.8rem;
  font-weight: 400;
}

.tasks-container {
  border-style: solid;
  border-width: 2px;
  border-color: var(--primary);
  border-radius: 14px;
  padding: 2rem;
}

.title {
  text-align: left;
}
</style>
