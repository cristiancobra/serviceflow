<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-coins" class="page-icon" />
        <h1>MOVIMENTAÇÕES</h1>
      </div>
      <div class="page-action">
        <!-- Espaço para futuro botão de criar transação -->
      </div>
    </div>

    <section class="px-8 mt-4 mb-20">
      <!-- Filtros -->
      <div class="mb-4 rounded-lg bg-gradient-to-br from-slate-100 to-slate-200 px-6 py-3 shadow-sm border border-white/50">
        <div class="flex flex-wrap gap-6 items-end">
          <!-- Busca -->
          <div class="flex-1 min-w-64 relative">
            <font-awesome-icon icon="fa-solid fa-search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none" />
            <input
              type="text"
              v-model="searchTerm"
              placeholder="Buscar por cliente, fatura, conta..."
              class="w-full pl-10 pr-4 py-2 border-2 border-gray-300 rounded-lg bg-white text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
            />
          </div>
          
          <!-- Filtro de Conta -->
          <div class="flex flex-col gap-1 min-w-80">
            <label for="bank-account-filter" class="text-sm font-semibold text-gray-900 flex items-center gap-2">
              <font-awesome-icon icon="fa-solid fa-building-columns" class="text-blue-500 text-sm" />
              Conta Bancária:
            </label>
            <select
              id="bank-account-filter"
              v-model="selectedBankAccount"
              @change="filterByBankAccount"
              class="px-4 py-2 border-2 border-gray-300 rounded-lg bg-white text-sm font-medium text-gray-700 cursor-pointer hover:border-blue-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
            >
              <option value="">Todas as contas</option>
              <option
                v-for="account in bankAccounts"
                :key="account.id"
                :value="account.id"
              >
                {{ account.name || account.bank_name }} - {{ account.account_number }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Transações agrupadas por mês -->
      <div v-for="monthGroup in groupedTransactions" :key="monthGroup.monthKey" class="mb-4">
        <!-- Header do Mês -->
        <div class="flex items-center mb-1 sticky top-0 z-10">
          <div class="flex items-center gap-3 bg-white pe-6 pb-1 pt-2">
            <span class="font-bold text-primary text-lg whitespace-nowrap">{{ monthGroup.monthLabel }}</span>
          </div>
          
        </div>

        <!-- Tabela do Mês -->
        <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
          <div class="overflow-x-auto">
            <table class="w-full table-fixed">
              <thead class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                <tr>
                  <th class="w-[8%] px-3 py-2 text-center text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-calendar" class="mr-1" />
                    Data
                  </th>
                  <th class="w-[20%] px-3 py-2 text-left text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-user" class="mr-1" />
                    Cliente
                  </th>
                  <th class="w-[16%] px-3 py-2 text-left text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-bullseye" class="mr-1" />
                    Oportunidade
                  </th>
                  <th class="w-[6%] px-3 py-2 text-center text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-file-contract" class="mr-1" />
                    Proposta
                  </th>
                  <th class="w-[10%] px-3 py-2 text-left text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-receipt" class="mr-1" />
                    Fatura
                  </th>
                  <th class="w-[8%] px-3 py-2 text-center text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-calendar-check" class="mr-1" />
                    Vencimento
                  </th>
                  <th class="w-[14%] px-3 py-2 text-left text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-wallet" class="mr-1" />
                    Conta
                  </th>
                  <th class="w-[9%] px-3 py-2 text-right text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-money-bill-wave" class="mr-1" />
                    Valor
                  </th>
                  <th class="w-[5%] px-3 py-2 text-center text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-flag" class="mr-1" />
                    Status
                  </th>
                  <th class="w-[4%] px-3 py-2 text-center text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-exchange-alt" class="mr-1" />
                    Tipo
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <template v-for="row in monthGroup.rows" :key="row.key">
                <!-- Pagamento em lote: uma linha só, como no extrato -->
                <tr
                  v-if="row.batch"
                  class="hover:bg-gray-50 transition-colors cursor-pointer"
                  @click="toggleBatch(row.batch.id)"
                >
                  <td class="w-[8%] px-3 py-1 text-center">
                    <span class="inline-block bg-primary-content text-black px-3 py-0.5 rounded-md text-xs font-semibold">
                      {{ formatDateBr(row.batch.transaction_date) }}
                    </span>
                  </td>
                  <td colspan="3" class="px-3 py-1 text-left max-w-0">
                    <div class="flex items-center gap-2 min-w-0">
                      <font-awesome-icon icon="fa-solid fa-layer-group" class="text-gray-400 text-sm flex-shrink-0" />
                      <span class="text-sm font-semibold text-gray-900 truncate" :title="row.batch.description || 'Pagamento em lote'">
                        {{ row.batch.description || 'Pagamento em lote' }}
                      </span>
                    </div>
                  </td>
                  <td colspan="2" class="px-3 py-1 text-left">
                    <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold text-sm">
                      <font-awesome-icon
                        :icon="expandedBatches.includes(row.batch.id) ? 'fa-solid fa-chevron-down' : 'fa-solid fa-chevron-right'"
                        class="text-xs"
                      />
                      {{ row.batch.children.length }} {{ row.batch.children.length === 1 ? 'fatura' : 'faturas' }}
                    </span>
                  </td>
                  <td class="w-[14%] px-3 py-1 text-left max-w-0">
                    <div class="flex items-center gap-2 min-w-0">
                      <font-awesome-icon icon="fa-solid fa-university" class="text-gray-400 text-sm flex-shrink-0" />
                      <span class="text-sm font-medium text-gray-900 truncate">
                        {{ row.batch.bank_account?.name || row.batch.bank_account?.bank_name || '-' }}
                      </span>
                    </div>
                  </td>
                  <td class="w-[9%] px-3 py-1 text-right">
                    <span class="text-sm font-bold" :class="row.batch.type === 'credit' ? 'text-green-600' : 'text-red-600'">
                      {{ formatCurrency(row.batch.amount) }}
                    </span>
                  </td>
                  <td class="w-[5%] px-3 py-1 text-center">
                    <button
                      type="button"
                      class="text-gray-400 hover:text-red-600 transition-colors"
                      title="Estornar o pagamento em lote inteiro"
                      @click.stop="destroyBatch(row.batch)"
                    >
                      <font-awesome-icon icon="fa-solid fa-rotate-left" />
                    </button>
                  </td>
                  <td class="w-[4%] px-3 py-1 text-center">
                    <span
                      :class="row.batch.type === 'credit' ? 'bg-green-100' : 'bg-red-100'"
                      class="w-6 h-6 flex items-center justify-center rounded-full mx-auto"
                    >
                      <font-awesome-icon
                        :icon="row.batch.type === 'credit' ? 'fa-solid fa-arrow-up' : 'fa-solid fa-arrow-down'"
                        :class="row.batch.type === 'credit' ? 'text-green-600' : 'text-red-600'"
                        class="text-xs"
                      />
                    </span>
                  </td>
                </tr>
                <tr
                  v-else-if="!row.batchId || expandedBatches.includes(row.batchId)"
                  class="hover:bg-gray-50 transition-colors"
                  :class="{ 'bg-gray-50/70': row.batchId }"
                >
                  <!-- Data -->
                  <td class="w-[8%] px-3 py-1 text-center">
                    <font-awesome-icon v-if="row.batchId" icon="fa-solid fa-turn-up" class="rotate-90 text-gray-300 text-xs mr-1" />
                    <span v-else class="inline-block bg-primary-content text-black px-3 py-0.5 rounded-md text-xs font-semibold">
                      {{ formatDateBr(row.transaction.transaction_date) }}
                    </span>
                  </td>
                  
                  <!-- Cliente -->
                  <td class="w-[20%] px-3 py-1 text-left max-w-0">
                    <div class="flex items-center gap-2 min-w-0">
                      <font-awesome-icon icon="fa-solid fa-building" class="text-gray-400 text-sm flex-shrink-0" />
                      <span class="text-sm font-medium text-gray-900 truncate" :title="getClientName(row.transaction.invoice?.proposal?.opportunity, row.transaction.invoice) || '-'">
                        {{ getClientName(row.transaction.invoice?.proposal?.opportunity, row.transaction.invoice) || '-' }}
                      </span>
                    </div>
                  </td>
                  
                  <!-- Oportunidade -->
                  <td class="w-[16%] px-3 py-1 text-left max-w-0">
                    <router-link
                      v-if="row.transaction.invoice?.proposal?.opportunity?.name"
                      :to="{ name: 'opportunityShow', params: { id: row.transaction.invoice.proposal.opportunity.id } }"
                      class="flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors min-w-0"
                      :title="row.transaction.invoice.proposal.opportunity.name.trim()"
                    >
                      <font-awesome-icon icon="fa-solid fa-bullseye" class="text-xs flex-shrink-0" />
                      <span class="truncate block">{{ row.transaction.invoice.proposal.opportunity.name.trim() }}</span>
                    </router-link>
                    <span v-else class="text-gray-400 text-sm italic">-</span>
                  </td>
                  
                  <!-- Proposta -->
                  <td class="w-[6%] px-3 py-1 text-center">
                    <router-link
                      v-if="row.transaction.invoice?.proposal"
                      :to="{ name: 'proposalShow', params: { id: row.transaction.invoice.proposal.id } }"
                      class="inline-flex items-center justify-center text-indigo-600 hover:text-indigo-800 transition-all hover:scale-110"
                      :title="'Proposta ' + row.transaction.invoice.proposal.id"
                    >
                      <font-awesome-icon icon="fa-solid fa-magnifying-glass" />
                    </router-link>
                    <span v-else class="text-gray-400">-</span>
                  </td>
                  
                  <!-- Fatura -->
                  <td class="w-[10%] px-3 py-1 text-left">
                    <router-link
                      v-if="row.transaction.invoice"
                      :to="{ name: 'invoiceShow', params: { id: row.transaction.invoice.id } }"
                      class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-800 font-semibold text-sm transition-colors"
                      :title="'Fatura #' + row.transaction.invoice.id"
                    >
                      <font-awesome-icon icon="fa-solid fa-receipt" class="text-sm" />
                      {{ getInvoiceLabel(row.transaction.invoice) }}
                    </router-link>
                    <div v-else class="inline-flex items-center gap-1 text-amber-500 font-medium text-xs">
                      <font-awesome-icon icon="fa-solid fa-circle-dot" class="text-xs" />
                      Avulsa
                    </div>
                  </td>
                  
                  <!-- Data Vencimento -->
                  <td class="w-[8%] px-3 py-1 text-center">
                    <span v-if="row.transaction.invoice?.date_due" class="text-sm font-medium text-gray-900">
                      {{ formatDateBr(row.transaction.invoice.date_due) }}
                    </span>
                    <span v-else class="text-gray-400 text-sm italic">-</span>
                  </td>
                  
                  <!-- Conta -->
                  <td class="w-[14%] px-3 py-1 text-left max-w-0">
                    <div class="flex items-center gap-2 min-w-0">
                      <font-awesome-icon icon="fa-solid fa-university" class="text-gray-400 text-sm flex-shrink-0" />
                      <span class="text-sm font-medium text-gray-900 truncate" :title="row.transaction.bank_account?.name || row.transaction.bank_account?.bank_name || '-'">
                        {{ row.transaction.bank_account?.name || row.transaction.bank_account?.bank_name || '-' }}
                      </span>
                    </div>
                  </td>
                  
                  <!-- Valor -->
                  <td class="w-[9%] px-3 py-1 text-right">
                    <span class="text-sm font-bold" :class="row.transaction.type === 'credit' ? 'text-green-600' : 'text-red-600'">
                      <money-field name="amount" v-model="row.transaction.amount" :readonly="true" />
                    </span>
                  </td>
                  
                  <!-- Status -->
                  <td class="w-[5%] px-3 py-1 text-center">
                    <span 
                      class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider border transition-shadow"
                      :class="getStatusClass(row.transaction.status)"
                    >
                      <font-awesome-icon :icon="getStatusIcon(row.transaction.status)" class="text-xs" />
                      {{ getStatusLabel(row.transaction.status) }}
                    </span>
                  </td>
                  
                  <!-- Tipo -->
                  <td class="w-[4%] px-3 py-1 text-center">
                    <span
                      :class="{
                        'bg-green-100': row.transaction.type === 'credit',
                        'bg-red-100': row.transaction.type === 'debit',
                      }"
                      class="w-6 h-6 flex items-center justify-center rounded-full mx-auto"
                    >
                      <font-awesome-icon
                        :icon="row.transaction.type === 'credit' ? 'fa-solid fa-arrow-up' : 'fa-solid fa-arrow-down'"
                        :class="{
                          'text-green-600': row.transaction.type === 'credit',
                          'text-red-600': row.transaction.type === 'debit',
                        }"
                        class="text-xs"
                      />
                    </span>
                  </td>
                </tr>
                </template>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Estado Vazio -->
      <div v-if="filteredTransactions && filteredTransactions.length === 0" class="text-center py-16 bg-gradient-to-b from-gray-50 to-gray-100 rounded-lg border-2 border-dashed border-gray-300">
        <font-awesome-icon icon="fa-solid fa-inbox" class="text-5xl text-gray-300 mb-4 block" />
        <p class="text-xl font-bold text-gray-900 mb-2">Nenhuma movimentação encontrada</p>
        <p class="text-sm text-gray-600">Tente ajustar seus filtros de busca</p>
      </div>
    </section>
  </div>
</template>

<script>
import { index, destroy } from "@/utils/requests/httpUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import MoneyField from "../fields/number/MoneyField.vue";

export default {
  components: {
    MoneyField,
  },
  props: {
    filterType: {
      type: String,
      required: false,
      default: null,
    },
  },
  data() {
    return {
      searchTerm: "",
      selectedBankAccount: "",
      transactions: [],
      filteredTransactions: [],
      // Lotes de pagamento abertos para mostrar as faturas que quitaram
      expandedBatches: [],
      bankAccounts: [],
    };
  },
  computed: {
    searchFilteredTransactions() {
      let filtered = this.transactions;

      // Filtro por tipo (débito/crédito)
      if (this.filterType) {
        filtered = filtered.filter(transaction => transaction.type === this.filterType);
      }

      // Filtro por busca
      if (!this.searchTerm) {
        return filtered;
      }
      
      const term = this.searchTerm.toLowerCase();
      return filtered.filter(transaction => {
        if (transaction.amount && transaction.amount.toString().includes(term)) {
          return true;
        }
        
        const clientName = this.getClientName(
          transaction.invoice?.proposal?.opportunity,
          transaction.invoice
        ).toLowerCase();
        if (clientName.includes(term)) {
          return true;
        }
        
        if (transaction.bank_account) {
          const accountName = (transaction.bank_account.name || transaction.bank_account.bank_name || '').toLowerCase();
          if (accountName.includes(term)) {
            return true;
          }
        }
        
        return false;
      });
    },
    
    groupedTransactions() {
      const groups = {};
      
      this.filteredTransactions.forEach(transaction => {
        const date = new Date(transaction.transaction_date);
        const monthKey = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        const monthLabel = new Intl.DateTimeFormat('pt-BR', { month: 'long', year: 'numeric' }).format(date);
        
        if (!groups[monthKey]) {
          groups[monthKey] = {
            monthKey,
            monthLabel: monthLabel.charAt(0).toUpperCase() + monthLabel.slice(1),
            rows: [],
            batches: {},
          };
        }

        const group = groups[monthKey];
        const batchId = transaction.payment_batch_id;

        // Transactions do mesmo lote viram uma linha só (o total que saiu do
        // banco), seguida das faturas que ela quitou, mostradas ao expandir.
        if (batchId) {
          if (!group.batches[batchId]) {
            group.batches[batchId] = {
              id: batchId,
              transaction_date: transaction.transaction_date,
              type: transaction.type,
              bank_account: transaction.bank_account,
              amount: transaction.payment_batch?.amount,
              description: transaction.payment_batch?.description,
              children: [],
            };
            group.rows.push({ key: `batch-${batchId}`, batch: group.batches[batchId] });
          }
          group.batches[batchId].children.push(transaction);
          const lastChildIndex = group.rows.map(r => r.batchId).lastIndexOf(batchId);
          const insertAt = lastChildIndex === -1
            ? group.rows.findIndex(r => r.key === `batch-${batchId}`) + 1
            : lastChildIndex + 1;
          group.rows.splice(insertAt, 0, { key: `tx-${transaction.id}`, batchId, transaction });
          return;
        }

        group.rows.push({ key: `tx-${transaction.id}`, transaction });
      });
      
      return Object.values(groups).sort((a, b) => {
        return new Date(b.monthKey) - new Date(a.monthKey);
      });
    }
  },
  watch: {
    searchTerm() {
      this.applyFilters();
    },
    searchFilteredTransactions() {
      this.applyFilters();
    },
    filterType(newType, oldType) {
      // Recarrega as transações quando o filtro de tipo mudar
      if (newType !== oldType) {
        this.applyFilters();
      }
    }
  },
  methods: {
    formatDateBr,

    formatCurrency(value) {
      return new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(value || 0);
    },

    toggleBatch(batchId) {
      const index = this.expandedBatches.indexOf(batchId);
      if (index === -1) {
        this.expandedBatches.push(batchId);
      } else {
        this.expandedBatches.splice(index, 1);
      }
    },

    async destroyBatch(batch) {
      const message = `Estornar o pagamento em lote de ${this.formatCurrency(batch.amount)}? `
        + `As ${batch.children.length} faturas voltarão a ficar em aberto.`;
      if (!confirm(message)) return;

      try {
        await destroy("payment_batches", batch.id);
        await this.getTransactions();
      } catch (error) {
        console.error("Erro ao estornar pagamento em lote:", error);
      }
    },
    
    getInvoiceLabel(invoice) {
      if (!invoice.installment_number) return 'Fatura #' + invoice.id;
      if (invoice.installment_quantity === 1) return 'Parcela única';
      return 'Fatura  ' + invoice.installment_number + ' de ' + invoice.installment_quantity;
    },

    getClientName(opportunity, invoice) {
      if (opportunity) {
        if (opportunity.company?.business_name) return opportunity.company.business_name;
        if (opportunity.company?.legal_name) return opportunity.company.legal_name;
        if (opportunity.lead?.name) return opportunity.lead.name;
      }
      if (invoice) {
        if (invoice.company?.business_name) return invoice.company.business_name;
        if (invoice.company?.legal_name) return invoice.company.legal_name;
        if (invoice.lead?.name) return invoice.lead.name;
      }
      return 'Cliente não identificado';
    },
    
    getStatusClass(status) {
      switch (status) {
        case 'confirmed':
        case 'received':
          return 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-900 border-green-300';
        case 'pending':
          return 'bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-900 border-yellow-300';
        case 'cancelled':
          return 'bg-gradient-to-r from-red-100 to-rose-100 text-red-900 border-red-300';
        default:
          return 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 border-gray-300';
      }
    },
    
    getStatusLabel(status) {
      const labels = {
        'confirmed': 'Confirmado',
        'received': 'Recebido',
        'pending': 'Pendente',
        'cancelled': 'Cancelado'
      };
      return labels[status] || status;
    },
    
    getStatusIcon(status) {
      switch (status) {
        case 'confirmed':
        case 'received':
          return 'fa-solid fa-check-circle';
        case 'pending':
          return 'fa-solid fa-clock';
        case 'cancelled':
          return 'fa-solid fa-times-circle';
        default:
          return 'fa-solid fa-circle';
      }
    },
    
    filterByBankAccount() {
      this.applyFilters();
    },
    
    applyFilters() {
      let filtered = this.searchFilteredTransactions;
      
      if (this.selectedBankAccount) {
        filtered = filtered.filter(transaction => 
          transaction.bank_account_id == this.selectedBankAccount
        );
      }
      
      this.filteredTransactions = filtered;
    },
    
    extractBankAccounts() {
      const accounts = new Map();
      
      this.transactions.forEach(transaction => {
        if (transaction.bank_account) {
          accounts.set(transaction.bank_account.id, transaction.bank_account);
        }
      });
      
      this.bankAccounts = Array.from(accounts.values());
    },
    
    async getTransactions() {
      try {
        this.transactions = await index("transactions");
        
        this.transactions.sort((a, b) => {
          return new Date(b.transaction_date) - new Date(a.transaction_date);
        });
        
        this.extractBankAccounts();
        this.applyFilters();
      } catch (error) {
        console.error("Erro ao carregar transações:", error);
      }
    },
  },
  mounted() {
    this.getTransactions();
  },
};
</script>