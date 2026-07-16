<template>
  <div class="section-container">
    <div class="section-header">
      <div class="section-title">
        <font-awesome-icon icon="fa-solid fa-tasks" class="icon" />
        <h2>TAREFAS</h2>
      </div>
      <div class="section-action">
        <button-new-form target="task" @open-modal="openTaskForm = true" />

        <button @click="createOrganizationTask"
          class="w-9 h-9 flex items-center justify-center rounded-full bg-success text-white hover:bg-green-700 transition shadow-md"
          title="Criar tarefa de organização (30min)">
          <font-awesome-icon icon="fa-solid fa-calendar-check" />
        </button>

        <task-create-form v-model="openTaskForm" :opportunity="opportunity" :project="project"
          @new-task-event="addTaskCreated" />
      </div>
    </div>

    <section class="section-container">
      <!-- Barra de busca e filtros -->
      <div class="flex flex-col md:flex-row gap-4 items-start md:items-center mb-6">
        <div class="flex-1 w-full md:w-auto">
          <SearchInput v-model="searchTerm" placeholder="Buscar tarefas" />
        </div>
        
        <!-- Dropdown de Filtro por Status -->
        <div class="relative" ref="filterDropdown">
          <button
            @click="toggleDropdown"
            class="px-4 py-2 border-2 rounded-lg font-semibold text-sm cursor-pointer transition-all duration-300 flex items-center gap-2 min-w-[220px] justify-between"
            :class="currentFilterClass">
            <span>{{ currentFilterLabel }}</span>
            <font-awesome-icon :icon="isDropdownOpen ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'" class="text-xs" />
          </button>
          
          <!-- Dropdown Menu Status -->
          <div
            v-show="isDropdownOpen"
            class="absolute top-full left-0 mt-2 w-full bg-white border-2 border-gray-200 rounded-lg shadow-lg z-50 overflow-hidden">
            <button
              v-for="filter in filterOptions"
              :key="filter.value"
              @click="selectFilter(filter.value)"
              class="w-full px-4 py-2 text-left font-semibold text-sm cursor-pointer transition-all duration-300 border-b border-gray-100 last:border-b-0"
              :class="filter.class"
              :title="filter.title">
              {{ filter.label }}
            </button>
          </div>
        </div>

        <!-- Dropdown de Filtro por Departamento -->
        <div class="relative" ref="departmentDropdown">
          <button
            @click="toggleDepartmentDropdown"
            class="px-4 py-2 border-2 rounded-lg font-semibold text-sm cursor-pointer transition-all duration-300 flex items-center gap-2 min-w-[220px] justify-between bg-white border-gray-300 text-gray-700 hover:border-primary hover:text-primary">
            <span>{{ currentDepartmentLabel }}</span>
            <font-awesome-icon :icon="isDepartmentDropdownOpen ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'" class="text-xs" />
          </button>
          
          <!-- Dropdown Menu Departamentos -->
          <div
            v-show="isDepartmentDropdownOpen"
            class="absolute top-full left-0 mt-2 w-full bg-white border-2 border-gray-200 rounded-lg shadow-lg z-50 overflow-hidden max-h-[300px] overflow-y-auto">
            <button
              @click="selectDepartment(null)"
              class="w-full px-4 py-2 text-left font-semibold text-sm cursor-pointer transition-all duration-300 border-b border-gray-100 hover:bg-gray-50"
              title="Todos os departamentos">
              Todos os departamentos
            </button>
            <button
              v-for="department in departments"
              :key="department.id"
              @click="selectDepartment(department.id)"
              class="w-full px-4 py-2 text-left font-semibold text-sm cursor-pointer transition-all duration-300 border-b border-gray-100 last:border-b-0 hover:opacity-80"
              :style="{ backgroundColor: department.color + '20', color: department.color }"
              :title="department.description">
              <font-awesome-icon :icon="department.icon" class="mr-2" />
              {{ department.name }}
            </button>
          </div>
        </div>
      </div>

      <section class="">
        <!-- Lista de tarefas agrupadas por mês -->
        <template v-for="monthGroup in groupedByMonth" :key="monthGroup.monthKey">
          <div class="flex items-center gap-2 mb-3 mt-4">
            <span class="font-bold text-black text-sm uppercase tracking-wide whitespace-nowrap">
              {{ monthGroup.monthLabel }}
            </span>
            <div class="h-px flex-1 bg-gray-200"></div>
          </div>
          <template v-for="(dayTasks, dayKey) in monthGroup.tasksByDay" :key="dayKey">
            <div v-for="localTask in dayTasks" :key="localTask.id">
              <div :id="'task-' + localTask.id" class="list-line flex items-center space-x-10 pt-1 pb-1">

            <!-- Coluna do Dia e Horário -->
            <div class="min-w-[70px] max-w-[70px] flex flex-col items-start justify-center">
              <span class="text-sm font-semibold" :class="getDeadlineClass(localTask.date_due ? localTask.date_due.split(' ')[0] : 'Sem Data')">
                {{ formatTaskDate(localTask.date_due) }}
              </span>
              <span v-if="localTask.date_due" class="text-xs text-gray-500">
                {{ formatTaskTime(localTask.date_due) }}
              </span>
            </div>

            <!-- coluna de ícones-->
            <div class="flex items-center gap-0">
              <!-- Badge de Status da Tarefa -->
              <task-status-badge :task="localTask" />

              <user-avatar
                :photo="userData.photo"
                :name="userData.name"
                :user-id="userData.id"
              />
            </div>

            <!-- Coluna Badge do Departamento -->
            <div class="min-w-[40px] max-w-[40px] flex items-center justify-center">
              <department-badge :task="localTask" @department-updated="handleDepartmentUpdated" />
            </div>

            <!-- Coluna do nome da tarefa -->
            <div class="flex flex-[6] items-center justify-start">
              <text-editable-field name="name" v-model="localTask.name"
                placeholder="descrição detalhada da tarefa" @save="updateTask('name', $event, localTask.id)" />
            </div>

            <!-- Coluna de Avatares (Company e Lead) -->
            <div v-if="showOpportunityColumn" class="flex items-center gap-1">
              <template v-if="localTask.opportunity">
                <!-- Avatar da Empresa -->
                <div v-if="editingCompany[localTask.id]" class="flex items-center gap-2 min-w-[200px]">
                  <companies-select-input
                    v-model="selectedCompanyId"
                    name="company_id"
                    fieldsToDisplay="legal_name"
                    fieldNull="Nenhuma"
                    @update:modelValue="saveCompany(localTask.id, $event)"
                  />
                  <button @click="cancelEditCompany(localTask.id)" class="bg-transparent border-0 text-red-600 cursor-pointer px-2 py-1 text-sm transition-colors hover:text-red-800 ms-2" title="Cancelar">
                    <font-awesome-icon icon="fa-solid fa-times" />
                  </button>
                </div>
                <company-avatar
                  v-else
                  :photo="localTask.opportunity.company?.photo"
                  :business-name="localTask.opportunity.company?.business_name"
                  :legal-name="localTask.opportunity.company?.legal_name"
                  :company-id="localTask.opportunity.company?.id"
                  @click.native="!localTask.opportunity.company?.id && startEditCompany(localTask.id)"
                  :custom-class="!localTask.opportunity.company?.id ? 'cursor-pointer hover:opacity-70' : ''"
                />
                <!-- Avatar do Lead -->
                <lead-avatar
                  :photo="localTask.opportunity.lead?.photo"
                  :name="localTask.opportunity.lead?.name"
                  :lead-id="localTask.opportunity.lead?.id"
                  :overlap="true"
                />
              </template>
              
              <template v-else-if="localTask.project">
                <!-- Avatar da Empresa do Projeto -->
                <company-avatar
                  :photo="localTask.project.company?.photo"
                  :business-name="localTask.project.company?.business_name"
                  :legal-name="localTask.project.company?.legal_name"
                  :company-id="localTask.project.company?.id"
                />
              </template>
              
              <template v-else>
                <!-- Avatar genérico -->
                <company-avatar />
              </template>
            </div>

            <!-- Coluna Nome da Oportunidade/Projeto -->
            <div v-if="showOpportunityColumn" class="min-w-[200px] max-w-[200px] flex items-center justify-start">
              <template v-if="localTask.opportunity">
                <router-link class="flex no-underline text-inherit items-center gap-1"
                  :to="{
                    name: 'opportunityShow',
                    params: { id: localTask.opportunity.id },
                    query: { tab: 'tasks' },
                    hash: '#task-' + localTask.id
                  }">
                  <div class="flex items-center gap-1 font-medium text-xs">
                    <font-awesome-icon icon="fa-solid fa-bullseye" class="text-xs" />
                    {{ trimName(localTask.opportunity.name) }}
                  </div>
                </router-link>
              </template>
              
              <template v-else-if="localTask.project">
                <router-link class="flex no-underline text-inherit items-center gap-1"
                  :to="{
                    name: 'projectShow',
                    params: { id: localTask.project.id },
                    hash: '#task-' + localTask.id
                  }">
                  <div class="flex items-center gap-1 font-medium text-xs">
                    <font-awesome-icon icon="fa-solid fa-folder-open" class="text-xs" />
                    {{ trimName(localTask.project.name) }}
                  </div>
                </router-link>
              </template>
              
              <template v-else>
                <div v-if="editingOpportunity[localTask.id]" class="flex items-center gap-2 flex-1">
                  <opportunities-open-select-input v-model="selectedOpportunityId" fieldsToDisplay="name"
                    :autoSelect="false" fieldNull="Nenhuma"
                    @update:modelValue="saveOpportunity(localTask.id, $event)" />
                  <button @click="cancelEditOpportunity(localTask.id)" class="bg-transparent border-0 text-red-600 cursor-pointer px-2 py-1 text-sm transition-colors hover:text-red-800 ms-2"
                    title="Cancelar">
                    <font-awesome-icon icon="fa-solid fa-times" />
                  </button>
                </div>
                <p v-else @click="startEditOpportunity(localTask.id)"
                  class="text-xs text-gray-400 hover:text-primary cursor-pointer"
                  title="Clique para vincular a uma oportunidade">
                  ----
                </p>
              </template>
            </div>

            <div class="flex items-center justify-center text-center text-primary mr-4 font-bold">
              {{ formatDuration(localTask.duration_time) }}
            </div>

            <div class="flex flex-1 md:flex-[2] items-center justify-start mr-4">
              <font-awesome-icon icon="fa-solid fa-check-circle" class="text-success me-2" />
              <date-time-editable-input name="date_conclusion" v-model="localTask.date_conclusion"
                @save="updateTask('date_conclusion', $event, localTask.id)" />
            </div>

            <!-- Botão para abrir modal de detalhes -->
            <button
              class="w-7 h-7 flex items-center justify-center rounded-full bg-gray-500 text-white hover:bg-gray-700 transition"
              @click="openTaskModal(localTask.id)"
              title="Ver detalhes da tarefa">
              <font-awesome-icon icon="fa-solid fa-plus" />
            </button>
              </div>
            </div>
          </template>
        </template>
      </section>
    </section>
  </div>
</template>

<script>
import axios from "axios";
import { convertUtcToLocal, formatDuration } from "@/utils/date/dateUtils";
import {
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
import ButtonNewForm from "../buttons/ButtonNewForm.vue";
import CompanyAvatar from "@/components/common/CompanyAvatar.vue";
import LeadAvatar from "@/components/common/LeadAvatar.vue";
import UserAvatar from "@/components/common/UserAvatar.vue";
import DateTimeEditableInput from "@/components/fields/datetime/DateTimeEditableInput.vue";
import TaskCreateForm from "@/components/forms/TaskCreateForm.vue";
import TextEditableField from "@/components/fields/text/TextEditableField.vue";
import SearchInput from "@/components/filters/SearchInput.vue";
import OpportunitiesOpenSelectInput from "@/components/forms/selects/OpportunitiesOpenSelectInput.vue";
import CompaniesSelectInput from "@/components/forms/selects/CompaniesSelectInput.vue";
import LeadsSelectInput from "@/components/forms/selects/LeadsSelectInput.vue";
import DepartmentBadge from "@/components/badges/DepartmentBadge.vue";
import TaskStatusBadge from "@/components/badges/TaskStatusBadge.vue";
import { mapState, mapMutations } from "vuex";

export default {
  name: "TasksList",
  emits: ['filter-change', 'department-filter-change'],
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
      totalTasks: 0,
      editingOpportunity: {},
      selectedOpportunityId: null,
      editingCompany: {},
      selectedCompanyId: null,
      editingLead: {},
      selectedLeadId: null,
      editingProjectCompany: {},
      selectedProjectCompanyId: null,
      activeFilter: null, // Filtro ativo (null = todas as tarefas)
      isLoadingTasks: false, // Indicador de carregamento
      isDropdownOpen: false, // Estado do dropdown
      isDepartmentDropdownOpen: false, // Estado do dropdown de departamento
      activeDepartment: null, // Departamento ativo (null = todos)
      departments: [], // Lista de departamentos
      filterOptions: [
        { value: null, label: 'Todas as situações', class: 'hover:bg-primary hover:text-white text-primary', title: 'Todas as tarefas' },
        { value: 'to-do', label: 'Fazer', class: 'hover:bg-orange-500 hover:text-white text-orange-500', title: 'Tarefas a fazer' },
        { value: 'doing', label: 'Fazendo', class: 'hover:bg-blue-500 hover:text-white text-blue-500', title: 'Tarefas em andamento' },
        { value: 'wait', label: 'Aguardando', class: 'hover:bg-yellow-500 hover:text-white text-yellow-500', title: 'Tarefas aguardando' },
        { value: 'done', label: 'Feitas', class: 'hover:bg-success hover:text-white text-success', title: 'Tarefas concluídas' },
        { value: 'canceled', label: 'Canceladas', class: 'hover:bg-gray-500 hover:text-white text-gray-500', title: 'Tarefas canceladas' },
      ],
    };
  },
  components: {
    ButtonNewForm,
    CompaniesSelectInput,
    CompanyAvatar,
    DateTimeEditableInput,
    DepartmentBadge,
    LeadAvatar,
    LeadsSelectInput,
    OpportunitiesOpenSelectInput,
    TaskStatusBadge,
    TaskCreateForm,
    TextEditableField,
    SearchInput,
    UserAvatar,
  },
  methods: {
    convertUtcToLocal,
    TextEditableField,
    formatDuration,
    getStatusColor,
    getPriorityClass,
    getDeadlineClass,
    getStatusIcon,
    trimName,
    ...mapMutations(['setSelectedTaskId']),
    addTaskCreated(newTask) {
      this.localTasks.unshift(newTask);
      this.openTaskForm = false;
    },
    formatTaskDate(date) {
      // Trata o caso de sem data
      if (!date) {
        return "Sem Data";
      }

      const dateParts = date.split(" ")[0].split("-");
      const day = parseInt(dateParts[2], 10);
      const month = parseInt(dateParts[1], 10) - 1;
      const year = parseInt(dateParts[0], 10);
      const dateObject = new Date(year, month, day);

      const today = new Date();
      today.setHours(0, 0, 0, 0);

      const weekday = dateObject.toLocaleDateString("pt-BR", {
        weekday: "short",
      });

      const isToday = dateObject.getTime() === today.getTime();

      return isToday
        ? `${day} - ${weekday} - HOJE`
        : `${day} - ${weekday}`;
    },
    formatTaskTime(date) {
      // Extrai o horário de date_due
      if (!date) {
        return "";
      }

      const timePart = date.split(" ")[1];
      if (!timePart) {
        return "";
      }

      // Retorna horário no formato HH:MM
      const [hours, minutes] = timePart.split(":");
      return `${hours}:${minutes}`;
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
    isValidDate(date) {
      if (
        date != "1969-12-31 18:00:00" &&
        date != "1969-12-31 21:00:00" &&
        date != "1970-01-01 00:00:00" &&
        date != null
      ) {
        return true;
      }
      return false;
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
    startEditCompany(taskId) {
      this.editingCompany[taskId] = true;
      this.selectedCompanyId = null;
    },
    cancelEditCompany(taskId) {
      this.editingCompany[taskId] = false;
      this.selectedCompanyId = null;
    },
    async saveCompany(taskId, companyId) {
      if (!companyId) {
        this.cancelEditCompany(taskId);
        return;
      }

      const task = this.localTasks.find(t => t.id === taskId);
      if (!task || !task.opportunity) return;

      try {
        const response = await axios.put(
          `${BACKEND_URL}opportunities/${task.opportunity.id}`,
          { company_id: companyId }
        );

        // Atualiza a task inteira para refletir mudanças
        const taskResponse = await axios.get(
          `${BACKEND_URL}${TASK_URL_PARAMETER}${taskId}`
        );
        const updatedTask = taskResponse.data.data;
        this.updateTasksList(updatedTask, taskId);
        this.cancelEditCompany(taskId);
      } catch (error) {
        console.error("Erro ao atualizar empresa da oportunidade:", error);
      }
    },
    startEditLead(taskId) {
      this.editingLead[taskId] = true;
      this.selectedLeadId = null;
    },
    cancelEditLead(taskId) {
      this.editingLead[taskId] = false;
      this.selectedLeadId = null;
    },
    async saveLead(taskId, leadId) {
      if (!leadId) {
        this.cancelEditLead(taskId);
        return;
      }

      const task = this.localTasks.find(t => t.id === taskId);
      if (!task || !task.opportunity) return;

      try {
        const response = await axios.put(
          `${BACKEND_URL}opportunities/${task.opportunity.id}`,
          { lead_id: leadId }
        );

        // Atualiza a task inteira para refletir mudanças
        const taskResponse = await axios.get(
          `${BACKEND_URL}${TASK_URL_PARAMETER}${taskId}`
        );
        const updatedTask = taskResponse.data.data;
        this.updateTasksList(updatedTask, taskId);
        this.cancelEditLead(taskId);
      } catch (error) {
        console.error("Erro ao atualizar lead da oportunidade:", error);
      }
    },
    startEditProjectCompany(taskId) {
      this.editingProjectCompany[taskId] = true;
      this.selectedProjectCompanyId = null;
    },
    cancelEditProjectCompany(taskId) {
      this.editingProjectCompany[taskId] = false;
      this.selectedProjectCompanyId = null;
    },
    async saveProjectCompany(taskId, companyId) {
      if (!companyId) {
        this.cancelEditProjectCompany(taskId);
        return;
      }

      const task = this.localTasks.find(t => t.id === taskId);
      if (!task || !task.project) return;

      try {
        const response = await axios.put(
          `${BACKEND_URL}projects/${task.project.id}`,
          { company_id: companyId }
        );

        // Atualiza a task inteira para refletir mudanças
        const taskResponse = await axios.get(
          `${BACKEND_URL}${TASK_URL_PARAMETER}${taskId}`
        );
        const updatedTask = taskResponse.data.data;
        this.updateTasksList(updatedTask, taskId);
        this.cancelEditProjectCompany(taskId);
      } catch (error) {
        console.error("Erro ao atualizar empresa do projeto:", error);
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

      } catch (error) {
        console.error("❌ Erro ao criar tarefa de organização:", error);
      }
    },
    openTaskModal(taskId) {
      this.setSelectedTaskId(taskId);
    },
    handleTaskUpdated(updatedTask) {
      // Atualiza a tarefa na lista local
      const index = this.localTasks.findIndex(task => task.id === updatedTask.id);
      if (index !== -1) {
        this.localTasks.splice(index, 1, updatedTask);
      }
    },
    handleDepartmentUpdated(updatedTask) {
      // Atualiza a tarefa na lista local quando o departamento é alterado
      const index = this.localTasks.findIndex(task => task.id === updatedTask.id);
      if (index !== -1) {
        this.localTasks.splice(index, 1, updatedTask);
      }
    },
    async handleFilterClick(status) {
      this.activeFilter = status;
      
      // Se há um opportunity ou project, significa que estamos em uma página de detalhes
      // Neste caso, filtramos localmente
      if (this.opportunity || this.project) {
        // Filtro local - filtra as tasks já carregadas
        if (!status) {
          this.localTasks = this.tasks;
        } else {
          this.localTasks = this.tasks.filter(task => {
            if (status === 'done') {
              return task.date_conclusion && !task.date_canceled;
            } else if (status === 'canceled') {
              return task.date_canceled;
            } else {
              return task.status === status && !task.date_conclusion && !task.date_canceled;
            }
          });
        }
        return;
      }
      
      // Se não há opportunity/project, emite evento para componente pai
      this.$emit('filter-change', status);
    },
    toggleDropdown() {
      this.isDropdownOpen = !this.isDropdownOpen;
    },
    selectFilter(status) {
      this.isDropdownOpen = false;
      this.handleFilterClick(status);
    },
    handleClickOutside(event) {
      if (this.$refs.filterDropdown && !this.$refs.filterDropdown.contains(event.target)) {
        this.isDropdownOpen = false;
      }
      if (this.$refs.departmentDropdown && !this.$refs.departmentDropdown.contains(event.target)) {
        this.isDepartmentDropdownOpen = false;
      }
    },
    toggleDepartmentDropdown() {
      this.isDepartmentDropdownOpen = !this.isDepartmentDropdownOpen;
    },
    selectDepartment(departmentId) {
      this.activeDepartment = departmentId;
      this.isDepartmentDropdownOpen = false;
      this.handleDepartmentFilterClick(departmentId);
    },
    async handleDepartmentFilterClick(departmentId) {
      // Se há um opportunity ou project, significa que estamos em uma página de detalhes
      // Neste caso, filtramos localmente
      if (this.opportunity || this.project) {
        // Filtro local
        if (!departmentId) {
          this.localTasks = this.tasks;
        } else {
          this.localTasks = this.tasks.filter(task => task.department_id === departmentId);
        }
        return;
      }
      
      // Se não há opportunity/project, emite evento para componente pai
      this.$emit('department-filter-change', departmentId);
    },
    async getDepartments() {
      try {
        const response = await axios.get(`${BACKEND_URL}departments`);
        this.departments = response.data.data;
      } catch (error) {
        console.error('❌ Erro ao buscar departamentos:', error);
        this.departments = [];
      }
    },
  },
  computed: {
    currentFilterLabel() {
      const filter = this.filterOptions.find(f => f.value === this.activeFilter);
      return filter ? filter.label : 'Todas as situações';
    },
    currentFilterClass() {
      const filter = this.filterOptions.find(f => f.value === this.activeFilter);
      if (!filter || filter.value === null) {
        return 'bg-primary text-white border-primary';
      }
      
      const colorMap = {
        'to-do': 'bg-orange-500 text-white border-orange-500',
        'doing': 'bg-blue-500 text-white border-blue-500',
        'wait': 'bg-yellow-500 text-white border-yellow-500',
        'done': 'bg-success text-white border-success',
        'canceled': 'bg-gray-500 text-white border-gray-500',
      };
      
      return colorMap[filter.value] || 'bg-primary text-white border-primary';
    },
    currentDepartmentLabel() {
      if (!this.activeDepartment) {
        return 'Todos os departamentos';
      }
      const department = this.departments.find(d => d.id === this.activeDepartment);
      return department ? department.name : 'Todos os departamentos';
    },
    ...mapState({
      userData: (state) => state.userData,
      updatedTask: (state) => state.updatedTask,
    }),
    imagesPath() {
      return IMAGES_PATH;
    },
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
    // Modal approach - no inline expansion needed
    // Adiciona listener para fechar dropdown ao clicar fora
    document.addEventListener('click', this.handleClickOutside);
    // Carrega os departamentos
    this.getDepartments();
  },
  beforeUnmount() {
    // Remove listener ao destruir o componente
    document.removeEventListener('click', this.handleClickOutside);
  },
  watch: {
    tasks: {
      handler(newVal) {
        this.localTasks = newVal;
      },
      deep: true,
    },
    updatedTask(task) {
      if (!task) return;
      const idx = this.localTasks.findIndex(t => t.id === task.id);
      if (idx !== -1) {
        this.localTasks.splice(idx, 1, task);
      }
    },
  },
};
</script>

<style scoped>
/* Estilos customizados removidos - usando Tailwind */
</style>