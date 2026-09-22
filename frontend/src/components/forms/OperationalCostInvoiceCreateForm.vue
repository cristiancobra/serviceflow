<template>
  <ModalCard
    title="Custo Operacional"
    icon="fa-solid fa-briefcase"
    :compact="compact"
    @close="closeModal"
  >
        <!-- Form -->
        <form id="operationalCostInvoiceCreateForm" @submit.prevent="submitForm" class="space-y-5">
          <!-- Funcionário/Freelancer -->
          <div>
            <label for="employee" class="block text-sm font-semibold text-base-content mb-2">
              Funcionário / Prestador
            </label>
            <select
              id="employee"
              v-model="form.lead_id"
              class="w-full px-3 py-2 border border-base-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-colors"
              required
            >
              <option value="">Selecione um funcionário</option>
              <option
                v-for="employee in employees"
                :key="employee.id"
                :value="employee.id"
              >
                {{ employee.name }} {{ employee.category ? `(${employee.category})` : '' }}
              </option>
            </select>
          </div>

          <!-- Valor Total -->
          <div>
            <label for="amount" class="block text-sm font-semibold text-base-content mb-2">
              Valor Total
            </label>
            <div class="flex items-center">
              <span class="text-base-content/60 mr-2">R$</span>
              <money-input
                name="amount"
                v-model="totalAmount"
                disabled
                class="flex-1 px-3 py-2 border border-base-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-colors bg-base-300"
              />
            </div>
          </div>

          <!-- Quantidade de Parcelas -->
          <div>
            <label for="installment_quantity" class="block text-sm font-semibold text-base-content mb-2">
              Quantidade de Parcelas
            </label>
            <select
              id="installment_quantity"
              v-model.number="installmentQuantity"
              @change="updateInstallments"
              class="w-full px-3 py-2 border border-base-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-colors"
              required
            >
              <option v-for="n in 24" :key="n" :value="n">{{ n }}x</option>
            </select>
          </div>

          <!-- Lista de Parcelas -->
          <div class="border-t border-base-300 pt-4">
            <div class="flex items-center space-x-2 mb-4">
              <div class="w-2 h-6 bg-blue-500 rounded-full"></div>
              <h4 class="text-base font-bold text-base-content uppercase tracking-wide">
                Parcelamento
              </h4>
            </div>

            <div class="space-y-3">
              <div 
                v-for="(price, index) in form.prices" 
                :key="index"
                class="flex flex-col sm:flex-row gap-3 p-3 bg-base-200 rounded-lg border border-base-300"
              >
                <div class="flex items-center flex-1">
                  <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-100 text-blue-800 text-sm font-bold rounded-full mr-3">
                    {{ index + 1 }}
                  </span>
                  <label class="text-sm font-semibold text-base-content">
                    Valor da Parcela {{ index + 1 }}
                  </label>
                </div>
                <div class="flex items-center gap-2 sm:max-w-xs">
                  <span class="text-base-content/60">R$</span>
                  <money-input
                    :name="`price-${index}`"
                    :model-value="form.prices[index]"
                    @update:model-value="(value) => { form.prices[index] = value; adjustPrices(index); }"
                    class="flex-1 px-3 py-2 border border-base-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-colors"
                  />
                </div>
              </div>
            </div>

            <div class="mt-4 p-4 bg-blue-50 rounded-lg border-2 border-blue-200">
              <div class="flex flex-col sm:flex-row gap-3 items-center">
                <div class="flex-1">
                  <label class="text-sm font-semibold text-blue-700">Total das Parcelas</label>
                </div>
                <div class="flex items-center gap-2 sm:max-w-xs">
                  <span class="text-base-content font-bold">R$</span>
                  <span class="text-lg font-bold text-base-content">{{ totalPrices }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Data de Vencimento da Primeira Parcela -->
          <div>
            <label for="due_date" class="block text-sm font-semibold text-base-content mb-2">
              Data de Vencimento da 1ª Parcela
            </label>
            <input
              id="due_date"
              v-model="form.date_due"
              type="date"
              class="w-full px-3 py-2 border border-base-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-colors"
              required
            />
          </div>

          <!-- Observações -->
          <div>
            <label for="observations" class="block text-sm font-semibold text-base-content mb-2">
              Observações (opcional)
            </label>
            <textarea
              id="observations"
              v-model="form.observations"
              rows="3"
              class="w-full px-3 py-2 border border-base-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-colors resize-none"
              placeholder="Custo operacional referente à proposta..."
            ></textarea>
          </div>

          <!-- Mensagem de erro -->
          <div v-if="errorMessage" class="p-3 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-700">{{ errorMessage }}</p>
          </div>
        </form>

    <template #footer>
      <button
        type="button"
        class="px-6 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg font-semibold hover:bg-base-200 transition-colors"
        @click="closeModal"
      >
        Cancelar
      </button>
      <button
        type="submit"
        form="operationalCostInvoiceCreateForm"
        :disabled="isSubmitting"
        class="px-6 py-2 bg-primary hover:opacity-90 disabled:opacity-50 text-white rounded-lg font-semibold transition-colors flex items-center gap-2"
      >
        <font-awesome-icon icon="fa-solid fa-plus" />
        {{ isSubmitting ? 'Criando...' : `Criar ${installmentQuantity} Fatura(s)` }}
      </button>
    </template>
  </ModalCard>
</template>

<script>
import { submitFormCreate, index, get } from "@/utils/requests/httpUtils";
import MoneyInput from "./inputs/money/MoneyInput.vue";
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
  name: "OperationalCostInvoiceCreateForm",
  emits: ["new-invoice-event", "close"],
  components: {
    MoneyInput,
    ModalCard,
  },
  props: {
    proposal: {
      type: Object,
      required: true,
    },
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      isSubmitting: false,
      errorMessage: "",
      employees: [],
      operationalBalance: null,
      totalAmount: 0,
      installmentQuantity: 1,
      form: {
        lead_id: null,
        prices: [],
        date_due: this.getDefaultDueDate(),
        observations: `Custo operacional da proposta ${this.proposal?.name || this.proposal?.id || ''}`,
        proposal_id: this.proposal?.id,
        type: "debit",
        category: "operational",
      },
    };
  },
  computed: {
    totalPrices() {
      return this.form.prices.reduce((acc, price) => acc + (isNaN(price) ? 0 : price), 0).toFixed(2);
    }
  },
  async mounted() {
    await this.loadEmployees();
    await this.initializeForm();
  },
  methods: {
    submitFormCreate,
    async loadEmployees() {
      try {
        const allLeads = await index("leads");
        this.employees = allLeads.filter(
          lead => lead.category === 'employee' || lead.category === 'freelancer'
        );
        if (this.employees.length > 0) {
          this.form.lead_id = this.employees[0].id;
        }
      } catch (error) {
        console.error("Erro ao carregar funcionários:", error);
        this.errorMessage = "Erro ao carregar lista de funcionários";
      }
    },
    getDefaultDueDate() {
      const date = new Date();
      date.setDate(date.getDate() + 30);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    },
    initializePrices() {
      const pricePerInstallment = parseFloat((this.totalAmount / this.installmentQuantity).toFixed(2));
      const prices = Array(this.installmentQuantity).fill(pricePerInstallment);

      // Calcular a diferença com precisão de 2 casas decimais
      const totalCalculated = parseFloat((pricePerInstallment * this.installmentQuantity).toFixed(2));
      const difference = parseFloat((this.totalAmount - totalCalculated).toFixed(2));
      
      // Ajustar a última parcela para compensar a diferença
      prices[this.installmentQuantity - 1] = parseFloat((pricePerInstallment + difference).toFixed(2));

      return prices;
    },
    updateInstallments() {
      this.form.prices = this.initializePrices();
    },
    adjustPrices(changedIndex) {
      const prices = [...this.form.prices];
      const newPrice = parseFloat(parseFloat(prices[changedIndex]).toFixed(2)) || 0;
      
      // Arredondar para 2 casas decimais
      prices[changedIndex] = newPrice;
      
      const sumBefore = parseFloat(prices.slice(0, changedIndex).reduce((acc, price) => acc + price, 0).toFixed(2));
      const remaining = parseFloat((this.totalAmount - sumBefore).toFixed(2));

      if (newPrice > remaining) {
        prices[changedIndex] = remaining;
      }

      const sumUpToCurrent = parseFloat(prices.slice(0, changedIndex + 1).reduce((acc, price) => acc + price, 0).toFixed(2));
      const remainingAfterChange = parseFloat((this.totalAmount - sumUpToCurrent).toFixed(2));
      const remainingInstallments = prices.length - (changedIndex + 1);
      
      if (remainingInstallments > 0) {
        const newPricePerInstallment = parseFloat((remainingAfterChange / remainingInstallments).toFixed(2));

        for (let i = changedIndex + 1; i < prices.length; i++) {
          prices[i] = newPricePerInstallment;
        }

        // Recalcular a diferença e ajustar a última parcela
        const totalRecalculated = parseFloat(prices.reduce((acc, price) => acc + price, 0).toFixed(2));
        const finalDifference = parseFloat((this.totalAmount - totalRecalculated).toFixed(2));
        
        if (finalDifference !== 0) {
          prices[prices.length - 1] = parseFloat((prices[prices.length - 1] + finalDifference).toFixed(2));
        }
      }

      this.form.prices = prices;
    },
    async initializeForm() {
      this.errorMessage = "";

      try {
        const response = await get(`proposals/${this.proposal.id}/operational-cost-balance`);
        this.operationalBalance = response;
        this.totalAmount = this.operationalBalance.balance;
      } catch (error) {
        console.error("Erro ao buscar saldo operacional:", error);
        this.totalAmount = this.proposal?.total_operational_cost || 0;
      }

      this.installmentQuantity = 1;
      this.form.prices = this.initializePrices();
      this.form.date_due = this.getDefaultDueDate();

      if (this.employees.length > 0 && !this.form.lead_id) {
        this.form.lead_id = this.employees[0].id;
      }
    },
    closeModal() {
      this.$emit("close");
      this.errorMessage = "";
    },
    async submitForm() {
      // Validações
      if (!this.form.lead_id) {
        this.errorMessage = "Selecione um funcionário/freelancer.";
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

      // Validar soma das parcelas
      const totalCalculated = this.form.prices.reduce((acc, price) => acc + (isNaN(price) ? 0 : price), 0);
      if (Math.abs(totalCalculated - this.totalAmount) > 0.01) {
        const difference = (totalCalculated - this.totalAmount).toFixed(2);
        this.errorMessage = `A soma das parcelas (R$ ${totalCalculated.toFixed(2)}) difere do valor total (R$ ${this.totalAmount.toFixed(2)}) em R$ ${difference}. Ajuste os valores.`;
        return;
      }

      this.isSubmitting = true;
      this.errorMessage = "";

      const payload = {
        lead_id: this.form.lead_id,
        prices: this.form.prices,
        date_due: this.form.date_due,
        observations: this.form.observations || null,
        proposal_id: this.proposal.id,
        type: "debit",
        category: "operational",
      };

      try {
        const { data, error } = await this.submitFormCreate("invoices", payload);

        if (data) {
          this.$emit("new-invoice-event", data);
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
