<template>
  <div>
    <!-- Modal -->
    <div
      v-if="modelValue"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      style="background-color: rgba(0, 0, 0, 0.25)"
    >
      <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
          <div class="flex items-center gap-3">
            <font-awesome-icon icon="fa-solid fa-file-invoice-dollar" class="text-red-600 text-xl" />
            <h2 class="text-xl font-semibold text-gray-900">Nova Conta a Pagar</h2>
          </div>
          <button
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600 transition-colors"
            aria-label="Fechar"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="p-6 space-y-5">

          <!-- Nome da conta -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Nome da Conta <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              placeholder="Ex: Internet, Aluguel, Fornecedor X..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors"
              required
            />
          </div>

          <!-- Categoria e Forma de Pagamento -->
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Categoria</label>
              <select
                v-model="form.category"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors"
              >
                <option value="fixed_cost">Custo Fixo</option>
                <option value="recurring">Recorrente</option>
                <option value="supplier">Fornecedor</option>
                <option value="operational">Operacional</option>
                <option value="other">Outro</option>
              </select>
            </div>
            <div class="flex-1">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Forma de Pagamento</label>
              <select
                v-model="form.payment_method"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors"
                @change="handlePaymentMethodChange"
              >
                <option value="pix">Pix</option>
                <option value="boleto">Boleto</option>
                <option value="transfer">Transferência</option>
                <option value="credit_card">Cartão de Crédito</option>
                <option value="debit_card">Cartão de Débito</option>
                <option value="cash">Dinheiro</option>
              </select>
            </div>
          </div>

          <!-- Departamento (Centro de Custo) -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Departamento
              <span class="text-gray-400 font-normal text-xs ml-1">(centro de custo)</span>
            </label>
            <DepartmentsSelectInput
              name="department_id"
              v-model="form.department_id"
              fieldNull="Sem departamento"
            />
          </div>

          <!-- Tipo de Fornecedor -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Fornecedor (opcional)</label>
            <div class="flex gap-4 mb-3">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" v-model="supplierType" value="none" class="mr-1" />
                <span class="text-gray-700 text-sm">Sem fornecedor</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" v-model="supplierType" value="lead" class="mr-1" />
                <span class="text-gray-700 text-sm">Pessoa</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" v-model="supplierType" value="company" class="mr-1" />
                <span class="text-gray-700 text-sm">Empresa</span>
              </label>
            </div>
            <LeadsSelectInput
              v-if="supplierType === 'lead'"
              name="lead_id"
              label="Fornecedor (Pessoa)"
              v-model="form.lead_id"
              fieldsToDisplay="name"
              fieldNull="Selecione um fornecedor"
            />
            <CompaniesSelectInput
              v-if="supplierType === 'company'"
              name="company_id"
              label="Empresa Fornecedora"
              v-model="form.company_id"
              :fieldsToDisplay="['business_name', 'legal_name']"
              fieldNull="Selecione uma empresa"
            />
          </div>

          <!-- Valor e Parcelas -->
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
              <label class="block text-sm font-semibold text-gray-700 mb-1">
                Valor Total <span class="text-red-500">*</span>
              </label>
              <div class="flex items-center gap-2">
                <span class="text-gray-500 font-medium">R$</span>
                <input
                  v-model.number="totalAmount"
                  @input="updatePrices"
                  type="number"
                  step="0.01"
                  min="0"
                  placeholder="0,00"
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors"
                  required
                />
              </div>
            </div>
            <div class="flex-1">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Parcelas</label>
              <select
                v-model.number="installmentQuantity"
                @change="initializePrices"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors"
              >
                <option v-for="n in 24" :key="n" :value="n">{{ n }}x</option>
              </select>
            </div>
          </div>

          <!-- Parcelamento detalhado -->
          <div v-if="installmentQuantity > 1" class="border-t border-gray-200 pt-4">
            <div class="flex items-center gap-2 mb-3">
              <div class="w-2 h-5 bg-red-500 rounded-full"></div>
              <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Parcelamento</h4>
            </div>
            <div class="space-y-2">
              <div
                v-for="(price, index) in form.prices"
                :key="index"
                class="flex items-center gap-3 p-2 bg-gray-50 rounded-lg border border-gray-200"
              >
                <span class="inline-flex items-center justify-center w-6 h-6 bg-red-100 text-red-800 text-xs font-bold rounded-full flex-shrink-0">
                  {{ index + 1 }}
                </span>
                <div class="flex items-center gap-2 flex-1">
                  <span class="text-gray-500 text-sm">R$</span>
                  <input
                    v-model.number="form.prices[index]"
                    @input="adjustPrices(index)"
                    type="number"
                    step="0.01"
                    min="0"
                    class="flex-1 px-2 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-red-500 text-sm"
                    required
                  />
                </div>
              </div>
              <div class="flex justify-between text-sm pt-1 px-1">
                <span class="text-gray-500">Total das parcelas:</span>
                <span :class="Math.abs(totalPrices - totalAmount) > 0.01 ? 'text-red-600 font-bold' : 'text-green-600 font-semibold'">
                  R$ {{ parseFloat(totalPrices).toFixed(2) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Data de Vencimento -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              {{ installmentQuantity > 1 ? 'Data da 1ª Parcela' : 'Data de Vencimento' }}
              <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.date_due"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors"
              required
            />
          </div>

          <!-- Observações -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Observações</label>
            <textarea
              v-model="form.observations"
              rows="3"
              placeholder="Detalhes adicionais..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors resize-none"
            ></textarea>
          </div>

          <!-- Gerar Tarefa Financeira -->
          <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
            <label class="flex items-center gap-3 cursor-pointer">
              <input
                type="checkbox"
                v-model="form.generate_task"
                class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500"
              />
              <div>
                <span class="text-sm font-semibold text-gray-800">Gerar tarefa financeira</span>
                <p class="text-xs text-gray-500 mt-0.5">Cria uma tarefa "Pagar: {{ form.name || '...' }}" para o departamento selecionado</p>
              </div>
            </label>

            <div v-if="form.generate_task" class="mt-3">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Departamento Responsável</label>
              <DepartmentsSelectInput
                name="task_department_id"
                v-model="form.task_department_id"
                :fieldNull="'Selecione o departamento'"
              />
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="errorMessage" class="p-3 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-700 text-sm">{{ errorMessage }}</p>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3 pt-2 border-t border-gray-200">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm disabled:opacity-50 flex items-center gap-2"
            >
              <font-awesome-icon v-if="isSubmitting" icon="fa-solid fa-spinner" class="animate-spin" />
              {{ isSubmitting ? 'Salvando...' : 'Salvar' }}
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
import CompaniesSelectInput from "./selects/CompaniesSelectInput.vue";
import DepartmentsSelectInput from "./selects/DepartmentsSelectInput.vue";
import { BACKEND_URL } from "@/config/apiConfig";
import axios from "axios";

export default {
  name: "StandaloneDebitInvoiceCreateForm",
  emits: ["invoice-created", "update:modelValue"],
  components: {
    LeadsSelectInput,
    CompaniesSelectInput,
    DepartmentsSelectInput,
  },
  props: {
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
      supplierType: "none",
      financeiroDepartmentId: null,
      form: {
        name: "",
        category: "fixed_cost",
        payment_method: "pix",
        department_id: null,
        lead_id: null,
        company_id: null,
        prices: [],
        date_due: this.getTodayDate(),
        observations: "",
        type: "debit",
        generate_task: true,
        task_department_id: null,
      },
    };
  },
  computed: {
    totalPrices() {
      return this.form.prices.reduce((acc, price) => acc + (isNaN(price) ? 0 : price), 0).toFixed(2);
    },
  },
  watch: {
    modelValue(newVal) {
      if (newVal) {
        this.resetForm();
        this.loadFinanceiroDepartment();
      }
    },
    supplierType(newType) {
      if (newType !== "lead") this.form.lead_id = null;
      if (newType !== "company") this.form.company_id = null;
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
    handlePaymentMethodChange() {
      // Cartão de crédito tipicamente não precisa de ação manual
      this.form.generate_task = this.form.payment_method !== "credit_card";
    },
    initializePrices() {
      if (this.totalAmount <= 0 || this.installmentQuantity <= 0) {
        this.form.prices = [];
        return;
      }
      const pricePerInstallment = parseFloat((this.totalAmount / this.installmentQuantity).toFixed(2));
      const prices = Array(this.installmentQuantity).fill(pricePerInstallment);
      const totalCalculated = parseFloat((pricePerInstallment * this.installmentQuantity).toFixed(2));
      const difference = parseFloat((this.totalAmount - totalCalculated).toFixed(2));
      if (difference !== 0) {
        prices[prices.length - 1] = parseFloat((prices[prices.length - 1] + difference).toFixed(2));
      }
      this.form.prices = prices;
    },
    updatePrices() {
      if (this.installmentQuantity > 1) {
        this.initializePrices();
      }
    },
    adjustPrices(changedIndex) {
      if (changedIndex >= this.form.prices.length - 1) return;
      const sumBefore = this.form.prices.slice(0, changedIndex + 1).reduce((a, b) => a + (parseFloat(b) || 0), 0);
      const remaining = parseFloat((this.totalAmount - sumBefore).toFixed(2));
      const remainingCount = this.form.prices.length - changedIndex - 1;
      const perInstallment = parseFloat((remaining / remainingCount).toFixed(2));
      for (let i = changedIndex + 1; i < this.form.prices.length; i++) {
        this.form.prices[i] = perInstallment;
      }
      const diff = parseFloat((this.totalAmount - this.form.prices.reduce((a, b) => a + b, 0)).toFixed(2));
      if (diff !== 0) {
        this.form.prices[this.form.prices.length - 1] = parseFloat((this.form.prices[this.form.prices.length - 1] + diff).toFixed(2));
      }
    },
    async loadFinanceiroDepartment() {
      try {
        const response = await axios.get(`${BACKEND_URL}departments`);
        const departments = response.data?.data || response.data || [];
        const financeiro = departments.find(d => d.slug === 'financeiro' || d.name?.toLowerCase().includes('financeiro'));
        if (financeiro) {
          this.financeiroDepartmentId = financeiro.id;
          this.form.task_department_id = financeiro.id;
        }
      } catch (e) {
        // silently fail - user can select manually
      }
    },
    resetForm() {
      this.totalAmount = 0;
      this.installmentQuantity = 1;
      this.supplierType = "none";
      this.errorMessage = "";
      this.form = {
        name: "",
        category: "fixed_cost",
        payment_method: "pix",
        department_id: null,
        lead_id: null,
        company_id: null,
        prices: [],
        date_due: this.getTodayDate(),
        observations: "",
        type: "debit",
        generate_task: true,
        task_department_id: this.financeiroDepartmentId,
      };
    },
    closeModal() {
      this.$emit("update:modelValue", false);
    },
    async submitForm() {
      if (!this.form.name?.trim()) {
        this.errorMessage = "O nome da conta é obrigatório.";
        return;
      }
      if (!this.totalAmount || this.totalAmount <= 0) {
        this.errorMessage = "Informe um valor maior que zero.";
        return;
      }

      this.isSubmitting = true;
      this.errorMessage = "";

      const isInstallment = this.installmentQuantity > 1;
      const payload = {
        name: this.form.name.trim(),
        category: this.form.category,
        department_id: this.form.department_id || null,
        lead_id: this.form.lead_id || null,
        company_id: this.form.company_id || null,
        date_due: this.form.date_due,
        observations: this.form.observations || null,
        type: "debit",
        generate_task: this.form.generate_task ? 1 : 0,
        task_department_id: this.form.generate_task ? this.form.task_department_id : null,
        ...(isInstallment
          ? { prices: this.form.prices }
          : { price: this.totalAmount }),
      };

      try {
        const endpoint = isInstallment ? "invoices" : "invoices/debit";
        const { data, error } = await this.submitFormCreate(endpoint, payload);
        if (error) {
          this.errorMessage = error?.message || "Erro ao criar conta a pagar.";
          return;
        }
        this.$emit("invoice-created", data);
        this.closeModal();
      } catch (e) {
        this.errorMessage = "Erro ao criar conta a pagar.";
      } finally {
        this.isSubmitting = false;
      }
    },
  },
};
</script>
