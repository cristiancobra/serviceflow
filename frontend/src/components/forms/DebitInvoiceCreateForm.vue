<template>
  <div>
    <!-- Modal -->
    <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.25)">
      <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
          <div class="flex items-center gap-3">
            <font-awesome-icon icon="fa-solid fa-file-invoice" class="text-primary text-xl" />
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

          <!-- Valor Total -->
          <div>
            <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">
              Valor Total
            </label>
            <div class="flex items-center">
              <span class="text-gray-500 mr-2">R$</span>
              <input
                id="amount"
                v-model.number="totalAmount"
                @input="updateInstallments"
                type="number"
                step="0.01"
                min="0"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-colors"
                placeholder="0,00"
                required
              />
            </div>
          </div>

          <!-- Quantidade de Parcelas -->
          <div>
            <label for="installment_quantity" class="block text-sm font-semibold text-gray-700 mb-2">
              Quantidade de Parcelas
            </label>
            <select
              id="installment_quantity"
              v-model.number="installmentQuantity"
              @change="updateInstallments"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-colors"
              required
            >
              <option v-for="n in 24" :key="n" :value="n">{{ n }}x</option>
            </select>
          </div>

          <!-- Lista de Parcelas -->
          <div v-if="installmentQuantity > 1" class="border-t border-gray-200 pt-4">
            <div class="flex items-center space-x-2 mb-4">
              <div class="w-2 h-6 bg-red-500 rounded-full"></div>
              <h4 class="text-base font-bold text-gray-900 uppercase tracking-wide">
                Parcelamento
              </h4>
            </div>

            <div class="space-y-3">
              <div 
                v-for="(price, index) in form.prices" 
                :key="index"
                class="flex flex-col sm:flex-row gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200"
              >
                <div class="flex items-center flex-1">
                  <span class="inline-flex items-center justify-center w-7 h-7 bg-red-100 text-red-800 text-sm font-bold rounded-full mr-3">
                    {{ index + 1 }}
                  </span>
                  <label class="text-sm font-semibold text-gray-700">
                    Valor da Parcela {{ index + 1 }}
                  </label>
                </div>
                <div class="flex items-center gap-2 sm:max-w-xs">
                  <span class="text-gray-500">R$</span>
                  <input
                    v-model.number="form.prices[index]"
                    @input="adjustPrices(index)"
                    type="number"
                    step="0.01"
                    min="0"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-colors"
                    required
                  />
                </div>
              </div>
            </div>

            <div class="mt-4 p-4 bg-red-50 rounded-lg border-2 border-red-200">
              <div class="flex flex-col sm:flex-row gap-3 items-center">
                <div class="flex-1">
                  <label class="text-sm font-semibold text-red-700">Total das Parcelas</label>
                </div>
                <div class="flex items-center gap-2 sm:max-w-xs">
                  <span class="text-gray-700 font-bold">R$</span>
                  <span class="text-lg font-bold text-gray-900">{{ totalPrices }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Data de Vencimento da Primeira Parcela -->
          <div>
            <label for="due_date" class="block text-sm font-semibold text-gray-700 mb-2">
              Data de Vencimento {{ installmentQuantity > 1 ? 'da 1ª Parcela' : '' }}
            </label>
            <input
              id="due_date"
              v-model="form.date_due"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-colors"
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
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-colors resize-none"
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
              class="flex-1 px-4 py-2 bg-primary hover:bg-primary-dark disabled:bg-gray-400 text-white font-medium rounded-lg transition-colors"
            >
              {{ isSubmitting ? 'Criando...' : `Criar ${installmentQuantity} Fatura(s)` }}
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
  emits: ["invoice-created", "update:modelValue"],
  components: {
    LeadsSelectInput,
  },
  props: {
    proposal: {
      type: Object,
      required: true,
    },
    modelValue: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      isSubmitting: false,
      errorMessage: "",
      totalAmount: 0,
      installmentQuantity: 1,
      form: {
        lead_id: null,
        prices: [],
        date_due: this.getTodayDate(),
        observations: "",
        proposal_id: this.proposal.id,
        type: "debit",
      },
    };
  },
  computed: {
    totalPrices() {
      return this.form.prices.reduce((acc, price) => acc + (isNaN(price) ? 0 : price), 0).toFixed(2);
    }
  },
  watch: {
    proposal: {
      handler(newProposal) {
        this.form.proposal_id = newProposal.id;
      },
      immediate: true,
    },
    modelValue(newVal) {
      if (newVal) {
        this.openModal();
      }
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
    initializePrices() {
      if (this.totalAmount <= 0 || this.installmentQuantity <= 0) {
        return [];
      }
      
      const pricePerInstallment = (this.totalAmount / this.installmentQuantity).toFixed(2);
      const prices = Array(this.installmentQuantity).fill(parseFloat(pricePerInstallment));

      // Ajustar a última parcela para compensar a diferença
      const totalCalculated = prices.reduce((acc, price) => acc + price, 0);
      const difference = this.totalAmount - totalCalculated;
      prices[this.installmentQuantity - 1] += difference;

      return prices;
    },
    updateInstallments() {
      this.form.prices = this.initializePrices();
    },
    adjustPrices(changedIndex) {
      const prices = [...this.form.prices];
      const newPrice = parseFloat(prices[changedIndex]) || 0;
      
      const sumBefore = prices.slice(0, changedIndex).reduce((acc, price) => acc + price, 0);
      const remaining = this.totalAmount - sumBefore;

      if (newPrice > remaining) {
        prices[changedIndex] = remaining;
      }

      const remainingAfterChange = this.totalAmount - prices.slice(0, changedIndex + 1).reduce((acc, price) => acc + price, 0);
      const remainingInstallments = prices.length - (changedIndex + 1);
      
      if (remainingInstallments > 0) {
        const newPricePerInstallment = (remainingAfterChange / remainingInstallments).toFixed(2);

        for (let i = changedIndex + 1; i < prices.length; i++) {
          prices[i] = parseFloat(newPricePerInstallment);
        }

        // Ajustar a última parcela para compensar a diferença
        const totalCalculated = prices.reduce((acc, price) => acc + price, 0);
        const difference = this.totalAmount - totalCalculated;
        prices[prices.length - 1] += difference;
      }

      this.form.prices = prices;
    },
    openModal() {
      this.errorMessage = "";
      this.totalAmount = 0;
      this.installmentQuantity = 1;
      this.form.prices = [];
      this.form.date_due = this.getTodayDate();
      this.form.observations = "";
      this.form.lead_id = null;
    },
    closeModal() {
      this.$emit("update:modelValue", false);
      this.errorMessage = "";
    },
    async submitForm() {
      // Validações
      if (!this.form.lead_id) {
        this.errorMessage = "Selecione um fornecedor.";
        return;
      }

      if (this.totalAmount <= 0) {
        this.errorMessage = "O valor deve ser maior que zero.";
        return;
      }

      if (!this.form.date_due) {
        this.errorMessage = "A data de vencimento é obrigatória.";
        return;
      }

      // Validar soma das parcelas se for parcelado
      if (this.installmentQuantity > 1) {
        const totalCalculated = this.form.prices.reduce((acc, price) => acc + (isNaN(price) ? 0 : price), 0);
        if (Math.abs(totalCalculated - this.totalAmount) > 0.01) {
          const difference = (totalCalculated - this.totalAmount).toFixed(2);
          this.errorMessage = `A soma das parcelas (R$ ${totalCalculated.toFixed(2)}) difere do valor total (R$ ${this.totalAmount.toFixed(2)}) em R$ ${difference}. Ajuste os valores.`;
          return;
        }
      }

      this.isSubmitting = true;
      this.errorMessage = "";

      // Se for parcelado, envia prices; se não, envia price único
      const payload = this.installmentQuantity > 1 ? {
        lead_id: this.form.lead_id,
        prices: this.form.prices,
        date_due: this.form.date_due,
        observations: this.form.observations || null,
        proposal_id: this.proposal.id,
        type: "debit",
      } : {
        lead_id: this.form.lead_id,
        price: this.totalAmount,
        date_due: this.form.date_due,
        observations: this.form.observations || null,
        proposal_id: this.proposal.id,
        type: "debit",
      };

      try {
        const endpoint = this.installmentQuantity > 1 ? "invoices" : "invoices/debit";
        const { data, error } = await this.submitFormCreate(endpoint, payload);

        if (data) {
          this.$emit("invoice-created", data);
          this.closeModal();
        }

        if (error) {
          this.errorMessage = "Erro ao criar fatura(s). Tente novamente.";
          console.error("Erro:", error);
        }
      } catch (error) {
        this.errorMessage = "Erro ao criar fatura(s). Tente novamente.";
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
