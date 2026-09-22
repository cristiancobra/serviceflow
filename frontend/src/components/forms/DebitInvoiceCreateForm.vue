<template>
  <ModalCard
    title="Fatura de Débito"
    icon="fa-solid fa-file-invoice"
    :compact="compact"
    @close="closeModal"
  >
        <!-- Form -->
        <form id="debitInvoiceCreateForm" @submit.prevent="submitForm" class="space-y-5">
          <!-- Tipo de Fornecedor -->
          <div>
            <label class="block text-sm font-semibold text-base-content mb-2">
              Tipo de Fornecedor
            </label>
            <div class="flex gap-4">
              <label class="flex items-center">
                <input
                  type="radio"
                  v-model="supplierType"
                  value="lead"
                  class="mr-2"
                />
                <span class="text-base-content">Pessoa</span>
              </label>
              <label class="flex items-center">
                <input
                  type="radio"
                  v-model="supplierType"
                  value="company"
                  class="mr-2"
                />
                <span class="text-base-content">Empresa</span>
              </label>
            </div>
          </div>

          <!-- Fornecedor Lead (Pessoa) -->
          <div v-if="supplierType === 'lead'">
            <leads-select-input 
              ref="leadsSelect"
              label="Fornecedor (Pessoa)" 
              v-model="form.lead_id" 
              fieldsToDisplay="name" 
              fieldNull="Selecione um fornecedor"
            />
            <button
              type="button"
              class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-semibold"
              @click="toggleLead()"
            >
              + Adicionar novo contato
            </button>
          </div>

          <!-- Fornecedor Company (Empresa) -->
          <div v-if="supplierType === 'company'">
            <companies-select-input 
              ref="companiesSelect"
              label="Empresa Fornecedora" 
              v-model="form.company_id" 
              :fieldsToDisplay="['business_name', 'legal_name']" 
              fieldNull="Selecione uma empresa"
            />
            <button
              type="button"
              class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-semibold"
              @click="toggleCompany()"
            >
              + Adicionar nova empresa
            </button>
          </div>

          <!-- Responsável na Empresa -->
          <div v-if="supplierType === 'company'">
            <leads-select-input 
              ref="leadsSelectResponsible"
              label="Responsável na Empresa (opcional)" 
              v-model="form.lead_id" 
              fieldsToDisplay="name" 
              fieldNull="Selecione um responsável"
            />
            <button
              type="button"
              class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-semibold"
              @click="toggleLead()"
            >
              + Adicionar novo contato
            </button>
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
                :model-value="totalAmount"
                @update:model-value="(value) => { totalAmount = value; updateInstallments(); }"
                class="flex-1 px-3 py-2 border border-base-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-colors"
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
          <div v-if="installmentQuantity > 1" class="border-t border-base-300 pt-4">
            <div class="flex items-center space-x-2 mb-4">
              <div class="w-2 h-6 bg-red-500 rounded-full"></div>
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
                  <span class="inline-flex items-center justify-center w-7 h-7 bg-red-100 text-red-800 text-sm font-bold rounded-full mr-3">
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

            <div class="mt-4 p-4 bg-red-50 rounded-lg border-2 border-red-200">
              <div class="flex flex-col sm:flex-row gap-3 items-center">
                <div class="flex-1">
                  <label class="text-sm font-semibold text-red-700">Total das Parcelas</label>
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
              Data de Vencimento {{ installmentQuantity > 1 ? 'da 1ª Parcela' : '' }}
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
              placeholder="Adicione notas sobre esta fatura..."
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
        form="debitInvoiceCreateForm"
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
import { mapMutations } from "vuex";
import { submitFormCreate } from "@/utils/requests/httpUtils";
import LeadsSelectInput from "./selects/LeadsSelectInput.vue";
import CompaniesSelectInput from "./selects/CompaniesSelectInput.vue";
import MoneyInput from "./inputs/money/MoneyInput.vue";
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
  name: "DebitInvoiceCreateForm",
  emits: ["invoice-created", "close"],
  components: {
    LeadsSelectInput,
    CompaniesSelectInput,
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
      totalAmount: 0,
      installmentQuantity: 1,
      supplierType: "lead", // 'lead' ou 'company'
      form: {
        lead_id: null,
        company_id: null,
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
    supplierType(newType) {
      // Limpar o fornecedor quando mudar o tipo
      if (newType === 'lead') {
        this.form.company_id = null;
      } else {
        this.form.lead_id = null;
      }
    },
  },
  methods: {
    ...mapMutations(["openModal"]),
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
    closeModal() {
      this.$emit("close");
    },
    async submitForm() {
      // Validações
      if (this.supplierType === 'lead') {
        // Se for pessoa, precisa apenas de lead_id
        if (!this.form.lead_id) {
          this.errorMessage = "Selecione um fornecedor (pessoa).";
          return;
        }
      } else if (this.supplierType === 'company') {
        // Se for empresa, precisa apenas de company_id (responsável é opcional)
        if (!this.form.company_id) {
          this.errorMessage = "Selecione uma empresa fornecedora.";
          return;
        }
        // lead_id (responsável) é opcional para empresas
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
        lead_id: this.form.lead_id || null,
        company_id: this.form.company_id || null,
        prices: this.form.prices,
        date_due: this.form.date_due,
        observations: this.form.observations || null,
        proposal_id: this.proposal.id,
        type: "debit",
      } : {
        lead_id: this.form.lead_id || null,
        company_id: this.form.company_id || null,
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
    toggleCompany() {
      this.openModal({
        component: "CompanyCreateForm",
        listeners: {
          "new-company-event": this.addCompanyCreated,
        },
      });
    },
    toggleLead() {
      this.openModal({
        component: "LeadCreateForm",
        listeners: {
          "new-lead-event": this.addLeadCreated,
        },
      });
    },
    addCompanyCreated(newCompany) {
      this.form.company_id = newCompany.id;
      // Recarregar a lista de empresas
      this.$refs.companiesSelect?.reload();
    },
    addLeadCreated(newLead) {
      this.form.lead_id = newLead.id;
      // Recarregar a lista de leads apropriada
      if (this.supplierType === 'lead') {
        this.$refs.leadsSelect?.reload();
      } else {
        this.$refs.leadsSelectResponsible?.reload();
      }
    },
  },
};
</script>

<style scoped>
/* Estilos específicos se necessário */
</style>
