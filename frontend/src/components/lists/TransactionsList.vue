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

    <section class="section-container">
      <!-- Filtros -->
      <div class="mb-6 rounded-lg bg-gradient-to-br from-slate-100 to-slate-200 p-6 shadow-sm border border-white/50">
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
          <div class="flex flex-col gap-2 min-w-80">
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
      <div v-for="monthGroup in groupedTransactions" :key="monthGroup.monthKey" class="mb-8">
        <!-- Header do Mês -->
        <div class="flex items-center gap-4 mb-4 sticky top-0 z-10">
          <div class="flex items-center gap-3 bg-white px-6 py-3 rounded-lg shadow-sm border border-blue-100">
            <font-awesome-icon icon="fa-solid fa-calendar-alt" class="text-primary text-lg" />
            <span class="font-bold text-gray-900 text-lg whitespace-nowrap">{{ monthGroup.monthLabel }}</span>
            <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
              {{ monthGroup.transactions.length }} {{ monthGroup.transactions.length === 1 ? 'movimentação' : 'movimentações' }}
            </span>
          </div>
          
        </div>

        <!-- Tabela do Mês -->
        <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200 mb-6">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                <tr>
                  <th class="w-1/12 px-3 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-calendar" class="mr-1" />
                    Data
                  </th>
                  <th class="w-3/12 px-3 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-user" class="mr-1" />
                    Cliente
                  </th>
                  <th class="w-2/12 px-3 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-bullseye" class="mr-1" />
                    Oportunidade
                  </th>
                  <th class="w-1/12 px-3 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-file-contract" class="mr-1" />
                    Proposta
                  </th>
                  <th class="w-2/12 px-3 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-receipt" class="mr-1" />
                    Fatura
                  </th>
                  <th class="w-1/12 px-3 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-calendar-check" class="mr-1" />
                    Vencimento
                  </th>
                  <th class="w-2/12 px-3 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-wallet" class="mr-1" />
                    Conta
                  </th>
                  <th class="w-1/12 px-3 py-4 text-right text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-money-bill-wave" class="mr-1" />
                    Valor
                  </th>
                  <th class="w-1/12 px-3 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-flag" class="mr-1" />
                    Status
                  </th>
                  <th class="w-1/12 px-3 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-900">
                    <font-awesome-icon icon="fa-solid fa-exchange-alt" class="mr-1" />
                    Tipo
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr
                  v-for="transaction in monthGroup.transactions"
                  :key="transaction.id"
                  class="hover:bg-gray-50 transition-colors"
                >
                  <!-- Data -->
                  <td class="w-1/12 px-3 py-3 text-center">
                    <span class="inline-block bg-primary-content text-black px-3 py-1 rounded-md text-xs font-semibold">
                      {{ formatDateBr(transaction.transaction_date) }}
                    </span>
                  </td>
                  
                  <!-- Cliente -->
                  <td class="w-3/12 px-3 py-3 text-left">
                    <div class="flex items-center gap-2">
                      <font-awesome-icon icon="fa-solid fa-building" class="text-gray-400 text-sm flex-shrink-0" />
                      <span class="text-sm font-medium text-gray-900 truncate" :title="getClientName(transaction.invoice?.proposal?.opportunity) || '-'">
                        {{ getClientName(transaction.invoice?.proposal?.opportunity) || '-' }}
                      </span>
                    </div>
                  </td>
                  
                  <!-- Oportunidade -->
                  <td class="w-2/12 px-3 py-3 text-left">
                    <router-link
                      v-if="transaction.invoice?.proposal?.opportunity?.name"
                      :to="{ name: 'opportunityShow', params: { id: transaction.invoice.proposal.opportunity.id } }"
                      class="flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium truncate text-sm transition-colors"
                      :title="transaction.invoice.proposal.opportunity.name"
                    >
                      <font-awesome-icon icon="fa-solid fa-bullseye" class="text-xs flex-shrink-0" />
                      {{ transaction.invoice.proposal.opportunity.name }}
                    </router-link>
                    <span v-else class="text-gray-400 text-sm italic">-</span>
                  </td>
                  
                  <!-- Proposta -->
                  <td class="w-1/12 px-3 py-3 text-center">
                    <router-link
                      v-if="transaction.invoice?.proposal"
                      :to="{ name: 'proposalShow', params: { id: transaction.invoice.proposal.id } }"
                      class="inline-flex items-center justify-center text-indigo-600 hover:text-indigo-800 transition-all hover:scale-110"
                      :title="'Proposta ' + transaction.invoice.proposal.id"
                    >
                      <font-awesome-icon icon="fa-solid fa-magnifying-glass" />
                    </router-link>
                    <span v-else class="text-gray-400">-</span>
                  </td>
                  
                  <!-- Fatura -->
                  <td class="w-2/12 px-3 py-3 text-left">
                    <router-link
                      v-if="transaction.invoice"
                      :to="{ name: 'invoiceShow', params: { id: transaction.invoice.id } }"
                      class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-800 font-semibold text-sm transition-colors"
                      :title="'Fatura #' + transaction.invoice.id"
                    >
                      <font-awesome-icon icon="fa-solid fa-receipt" class="text-sm" />
                      #{{ transaction.invoice.id }}
                    </router-link>
                    <div v-else class="inline-flex items-center gap-1 text-amber-500 font-medium text-xs">
                      <font-awesome-icon icon="fa-solid fa-circle-dot" class="text-xs" />
                      Avulsa
                    </div>
                  </td>
                  
                  <!-- Data Vencimento -->
                  <td class="w-1/12 px-3 py-3 text-center">
                    <span v-if="transaction.invoice?.date_due" class="text-sm font-medium text-gray-900">
                      {{ formatDateBr(transaction.invoice.date_due) }}
                    </span>
                    <span v-else class="text-gray-400 text-sm italic">-</span>
                  </td>
                  
                  <!-- Conta -->
                  <td class="w-2/12 px-3 py-3 text-left">
                    <div class="flex items-center gap-2">
                      <font-awesome-icon icon="fa-solid fa-university" class="text-gray-400 text-sm flex-shrink-0" />
                      <span class="text-sm font-medium text-gray-900 truncate" :title="transaction.bank_account?.name || transaction.bank_account?.bank_name || '-'">
                        {{ transaction.bank_account?.name || transaction.bank_account?.bank_name || '-' }}
                      </span>
                    </div>
                  </td>
                  
                  <!-- Valor -->
                  <td class="w-1/12 px-3 py-3 text-right">
                    <span class="text-sm font-bold text-emerald-600">
                      <money-field name="amount" v-model="transaction.amount" :readonly="true" />
                    </span>
                  </td>
                  
                  <!-- Status -->
                  <td class="w-1/12 px-3 py-3 text-center">
                    <span 
                      class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border transition-shadow"
                      :class="getStatusClass(transaction.status)"
                    >
                      <font-awesome-icon :icon="getStatusIcon(transaction.status)" class="text-xs" />
                      {{ getStatusLabel(transaction.status) }}
                    </span>
                  </td>
                  
                  <!-- Tipo -->
                  <td class="w-1/12 px-3 py-3 text-center">
                    <span
                      :class="{
                        'bg-blue-100': transaction.type === 'credit',
                        'bg-red-100': transaction.type === 'debit',
                      }"
                      class="w-7 h-7 flex items-center justify-center rounded-full"
                    >
                      <font-awesome-icon
                        :icon="transaction.type === 'credit' ? 'fa-solid fa-arrow-up' : 'fa-solid fa-arrow-down'"
                        :class="{
                          'text-blue-600': transaction.type === 'credit',
                          'text-red-600': transaction.type === 'debit',
                        }"
                        class="text-xs"
                      />
                    </span>
                  </td>
                </tr>
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
import { index } from "@/utils/requests/httpUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import MoneyField from "../fields/number/MoneyField.vue";

export default {
  components: {
    MoneyField,
  },
  data() {
    return {
      searchTerm: "",
      selectedBankAccount: "",
      transactions: [],
      filteredTransactions: [],
      bankAccounts: [],
    };
  },
  computed: {
    searchFilteredTransactions() {
      if (!this.searchTerm) {
        return this.transactions;
      }
      
      const term = this.searchTerm.toLowerCase();
      return this.transactions.filter(transaction => {
        if (transaction.amount && transaction.amount.toString().includes(term)) {
          return true;
        }
        
        if (transaction.invoice?.proposal?.opportunity) {
          const opportunity = transaction.invoice.proposal.opportunity;
          const clientName = this.getClientName(opportunity).toLowerCase();
          if (clientName.includes(term)) {
            return true;
          }
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
            transactions: []
          };
        }
        
        groups[monthKey].transactions.push(transaction);
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
    }
  },
  methods: {
    formatDateBr,
    
    getClientName(opportunity) {
      if (!opportunity) return 'Cliente não identificado';
      if (opportunity.company?.business_name) {
        return opportunity.company.business_name;
      }
      if (opportunity.company?.legal_name) {
        return opportunity.company.legal_name;
      }
      if (opportunity.lead?.name) {
        return opportunity.lead.name;
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
        
        console.log("transactions", this.transactions);
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