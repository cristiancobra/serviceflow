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
      <SearchInput v-model="searchTerm" placeholder="Buscar tarefas" />

      <section class="">
        <!-- Agrupamento por mês -->
        <div v-for="monthGroup in groupedByMonth" :key="monthGroup.monthKey" class="mb-8">
          <!-- Header do Mês -->
          <div class="flex items-center mb-4">
            <div class="flex items-center gap-3 bg-white pt-5 pb-0">
              <span class="font-bold text-black text-lg whitespace-nowrap uppercase">{{ monthGroup.monthLabel
              }}</span>
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

                <!-- colubna de ícones-->
                <div class="icons-column">
                  <!-- Ícone de cancelamento (X vermelho) ou conclusão (check verde) -->
                  <font-awesome-icon v-if="localTask.date_canceled" icon="fas fa-times-circle"
                    class="text-2xl text-red-500" title="Tarefa cancelada" />
                  <font-awesome-icon v-else icon="fas fa-check-circle" class="text-2xl" :class="isValidDate(localTask.date_conclusion) ? 'text-success' : 'text-gray-400'
                    " />

                  <user-avatar
                    :photo="userData.photo"
                    :name="userData.name"
                    :user-id="userData.id"
                  />
                </div>

                <!-- Coluna de Oportunidade/Projeto -->
                <div v-if="showOpportunityColumn" class="min-w-[350px] max-w-[350px] flex items-center justify-start gap-2">

                  <template v-if="localTask.opportunity">
                    <!-- Avatar da Empresa -->
                    <div v-if="editingCompany[localTask.id]" class="inline-edit-entity">
                      <companies-select-input
                        v-model="selectedCompanyId"
                        name="company_id"
                        fieldsToDisplay="legal_name"
                        fieldNull="Nenhuma"
                        @update:modelValue="saveCompany(localTask.id, $event)"
                      />
                      <button @click="cancelEditCompany(localTask.id)" class="btn-cancel-edit ms-2" title="Cancelar">
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

                    <router-link class="flex no-underline text-inherit items-center gap-2"
                      :to="{
                        name: 'opportunityShow',
                        params: { id: localTask.opportunity.id },
                        query: { tab: 'tasks' },
                        hash: '#task-' + localTask.id
                      }">
                      <div class="flex items-center gap-2 font-medium text-sm"
                        :class="getColorClassForName(localTask.opportunity.name)">
                        <font-awesome-icon icon="fa-solid fa-bullseye"
                          :class="getColorClassForName(localTask.opportunity.name)" />
                        {{ trimName(localTask.opportunity.name) }}
                      </div>
                    </router-link>
                  </template>
                  
                  <template v-else-if="localTask.project">
                    <!-- Avatar da Empresa do Projeto -->
                    <company-avatar
                      :photo="localTask.project.company?.photo"
                      :business-name="localTask.project.company?.business_name"
                      :legal-name="localTask.project.company?.legal_name"
                      :company-id="localTask.project.company?.id"
                    />

                    <router-link class="flex no-underline text-inherit items-center gap-2"
                      :to="{
                        name: 'projectShow',
                        params: { id: localTask.project.id },
                        hash: '#task-' + localTask.id
                      }">
                      <div class="flex items-center gap-2 font-medium text-sm"
                        :class="getColorClassForName(localTask.project.name)">
                        <font-awesome-icon icon="fa-solid fa-folder-open"
                          :class="getColorClassForName(localTask.project.name)" />
                        {{ trimName(localTask.project.name) }}
                      </div>
                    </router-link>
                  </template>
                  
                  <template v-else>
                    <!-- Avatar genérico para tarefas sem oportunidade ou projeto -->
                    <company-avatar />

                    <div v-if="editingOpportunity[localTask.id]" class="inline-edit-opportunity flex-1">
                      <opportunities-open-select-input v-model="selectedOpportunityId" fieldsToDisplay="name"
                        :autoSelect="false" fieldNull="Nenhuma"
                        @update:modelValue="saveOpportunity(localTask.id, $event)" />
                      <button @click="cancelEditOpportunity(localTask.id)" class="btn-cancel-edit ms-2"
                        title="Cancelar">
                        <font-awesome-icon icon="fa-solid fa-times" />
                      </button>
                    </div>
                    <p v-else @click="startEditOpportunity(localTask.id)"
                      class="text-sm text-gray-400 hover:text-primary cursor-pointer"
                      title="Clique para vincular a uma oportunidade">
                      ----
                    </p>
                  </template>
                </div>

                <!-- coluna do nome da tarefa  -->
                <div class="flex flex-row flex-[6] items-center justify-start m-0 gap-2">
                  <department-badge :task="localTask" @department-updated="handleDepartmentUpdated" />
                  <div class="flex-1">
                    <text-editable-field name="name" v-model="localTask.name"
                      placeholder="descrição detalhada da tarefa" @save="updateTask('name', $event, localTask.id)" />
                  </div>
                </div>



                <div class="flex items-center justify-center text-center text-primary mr-4 font-bold">
                  {{ formatDuration(localTask.duration_time) }}
                </div>

                <div class="date-column">
                  <font-awesome-icon icon="fa-solid fa-exclamation-circle"
                    :class="(isValidDate(localTask.date_conclusion) || localTask.date_canceled) ? 'text-gray-600' : 'text-error'"
                    class="me-2" />
                  <date-time-editable-input v-model="localTask.date_due"
                    :classText="getDeadlineClass(localTask.date_due)" :classIcon="getDeadlineClass(localTask.date_due)"
                    @save="updateTask('date_due', $event, localTask.id)" />
                </div>

                <div class="date-column">
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
          </div>
        </div>
      </section>
    </section>

    <!-- Modal de Detalhes da Tarefa -->
    <task-detail-modal
      v-model="showTaskModal"
      :taskId="selectedTaskId"
      @task-updated="handleTaskUpdated"
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
import TaskDetailModal from "@/components/modals/details/TaskDetailModal.vue";
import DepartmentBadge from "@/components/badges/DepartmentBadge.vue";
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
      totalTasks: 0,
      editingOpportunity: {},
      selectedOpportunityId: null,
      editingCompany: {},
      selectedCompanyId: null,
      editingLead: {},
      selectedLeadId: null,
      editingProjectCompany: {},
      selectedProjectCompanyId: null,
      showTaskModal: false,
      selectedTaskId: null,
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
    TaskCreateForm,
    TextEditableField,
    SearchInput,
    UserAvatar,
    TaskDetailModal,
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

        console.log("✅ Tarefa de organização criada com sucesso!");

      } catch (error) {
        console.error("❌ Erro ao criar tarefa de organização:", error);
      }
    },
    openTaskModal(taskId) {
      this.selectedTaskId = taskId;
      this.showTaskModal = true;
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
  },
  computed: {
    ...mapState({
      userData: (state) => state.userData,
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
.inline-edit-opportunity {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.inline-edit-entity {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  min-width: 200px;
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