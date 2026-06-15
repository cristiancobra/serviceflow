<template>
  <div>
    <div v-if="modelValue && task" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop">
      <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-gradient-to-r from-blue-50 to-blue-25 border-b border-gray-200 px-8 py-6">
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <div class="flex items-center gap-4 mb-2">
                <!-- Status Icon -->
                <font-awesome-icon 
                  v-if="task.date_canceled" 
                  icon="fas fa-times-circle"
                  class="text-3xl text-red-500" 
                  title="Tarefa cancelada" 
                />
                <font-awesome-icon 
                  v-else 
                  icon="fas fa-check-circle" 
                  class="text-3xl" 
                  :class="isValidDate(task.date_conclusion) ? 'text-success' : 'text-gray-400'"
                />
                
                <h3 class="text-2xl font-bold text-gray-800">{{ task.name }}</h3>
              </div>
              
              <!-- Oportunidade/Projeto -->
              <div v-if="task.opportunity" class="flex items-center gap-2 text-sm mt-2">
                <font-awesome-icon icon="fa-solid fa-bullseye" class="text-primary" />
                <router-link 
                  :to="{ name: 'opportunityShow', params: { id: task.opportunity.id } }"
                  class="text-primary hover:underline font-medium"
                >
                  {{ task.opportunity.name }}
                </router-link>
              </div>
              
              <div v-else-if="task.project" class="flex items-center gap-2 text-sm mt-2">
                <font-awesome-icon icon="fa-solid fa-folder-open" class="text-primary" />
                <router-link 
                  :to="{ name: 'projectShow', params: { id: task.project.id } }"
                  class="text-primary hover:underline font-medium"
                >
                  {{ task.project.name }}
                </router-link>
              </div>
            </div>
            
            <close-button @click="closeModal" />
          </div>
        </div>

        <!-- Body -->
        <div class="px-8 py-6">
          <!-- Datas e Duração -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
              <div class="flex items-center gap-2 mb-2">
                <font-awesome-icon icon="fa-solid fa-exclamation-circle" class="text-error" />
                <label class="text-sm font-semibold text-gray-700">Data de Vencimento</label>
              </div>
              <date-time-editable-input 
                v-model="task.date_due"
                :classText="getDeadlineClass(task.date_due)"
                @save="updateTask('date_due', $event)"
              />
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
              <div class="flex items-center gap-2 mb-2">
                <font-awesome-icon icon="fa-solid fa-check-circle" class="text-success" />
                <label class="text-sm font-semibold text-gray-700">Data de Conclusão</label>
              </div>
              <date-time-editable-input 
                name="date_conclusion" 
                v-model="task.date_conclusion"
                @save="updateTask('date_conclusion', $event)"
              />
            </div>

            <div class="bg-primary-50 rounded-lg p-4">
              <div class="flex items-center gap-2 mb-2">
                <font-awesome-icon icon="fa-solid fa-clock" class="text-primary" />
                <label class="text-sm font-semibold text-gray-700">Duração</label>
              </div>
              <p class="text-2xl font-bold text-primary">{{ formatDuration(task.duration_time) }}</p>
            </div>
          </div>

          <!-- Descrição -->
          <div class="mb-6 bg-gray-50 rounded-lg p-4">
            <text-area-editable-input 
              name="description"
              label="Descrição"
              v-model="task.description"
              placeholder="Adicione uma descrição detalhada da tarefa"
              @save="updateTask('description', $event)"
            />
          </div>

          <!-- Links -->
          <div class="mb-6">
            <task-links-list 
              :links="task.links || []"
              :show-header="true"
              :show-task-column="false"
              @delete-link="confirmDeleteLink"
              @copy-link="copyLink"
            >
              <template #action>
                <button-new-form 
                  target="link" 
                  @open-modal="showLinkForm = true" 
                />
                <link-create-form
                  v-model="showLinkForm"
                  :taskId="task.id"
                  @new-link-event="addLinkCreated"
                />
              </template>
            </task-links-list>
          </div>

          <!-- Jornadas -->
          <div v-if="task.journeys && task.journeys.length > 0" class="mb-6">
            <div class="flex items-center justify-between mb-4">
              <h4 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <font-awesome-icon icon="fa-solid fa-route" class="text-primary" />
                Jornadas
                <span class="text-sm font-normal text-gray-500">({{ task.journeys.length }})</span>
              </h4>
            </div>
            <journeys-list-from-opportunity 
              :journeys="task.journeys" 
              :taskId="task.id"
              @update-task-duration="refreshTask"
            />
          </div>

          <!-- Formulário de Nova Jornada -->
          <div v-if="showJourneyForm" class="mb-6">
            <journey-create-form 
              :taskId="task.id"
              @new-journey-event="addJourneyCreated"
              @close="showJourneyForm = false"
            />
          </div>

          <!-- Área de Cancelamento -->
          <div v-if="showCancelArea" class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
            <h4 class="text-lg font-bold text-red-700 mb-4">Cancelar Tarefa</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <date-time-editable-input 
                name="date_canceled" 
                v-model="task.date_canceled"
                label="Data de Cancelamento"
                @save="updateTask('date_canceled', $event)"
              />
              <cancellation-reason-select-input 
                name="cancellation_reason" 
                v-model="task.cancellation_reason"
                :disabled="!task.date_canceled" 
                @update:modelValue="updateTask('cancellation_reason', $event)"
              />
            </div>
          </div>
        </div>

        <!-- Footer com Ações -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-8 py-4">
          <div class="flex justify-between items-center">
            <!-- Ações Rápidas -->
            <div class="flex gap-3">
              <button
                type="button"
                class="px-4 py-2 bg-purple-500 text-white rounded-lg font-semibold hover:bg-purple-600 transition-colors"
                @click="quickStartJourney"
                title="Iniciar jornada agora"
              >
                <font-awesome-icon icon="fa-solid fa-bolt" class="me-2" />
                Iniciar Jornada
              </button>

              <add-journey-button
                :is-open="showJourneyForm"
                @click="toggleJourneyForm"
                title="Adicionar jornada manualmente"
              />

              <button
                v-if="!task.date_conclusion"
                type="button"
                class="px-4 py-2 bg-success text-white rounded-lg font-semibold hover:bg-green-700 transition-colors"
                @click="finishTask"
                title="Finalizar tarefa"
              >
                <font-awesome-icon icon="fa-solid fa-check" class="me-2" />
                Finalizar
              </button>

              <button
                type="button"
                class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-colors"
                @click="toggleCancelArea"
                :title="showCancelArea ? 'Ocultar cancelamento' : 'Cancelar tarefa'"
              >
                <font-awesome-icon icon="fa-solid fa-times-circle" class="me-2" />
                {{ showCancelArea ? 'Ocultar' : 'Cancelar' }}
              </button>
            </div>

            <!-- Botão Fechar -->
            <button
              type="button"
              class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
              @click="closeModal"
            >
              Fechar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { formatDuration } from "@/utils/date/dateUtils";
import { getDeadlineClass } from "@/utils/card/cardUtils";
import { BACKEND_URL, TASK_URL_PARAMETER, JOURNEY_URL } from "@/config/apiConfig";
import DateTimeEditableInput from "@/components/fields/datetime/DateTimeEditableInput.vue";
import TextAreaEditableInput from "@/components/forms/inputs/textarea/TextAreaEditableInput.vue";
import JourneysListFromOpportunity from "@/components/lists/JourneysListFromOpportunity.vue";
import CancellationReasonSelectInput from "@/components/forms/selects/CancellationReasonSelectInput.vue";
import JourneyCreateForm from "@/components/forms/JourneyCreateForm.vue";
import LinkCreateForm from "@/components/forms/LinkCreateForm.vue";
import CloseButton from "@/components/buttons/CloseButton.vue";
import AddJourneyButton from "@/components/buttons/AddJourneyButton.vue";
import TaskLinksList from "@/components/lists/TaskLinksList.vue";
import ButtonNewForm from "@/components/buttons/ButtonNewForm.vue";

export default {
  name: "TaskDetailModal",
  components: {
    DateTimeEditableInput,
    TextAreaEditableInput,
    JourneysListFromOpportunity,
    CancellationReasonSelectInput,
    JourneyCreateForm,
    LinkCreateForm,
    CloseButton,
    AddJourneyButton,
    TaskLinksList,
    ButtonNewForm,
  },
  props: {
    modelValue: {
      type: Boolean,
      required: true,
    },
    taskId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      task: null,
      loading: false,
      showCancelArea: false,
      showJourneyForm: false,
      showLinkForm: false,
    };
  },
  methods: {
    formatDuration,
    getDeadlineClass,
    
    async loadTask() {
      if (!this.taskId) return;
      
      this.loading = true;
      try {
        const response = await axios.get(`${BACKEND_URL}${TASK_URL_PARAMETER}${this.taskId}`);
        this.task = response.data.data;
        
        // Se tarefa já está cancelada, mostra a área de cancelamento
        if (this.task.date_canceled) {
          this.showCancelArea = true;
        }
      } catch (error) {
        console.error("Erro ao carregar tarefa:", error);
      } finally {
        this.loading = false;
      }
    },

    async updateTask(fieldName, editedValue) {
      const updatedField = {};
      updatedField[fieldName] = editedValue;

      try {
        const response = await axios.put(
          `${BACKEND_URL}${TASK_URL_PARAMETER}${this.taskId}`,
          updatedField
        );
        this.task = response.data.data;
        this.$emit('task-updated', this.task);
      } catch (error) {
        console.error("Erro ao atualizar a tarefa:", error);
      }
    },

    async quickStartJourney() {
      try {
        const quickForm = {
          task_id: this.taskId,
          start: new Date(),
          user_timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
        };

        const response = await axios.post(`${BACKEND_URL}journeys`, quickForm);
        const newJourney = response.data.data;
        
        // Adiciona a nova jornada à lista
        if (!this.task.journeys) this.task.journeys = [];
        this.task.journeys.unshift(newJourney);
        
        this.$emit('task-updated', this.task);
      } catch (error) {
        console.error("Erro ao iniciar jornada:", error);
      }
    },

    async finishTask() {
      if (this.task.journeys && this.task.journeys.length > 0) {
        // Ordena e pega a data da última jornada
        const sortedJourneys = [...this.task.journeys].sort(
          (a, b) => new Date(b.end) - new Date(a.end)
        );
        const journeyEnd = sortedJourneys[0].end;
        await this.updateTask("date_conclusion", journeyEnd);
      } else {
        // Finaliza com data atual
        await this.updateTask("date_conclusion", new Date().toISOString());
      }
    },

    toggleCancelArea() {
      this.showCancelArea = !this.showCancelArea;
    },

    toggleJourneyForm() {
      this.showJourneyForm = !this.showJourneyForm;
    },

    addJourneyCreated({ journey }) {
      if (!this.task.journeys) this.task.journeys = [];
      this.task.journeys.unshift(journey);
      this.showJourneyForm = false;
      this.$emit('task-updated', this.task);
    },

    addLinkCreated(linkData) {
      if (!this.task.links) this.task.links = [];
      this.task.links.unshift(linkData);
      this.showLinkForm = false;
      this.$emit('task-updated', this.task);
    },

    async deleteLink(linkId) {
      try {
        await axios.delete(`${BACKEND_URL}links/${linkId}`);
        this.task.links = this.task.links.filter(link => link.id !== linkId);
        this.$emit('task-updated', this.task);
      } catch (error) {
        console.error("Erro ao deletar link:", error);
      }
    },

    confirmDeleteLink(linkId) {
      if (window.confirm("Tem certeza que deseja excluir este link?")) {
        this.deleteLink(linkId);
      }
    },

    copyLink(url) {
      navigator.clipboard.writeText(url).then(() => {
        alert("Link copiado para a área de transferência!");
      }).catch(err => {
        console.error("Erro ao copiar link:", err);
      });
    },

    async refreshTask() {
      await this.loadTask();
      this.$emit('task-updated', this.task);
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

    closeModal() {
      this.$emit("update:modelValue", false);
    },
  },
  watch: {
    modelValue(newVal) {
      if (newVal) {
        this.loadTask();
      }
    },
  },
};
</script>

<style scoped>
/* Animação suave para o modal */
.fixed {
  animation: fadeIn 0.2s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* Background com desfoque */
.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px); /* Safari */
}

/* Estilo para o div de visualização (não editando) */
:deep(.w-full.border-none.p-1) {
  color: #6b7280; /* text-gray-500 - placeholder */
  font-style: italic;
  min-height: 80px;
}

/* Quando tem conteúdo no div de visualização */
:deep(.w-full.border-none.p-1:not(:empty)) {
  color: #374151; /* text-gray-700 - texto normal */
  font-style: normal;
}

/* Estilo para o textarea quando está editando */
:deep(textarea) {
  color: #1f2937 !important; /* text-gray-800 - texto digitado visível */
  background-color: white !important;
}

/* Força visibilidade dos botões Salvar/Cancelar */
:deep(button) {
  display: inline-block !important;
  opacity: 1 !important;
  visibility: visible !important;
  pointer-events: auto !important;
}

/* Garante contraste nos botões */
:deep(.bg-primary-500) {
  background-color: #3b82f6 !important;
  color: white !important;
}

:deep(.bg-gray-500) {
  background-color: #6b7280 !important;
  color: white !important;
}
</style>
