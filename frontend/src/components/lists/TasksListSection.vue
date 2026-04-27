<template>
  <div class="section-container">
    <div class="section-header">
      <div class="section-title">
        <font-awesome-icon icon="fa-solid fa-tasks" class="icon" />
        <h2>TAREFAS</h2>
      </div>
      <div class="section-action">
        <button-new-form target="task" @open-modal="openTaskForm = true" />
        
        <button
          @click="createOrganizationTask"
          class="w-9 h-9 flex items-center justify-center rounded-full bg-success text-white hover:bg-green-700 transition shadow-md"
          title="Criar tarefa de organização (30min)"
        >
          <font-awesome-icon icon="fa-solid fa-calendar-check" />
        </button>
        
        <task-create-form 
          v-model="openTaskForm" 
          :opportunity="opportunity" 
          :project="project" 
          @new-task-event="addTaskCreated" 
        />
      </div>
    </div>

    <section class="section-container">
      <SearchInput v-model="searchTerm" placeholder="Buscar tarefas" />

      <section class="">
        <!-- Agrupamento por mês -->
        <div v-for="monthGroup in groupedByMonth" :key="monthGroup.monthKey" class="mb-8">
          <!-- Header do Mês -->
          <div class="flex items-center mb-4">
            <div class="flex items-center gap-3 bg-white pt-5 pb-0">
              <span class="font-bold text-primary text-lg whitespace-nowrap uppercase">{{ monthGroup.monthLabel }}</span>
              <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                {{ monthGroup.totalTasks }} {{ monthGroup.totalTasks === 1 ? 'tarefa' : 'tarefas' }}
              </span>
            </div>
          </div>

          <!-- Agrupamento por dia dentro do mês -->
          <div v-for="(tasks, date) in monthGroup.tasksByDay" :key="date">
            <p class="text-base font-semibold mt-5 mb-1 border-b" :class="getDeadlineClass(date)">
              {{ formatDateGroup(date) }}
            </p>
            <div v-for="localTask in tasks" v-bind:key="localTask.id">
            <div :id="'task-' + localTask.id" class="list-line flex items-center space-x-10 pt-1 pb-1">
              <div class="icons-column">
                <!-- Ícone de cancelamento (X vermelho) ou conclusão (check verde) -->
                <font-awesome-icon v-if="localTask.date_canceled" icon="fas fa-times-circle"
                  class="text-2xl text-red-500" title="Tarefa cancelada" />
                <font-awesome-icon v-else icon="fas fa-check-circle" class="text-2xl" :class="isValidDate(localTask.date_conclusion) ? 'text-success' : 'text-gray-400'
                  " />

                <img v-if="userData.photo" :src="urlImagePhoto" :alt="userData.name"
                  class="w-8 h-8 rounded-full border-2 border-white mr-0" />
                <font-awesome-icon v-else icon="fa-solid fa-user" class="primary pe-2" />
              </div>

              <div class="flex flex-row flex-[6] items-center justify-start m-0">
                <div :class="{ 'task-name-highlighted': showControls[localTask.id] }">
                  <text-editable-field name="name" v-model="localTask.name" placeholder="descrição detalhada da tarefa"
                    @save="updateTask('name', $event, localTask.id)" />
                </div>
              </div>

              <!-- Coluna de Oportunidade/Projeto -->
              <div v-if="showOpportunityColumn" class="min-w-[150px] max-w-[200px] flex items-center justify-start">
                <router-link class="flex no-underline text-inherit" v-if="localTask.opportunity" :to="{
                  name: 'opportunityShow',
                  params: { id: localTask.opportunity.id },
                  query: { tab: 'tasks' },
                  hash: '#task-' + localTask.id
                }">
                  <p class="flex items-center gap-2 font-medium text-sm"
                    :class="getColorClassForName(localTask.opportunity.name)">
                    <font-awesome-icon icon="fa-solid fa-bullseye"
                      :class="getColorClassForName(localTask.opportunity.name)" />
                    {{ trimName(localTask.opportunity.name) }}
                  </p>
                </router-link>
                <router-link class="flex no-underline text-inherit" v-else-if="localTask.project"
                  :to="{ 
                    name: 'projectShow', 
                    params: { id: localTask.project.id },
                    hash: '#task-' + localTask.id
                  }">
                  <font-awesome-icon icon="fa-solid fa-folder-open"
                    :class="getColorClassForName(localTask.project.name)" />
                  <p class="flex items-center gap-2 font-medium text-sm"
                    :class="getColorClassForName(localTask.project.name)">
                    {{ trimName(localTask.project.name) }}
                  </p>
                </router-link>
                <div v-else>
                  <div v-if="editingOpportunity[localTask.id]" class="inline-edit-opportunity">
                    <opportunities-open-select-input
                      v-model="selectedOpportunityId"
                      fieldsToDisplay="name"
                      :autoSelect="false"
                      fieldNull="Nenhuma"
                      @update:modelValue="saveOpportunity(localTask.id, $event)"
                    />
                    <button 
                      @click="cancelEditOpportunity(localTask.id)" 
                      class="btn-cancel-edit ms-2"
                      title="Cancelar"
                    >
                      <font-awesome-icon icon="fa-solid fa-times" />
                    </button>
                  </div>
                  <p 
                    v-else 
                    @click="startEditOpportunity(localTask.id)" 
                    class="text-sm text-gray-400 hover:text-primary cursor-pointer" 
                    title="Clique para vincular a uma oportunidade"
                  >
                    ----
                  </p>
                </div>
              </div>

              <div class="flex items-center justify-center text-center text-primary mr-4 font-bold">
                {{ formatDuration(localTask.duration_time) }}
              </div>

              <div class="date-column">
                <font-awesome-icon icon="fa-solid fa-exclamation-circle"
                  :class="(isValidDate(localTask.date_conclusion) || localTask.date_canceled) ? 'text-gray-600' : 'text-error'"
                  class="me-2" />
                <date-time-editable-input v-model="localTask.date_due" :classText="getDeadlineClass(localTask.date_due)"
                  :classIcon="getDeadlineClass(localTask.date_due)"
                  @save="updateTask('date_due', $event, localTask.id)" />
              </div>

              <div class="date-column">
                <font-awesome-icon icon="fa-solid fa-check-circle" class="text-success me-2" />
                <date-time-editable-input name="date_conclusion" v-model="localTask.date_conclusion"
                  @save="updateTask('date_conclusion', $event, localTask.id)" />
              </div>

              <!-- Botão para mostrar/ocultar controles extras (sempre visível na mesma posição) -->
              <button
                class="w-7 h-7 flex items-center justify-center rounded-full bg-gray-500 text-white hover:bg-gray-700 transition"
                @click="toggleControls(localTask.id)"
                :title="showControls[localTask.id] ? 'Ocultar controles' : 'Mostrar controles'">
                <font-awesome-icon
                  :icon="showControls[localTask.id] ? 'fa-solid fa-chevron-up' : 'fa-solid fa-ellipsis-h'" />
              </button>
            </div>

            <!-- Controles extras (exibidos em uma nova linha abaixo) -->
            <div v-if="showControls[localTask.id]"
              class="flex items-start gap-4 pt-2 pb-10 px-4 bg-primary-content border-l-4 border-gray-400 rounded-r-lg">
              
              <!-- Coluna da Description (esquerda - maior) -->
              <div class="flex-1 min-w-0">
                <text-area-editable-input 
                  label="Descrição"
                  name="description" 
                  v-model="localTask.description" 
                  placeholder="Adicione uma descrição detalhada da tarefa"
                  @save="updateTask('description', $event, localTask.id)" 
                />
              </div>

              <!-- Coluna dos Botões (direita - menor) -->
              <div class="flex items-center space-x-3 flex-shrink-0">
                <!-- Botão de Toggle Cancelamento -->
                <button
                  class="w-7 h-7 flex items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-700 transition"
                  @click="toggleCancelLine(localTask.id)" title="Cancelar tarefa">
                  <font-awesome-icon :icon="showCancelLine[localTask.id]
                      ? 'fa-solid fa-minus'
                      : 'fa-solid fa-times-circle'
                    " />
                </button>

                <!-- Botão para toggle do formulário de jornada -->
                <button
                  class="w-7 h-7 flex items-center justify-center rounded-full bg-orange-500 text-white hover:bg-orange-700 transition"
                  @click="toggleJourneyForm(localTask.id)" title="Adicionar jornada">
                  <font-awesome-icon :icon="showJourneyForm[localTask.id]
                      ? 'fa-solid fa-minus'
                      : 'fa-solid fa-plus'
                    " />
                </button>

                <!-- Botão quickform - iniciar jornada rapidamente -->
                <button
                  class="w-7 h-7 flex items-center justify-center rounded-full bg-purple-500 text-white hover:bg-purple-700 transition"
                  @click="quickStartJourney(localTask.id)" title="Iniciar jornada agora">
                  <font-awesome-icon icon="fa-solid fa-bolt" />
                </button>

                <add-last-journey-date-button :task="localTask" :showEndTaskButton="localTask.date_conclusion === null"
                  @add-last-journey-date="updateDateConclusion(localTask)" @update-task="updateTask" />

                <!-- Botão para visualizar jornadas -->
                <button v-if="localTask.journeys && localTask.journeys.length > 0"
                  class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-500 text-white hover:bg-blue-700 transition relative"
                  @click="toggleJourneys(localTask.id)" title="Ver jornadas">
                  <font-awesome-icon icon="fa-solid fa-eye" />
                  <span class="absolute -top-1 -right-2 bg-red-400 text-xs rounded-full px-2 py-0.5">
                    {{ localTask.journeys.length }}
                  </span>
                </button>
                <button v-else
                  class="w-7 h-7 flex items-center justify-center rounded-full bg-gray-300 text-gray-500 cursor-not-allowed"
                  disabled title="Sem jornadas">
                  <font-awesome-icon icon="fa-solid fa-eye-slash" />
                </button>
              </div>
            </div>

            <!-- Formulário de nova jornada -->
            <div v-if="showJourneyForm[localTask.id]" class="mt-3 mb-3">
              <journey-create-form :taskId="localTask.id" @new-journey-event="addJourneyCreated"
                @close="toggleJourneyForm(localTask.id)" />
            </div>

            <!-- Linha de cancelamento -->
            <div id="cancel-line"
              class="flex items-center space-x-20 pt-4 pb-4 mt-3 mb-3 px-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm"
              v-if="showCancelLine[localTask.id]">
              <span class="font-semibold text-red-700">Cancelada:</span>
              <font-awesome-icon icon="fa-solid fa-times-circle" class="text-red-500 ms-3 me-2" />
              <date-time-editable-input name="date_canceled" v-model="localTask.date_canceled"
                @save="updateTask('date_canceled', $event, localTask.id)" />

              <cancellation-reason-select-input name="cancellation_reason" v-model="localTask.cancellation_reason"
                :disabled="!localTask.date_canceled" @update:modelValue="
                  updateTask('cancellation_reason', $event, localTask.id)
                  " />
            </div>

            <div class="journeys-line" v-if="
              localTask.journeys &&
              localTask.journeys.length > 0 &&
              showJourneys[localTask.id]
            ">
              <journeys-list-from-opportunity :journeys="localTask.journeys"
                @update-task-duration="updateTaskDuration()" @last-journey-end="updateEndTaskButtonVisibility" />
            </div>
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
  TASK_URL,
  JOURNEY_URL,
} from "@/config/apiConfig";
import AddLastJourneyDateButton from "@/components/tasks/buttons/AddLastJourneyDateButton.vue";
import ButtonNewForm from "../buttons/ButtonNewForm.vue";
import CancellationReasonSelectInput from "@/components/forms/selects/CancellationReasonSelectInput.vue";
import DateTimeEditableInput from "@/components/fields/datetime/DateTimeEditableInput.vue";
import JourneyCreateForm from "@/components/forms/JourneyCreateForm.vue";
import JourneysListFromOpportunity from "@/components/lists/JourneysListFromOpportunity.vue";
import TaskCreateForm from "@/components/forms/TaskCreateForm.vue";
import TextEditableField from "@/components/fields/text/TextEditableField.vue";
import TextAreaEditableInput from "@/components/forms/inputs/textarea/TextAreaEditableInput.vue";
import SearchInput from "@/components/filters/SearchInput.vue";
import OpportunitiesOpenSelectInput from "@/components/forms/selects/OpportunitiesOpenSelectInput.vue";
import { mapState } from "vuex";

export default {
  name: "TasksList",
  props: {
    tasks: {
      type: Array,
      required: false,
    },
    showOpportunityColumn: {
      type: Boolean,
      default: false,
    },
    opportunity: {
      type: Object,
      default: null,
    },
    project: {
      type: Object,
      default: null,
    },
    sortOrder: {
      type: String,
      default: 'asc', // 'asc' = mais antiga primeiro, 'desc' = mais recente primeiro
      validator: (value) => ['asc', 'desc'].includes(value),
    },
  },
  data() {
    return {
      completedTasks: 0,
      formatedDate: "",
      formatedTime: "",
      localTasks: this.tasks,
      openTaskForm: false,
      percentage: 0,
      searchTerm: "",
      showCancelLine: {},
      showControls: {},
      showGroupColumn: false,
      showJourneys: {},
      showJourneyForm: {},
      totalTasks: 0,
      editingOpportunity: {},
      selectedOpportunityId: null,
    };
  },
  components: {
    AddLastJourneyDateButton,
    ButtonNewForm,
    CancellationReasonSelectInput,
    DateTimeEditableInput,
    JourneyCreateForm,
    JourneysListFromOpportunity,
    OpportunitiesOpenSelectInput,
    TaskCreateForm,
    TextEditableField,
    TextAreaEditableInput,
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
      this.openTaskForm = false;
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
    checkAndExpandOpenJourneys() {
      if (!this.localTasks || !Array.isArray(this.localTasks)) return;

      this.localTasks.forEach((task) => {
        if (task.journeys && task.journeys.length > 0) {
          // Verifica se há alguma jornada aberta (sem end)
          const hasOpenJourney = task.journeys.some((journey) => !journey.end);
          if (hasOpenJourney) {
            this.setJourneysVisible(task.id);
          }
        }
      });
    },
    formatDateGroup(date) {
      // Trata o caso de "Sem Data"
      if (date === "Sem Data") {
        return "Sem Data";
      }

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

      const isToday = dateObject.getTime() === today.getTime();

      return isToday
        ? `Dia ${day} - ${weekday} - HOJE`
        : `Dia ${day} - ${weekday}`;
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
    toggleCancelLine(taskId) {
      this.showCancelLine[taskId] = !this.showCancelLine[taskId];
    },
    toggleControls(taskId) {
      this.showControls[taskId] = !this.showControls[taskId];
    },
    openControlsForTask(taskId) {
      this.showControls[taskId] = true;
    },
    checkAndOpenControlsFromHash() {
      // Verifica se há um hash na URL indicando uma tarefa específica
      if (this.$route && this.$route.hash) {
        const taskId = this.$route.hash.substring(1); // Remove o '#'
        
        // Se o hash é do formato 'task-123', extrai o ID numérico
        if (taskId.startsWith('task-')) {
          const numericId = parseInt(taskId.replace('task-', ''));
          
          // Aguarda um momento para garantir que o scroll foi executado
          setTimeout(() => {
            this.openControlsForTask(numericId);
          }, 1200);
        }
      }
    },
    toggleJourneys(taskId) {
      this.showJourneys[taskId] = !this.showJourneys[taskId];
    },
    toggleJourneyForm(taskId) {
      this.showJourneyForm[taskId] = !this.showJourneyForm[taskId];
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
    async quickStartJourney(taskId) {
      try {
        const quickForm = {
          task_id: taskId,
          start: new Date(),
          user_timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
        };

        const response = await axios.post(
          `${BACKEND_URL}journeys`,
          quickForm
        );

        const newJourney = response.data.data;
        this.addJourneyCreated({ journey: newJourney, taskId });
      } catch (error) {
        console.error("Erro ao iniciar jornada rapidamente:", error);
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
    startEditOpportunity(taskId) {
      this.editingOpportunity[taskId] = true;
      this.selectedOpportunityId = null;
    },
    cancelEditOpportunity(taskId) {
      this.editingOpportunity[taskId] = false;
      this.selectedOpportunityId = null;
    },
    async saveOpportunity(taskId, opportunityId) {
      if (!opportunityId) {
        this.cancelEditOpportunity(taskId);
        return;
      }

      try {
        const response = await axios.put(
          `${BACKEND_URL}${TASK_URL_PARAMETER}${taskId}`,
          { opportunity_id: opportunityId }
        );

        const updatedTask = response.data.data;
        this.updateTasksList(updatedTask, taskId);
        this.cancelEditOpportunity(taskId);
      } catch (error) {
        console.error("Erro ao atualizar oportunidade da tarefa:", error);
      }
    },
    async createOrganizationTask() {
      try {
        const now = new Date();
        const dueDate = new Date(now.getTime() + 30 * 60000); // +30 minutos
        
        // 1. Criar a tarefa
        const taskData = {
          name: "Organização diária",
          description: "Organização da agenda diária",
          date_start: now.toISOString(),
          date_due: dueDate.toISOString(),
          user_id: this.userData.id,
          status: "doing",
          priority: "medium",
          user_timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
        };

        const taskResponse = await axios.post(
          `${BACKEND_URL}${TASK_URL}`,
          taskData
        );

        const newTask = taskResponse.data.data;
        
        // 2. Criar a jornada automaticamente
        const journeyData = {
          task_id: newTask.id,
          start: now.toISOString(),
          user_timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
        };

        const journeyResponse = await axios.post(
          `${BACKEND_URL}${JOURNEY_URL}`,
          journeyData
        );

        const newJourney = journeyResponse.data.data;
        
        // 3. Atualizar a lista local
        newTask.journeys = [newJourney];
        this.addTaskCreated(newTask);
        this.setJourneysVisible(newTask.id);
        
        console.log("✅ Tarefa de organização criada com sucesso!");
        
      } catch (error) {
        console.error("❌ Erro ao criar tarefa de organização:", error);
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
      if (this.filteredTasks && this.filteredTasks.length > 0) {
        // Ordena as tarefas por data antes de agrupá-las
        const sortedTasks = [...this.filteredTasks].sort((a, b) => {
          const dateA = new Date(a.date_due || "9999-12-31"); // Tarefas sem data vão para o final
          const dateB = new Date(b.date_due || "9999-12-31");
          return this.sortOrder === 'asc' ? dateA - dateB : dateB - dateA;
        });

        return sortedTasks.reduce((groups, localTask) => {
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
    groupedByMonth() {
      const monthGroups = {};
      
      if (this.filteredTasks && this.filteredTasks.length > 0) {
        // Ordena as tarefas por data
        const sortedTasks = [...this.filteredTasks].sort((a, b) => {
          const dateA = new Date(a.date_due || "9999-12-31");
          const dateB = new Date(b.date_due || "9999-12-31");
          return this.sortOrder === 'asc' ? dateA - dateB : dateB - dateA;
        });

        sortedTasks.forEach(task => {
          if (task.date_due) {
            const date = new Date(task.date_due);
            const monthKey = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
            const monthLabel = new Intl.DateTimeFormat('pt-BR', { month: 'long', year: 'numeric' }).format(date).replace(' de ', ' ');
            
            if (!monthGroups[monthKey]) {
              monthGroups[monthKey] = {
                monthKey,
                monthLabel: monthLabel.charAt(0).toUpperCase() + monthLabel.slice(1),
                tasksByDay: {},
                totalTasks: 0
              };
            }
            
            // Agrupa por dia dentro do mês
            const dayKey = task.date_due.split(" ")[0];
            if (!monthGroups[monthKey].tasksByDay[dayKey]) {
              monthGroups[monthKey].tasksByDay[dayKey] = [];
            }
            monthGroups[monthKey].tasksByDay[dayKey].push(task);
            monthGroups[monthKey].totalTasks++;
          } else {
            // Tarefas sem data
            const monthKey = "9999-99";
            if (!monthGroups[monthKey]) {
              monthGroups[monthKey] = {
                monthKey,
                monthLabel: "Sem Data",
                tasksByDay: { "Sem Data": [] },
                totalTasks: 0
              };
            }
            monthGroups[monthKey].tasksByDay["Sem Data"].push(task);
            monthGroups[monthKey].totalTasks++;
          }
        });
      }
      
      // Retorna os grupos ordenados por mês
      return Object.values(monthGroups).sort((a, b) => {
        if (a.monthKey === "9999-99") return 1;
        if (b.monthKey === "9999-99") return -1;
        const comparison = new Date(a.monthKey) - new Date(b.monthKey);
        return this.sortOrder === 'asc' ? comparison : -comparison;
      });
    },
  },
  mounted() {
    this.checkAndExpandOpenJourneys();
    this.checkAndOpenControlsFromHash();
  },
  watch: {
    tasks: {
      handler(newVal) {
        this.localTasks = newVal;
        this.checkAndExpandOpenJourneys();
        this.checkAndOpenControlsFromHash();
      },
      deep: true,
    },
  },
};
</script>

<style scoped>
.inline-edit-opportunity {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-cancel-edit {
  background: none;
  border: none;
  color: #dc2626;
  cursor: pointer;
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
  transition: color 0.2s;
}

.btn-cancel-edit:hover {
  color: #991b1b;
}

.cursor-pointer {
  cursor: pointer;
}

.task-name-highlighted {
  font-weight: 700;
  color: var(--primary);
}

.task-name-highlighted p {
  font-weight: 700;
  color: var(--primary);
}
</style>