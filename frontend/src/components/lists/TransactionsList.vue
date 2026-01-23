<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-coins" class="page-icon" />
        <h1>PAGAMENTOS</h1>
      </div>
      <div class="page-action">
        <!-- Espaço para futuro botão de criar transação -->
      </div>
    </div>

    <section class="section-container">
      <div class="filters-container">
        <div class="search-container">
          <input
            type="text"
            class="search-input"
            v-model="searchTerm"
            placeholder="Digite para buscar"
          />
        </div>
        
        <div class="filter-container">
          <label for="bank-account-filter" class="filter-label">Conta Bancária:</label>
          <select
            id="bank-account-filter"
            v-model="selectedBankAccount"
            @change="filterByBankAccount"
            class="filter-select"
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

      <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full">
          <thead class="bg-gray-50 border-b-2 border-gray-200 sticky top-0">
            <tr class="divide-x divide-gray-200">
              <th class="col-span-1 text-center font-bold text-sm text-gray-700 px-4 py-3 w-1/12">Data</th>
              <th class="text-left font-bold text-sm text-gray-700 px-4 py-3 w-2/12">Cliente</th>
              <th class="text-left font-bold text-sm text-gray-700 px-4 py-3 w-2/12">Oportunidade</th>
              <th class="text-center font-bold text-sm text-gray-700 px-4 py-3 w-1/12">Proposta</th>
              <th class="text-left font-bold text-sm text-gray-700 px-4 py-3 w-2/12">Fatura</th>
              <th class="text-left font-bold text-sm text-gray-700 px-4 py-3 w-2/12">Conta</th>
              <th class="text-right font-bold text-sm text-gray-700 px-4 py-3 w-1/12">Valor</th>
              <th class="text-center font-bold text-sm text-gray-700 px-4 py-3 w-1/12">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr
              v-for="transaction in filteredTransactions"
              v-bind:key="transaction.id"
              class="hover:bg-gray-50 transition-colors divide-x divide-gray-100"
            >
              <td class="text-center text-black text-sm font-medium px-4 py-3 w-1/12">
                {{ formatDateBr(transaction.transaction_date) }}
              </td>
              
              <td class="text-left text-black text-sm px-4 py-3 w-2/12 truncate font-semibold">
                <p v-if="transaction.invoice?.proposal?.opportunity" class="truncate" :title="getClientName(transaction.invoice.proposal.opportunity)">
                  {{ getClientName(transaction.invoice.proposal.opportunity) }}
                </p>
                <p v-else class="text-gray-500">-</p>
              </td>
              
              <td class="text-left text-black text-sm px-4 py-3 w-2/12 truncate">
                <router-link
                  v-if="transaction.invoice?.proposal?.opportunity?.name"
                  :to="{ name: 'opportunityShow', params: { id: transaction.invoice.proposal.opportunity.id } }"
                  class="text-blue-600 hover:text-blue-800 font-medium truncate block"
                  :title="transaction.invoice.proposal.opportunity.name"
                >
                  {{ transaction.invoice.proposal.opportunity.name }}
                </router-link>
                <p v-else class="text-gray-500">-</p>
              </td>
              
              <td class="text-center text-black text-sm px-4 py-3 w-1/12">
                <router-link
                  v-if="transaction.invoice?.proposal"
                  :to="{ name: 'proposalShow', params: { id: transaction.invoice.proposal.id } }"
                  class="text-blue-600 hover:text-blue-800 font-medium inline-flex items-center justify-center"
                  :title="'Proposta ' + transaction.invoice.proposal.id"
                >
                  <font-awesome-icon icon="fa-solid fa-magnifying-glass" class="text-xs" />
                </router-link>
                <p v-else class="text-gray-500">-</p>
              </td>
              
              <td class="text-left text-black text-sm px-4 py-3 w-2/12 truncate">
                <router-link
                  v-if="transaction.invoice"
                  :to="{ name: 'invoiceShow', params: { id: transaction.invoice.id } }"
                  class="text-blue-600 hover:text-blue-800 font-semibold truncate block"
                  :title="'Fatura #' + transaction.invoice.id"
                >
                  #{{ transaction.invoice.id }}
                </router-link>
                <div v-else class="text-gray-500 text-xs">
                  Avulsa
                </div>
              </td>
              
              <td class="text-left text-black text-sm px-4 py-3 w-2/12 truncate">
                <p v-if="transaction.bank_account" class="truncate" :title="transaction.bank_account.name || transaction.bank_account.bank_name">
                  {{ transaction.bank_account.name || transaction.bank_account.bank_name }}
                </p>
                <p v-else class="text-gray-500">-</p>
              </td>
              
              <td class="text-right text-black text-sm font-semibold px-4 py-3 w-1/12">
                <money-field name="amount" v-model="transaction.amount" :readonly="true" />
              </td>
              
              <td class="text-center px-4 py-3 w-1/12">
                <span 
                  class="status-badge inline-block text-center"
                  :class="getStatusClass(transaction.status)"
                >
                  {{ getStatusLabel(transaction.status) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="filteredTransactions && filteredTransactions.length === 0" class="empty-state">
        <p>Nenhum pagamento encontrado</p>
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
    // Filtra transações baseado no termo de busca
    searchFilteredTransactions() {
      if (!this.searchTerm) {
        return this.transactions;
      }
      
      const term = this.searchTerm.toLowerCase();
      return this.transactions.filter(transaction => {
        // Busca por valor
        if (transaction.amount && transaction.amount.toString().includes(term)) {
          return true;
        }
        
        // Busca por cliente/empresa
        if (transaction.invoice?.proposal?.opportunity) {
          const opportunity = transaction.invoice.proposal.opportunity;
          const clientName = this.getClientName(opportunity).toLowerCase();
          if (clientName.includes(term)) {
            return true;
          }
        }
        
        // Busca por conta bancária
        if (transaction.bank_account) {
          const accountName = (transaction.bank_account.name || transaction.bank_account.bank_name || '').toLowerCase();
          if (accountName.includes(term)) {
            return true;
          }
        }
        
        return false;
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
          return 'status-success';
        case 'pending':
          return 'status-warning';
        case 'cancelled':
          return 'status-danger';
        default:
          return 'status-default';
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
    
    filterByBankAccount() {
      this.applyFilters();
    },
    
    applyFilters() {
      let filtered = this.searchFilteredTransactions;
      
      // Filtro por conta bancária
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
        
        // Ordena do mais recente para o mais antigo
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

<style scoped>
.filters-container {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  align-items: end;
}

.filter-container {
  display: flex;
  flex-direction: column;
  min-width: 200px;
}

.filter-label {
  font-weight: 600;
  margin-bottom: 0.25rem;
  color: #374151;
}

.filter-select {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background-color: white;
  color: #374151;
}

.filter-select:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
}

.list-header {
  display: flex;
  padding: 1rem;
  background-color: #f9fafb;
  border-bottom: 2px solid #e5e7eb;
  color: #374151;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status-success {
  background-color: #dcfce7;
  color: #166534;
}

.status-warning {
  background-color: #fef3c7;
  color: #92400e;
}

.status-danger {
  background-color: #fecaca;
  color: #991b1b;
}

.status-default {
  background-color: #f3f4f6;
  color: #4b5563;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.empty-state p {
  font-size: 1.125rem;
}
</style>