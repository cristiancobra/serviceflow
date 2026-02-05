<template>
  <div>
    <!-- Botão para abrir o modal -->
    <button 
      @click="openModal"
      class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm flex items-center gap-2"
      title="Gerar fatura de débito"
    >
      <font-awesome-icon icon="fa-solid fa-plus-circle" />
      Fatura de Débito
    </button>

    <!-- Modal -->
    <div v-if="isModalVisible" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.25)">
      <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
          <div class="flex items-center gap-3">
            <font-awesome-icon icon="fa-solid fa-file-invoice" class="text-green-600 text-xl" />
            <h2 class="text-xl font-semibold text-gray-900">Fatura de Débito</h2>
          </div>
          <button 
            @click="closeModal" 
            class="text-gray-400 hover:text-gray-600 transition-colors"
            aria-label="Fechar"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="p-6 space-y-5">
          <!-- Fornecedor -->
          <div>
            <leads-select-input 
              label="Fornecedor" 
              v-model="form.lead_id" 
              fieldsToDisplay="name" 
              fieldNull="Selecione um fornecedor"
            />
          </div>

          <!-- Valor -->
          <div>
            <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">
              Valor
            </label>
            <div class="flex items-center">
              <span class="text-gray-500 mr-2">R$</span>
              <input
                id="amount"
                v-model.number="form.price"
                type="number"
                step="0.01"
                min="0"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                placeholder="0,00"
                required
              />
            </div>
          </div>

          <!-- Data de Vencimento -->
          <div>
            <label for="due_date" class="block text-sm font-semibold text-gray-700 mb-2">
              Data de Vencimento
            </label>
            <input
              id="due_date"
              v-model="form.date_due"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
              required
            />
          </div>

          <!-- Observações -->
          <div>
            <label for="observations" class="block text-sm font-semibold text-gray-700 mb-2">
              Observações (opcional)
            </label>
            <textarea
              id="observations"
              v-model="form.observations"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors resize-none"
              placeholder="Adicione notas sobre esta fatura..."
            ></textarea>
          </div>

          <!-- Mensagem de erro -->
          <div v-if="errorMessage" class="p-3 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-700">{{ errorMessage }}</p>
          </div>

          <!-- Botões -->
          <div class="flex gap-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeModal"
              class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition-colors"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-medium rounded-lg transition-colors"
            >
              {{ isSubmitting ? 'Criando...' : 'Criar Fatura' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { submitFormCreate } from "@/utils/requests/httpUtils";
import LeadsSelectInput from "./selects/LeadsSelectInput.vue";

export default {
  name: "DebitInvoiceCreateForm",
  emits: ["invoice-created"],
  components: {
    LeadsSelectInput,
  },
  props: {
    proposal: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      isModalVisible: false,
      isSubmitting: false,
      errorMessage: "",
      form: {
        lead_id: null,
        price: 0,
        date_due: this.getTodayDate(),
        observations: "",
        proposal_id: this.proposal.id,
        type: "debit",
      },
    };
  },
  watch: {
    proposal: {
      handler(newProposal) {
        this.form.proposal_id = newProposal.id;
        // Atualizar valor padrão quando a proposta mudar
        if (newProposal.total_operational_cost) {
          this.form.price = newProposal.total_operational_cost;
        }
      },
      immediate: true,
    },
  },
  methods: {
    submitFormCreate,
    getTodayDate() {
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, "0");
      const day = String(today.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    },
    openModal() {
      this.errorMessage = "";
      // Resetar valor padrão quando abre o modal
      if (this.proposal.total_operational_cost) {
        this.form.price = this.proposal.total_operational_cost;
      }
      this.form.date_due = this.getTodayDate();
      this.isModalVisible = true;
    },
    closeModal() {
      this.isModalVisible = false;
      this.errorMessage = "";
    },
    async submitForm() {
      // Validações
      if (!this.form.lead_id) {
        this.errorMessage = "Selecione um fornecedor.";
        return;
      }

      if (this.form.price <= 0) {
        this.errorMessage = "O valor deve ser maior que zero.";
        return;
      }

      if (!this.form.date_due) {
        this.errorMessage = "A data de vencimento é obrigatória.";
        return;
      }

      this.isSubmitting = true;
      this.errorMessage = "";

      const payload = {
        lead_id: this.form.lead_id,
        price: this.form.price,
        date_due: this.form.date_due,
        observations: this.form.observations || null,
        proposal_id: this.proposal.id,
        type: "debit",
        // user_id é atribuído automaticamente pelo backend via Auth::user()->id
      };

      try {
        const { data, error } = await this.submitFormCreate("invoices/debit", payload);

        if (data) {
          this.$emit("invoice-created", data);
          this.closeModal();
        }

        if (error) {
          this.errorMessage = "Erro ao criar fatura. Tente novamente.";
          console.error("Erro:", error);
        }
      } catch (error) {
        this.errorMessage = "Erro ao criar fatura. Tente novamente.";
        console.error("Erro:", error);
      } finally {
        this.isSubmitting = false;
      }
    },
  },
};
</script>

<style scoped>
/* Estilos específicos se necessário */
</style>
