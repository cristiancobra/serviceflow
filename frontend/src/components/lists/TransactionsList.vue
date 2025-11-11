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

      <div class="list-header">
        <div class="w-1/10 text-center font-bold">Data</div>
        <div class="w-3/10 text-center font-bold">Oportunidade</div>
        <div class="w-3/10 text-center font-bold">Cliente/Fatura</div>
        <div class="w-2/10 text-center font-bold">Conta Bancária</div>
        <div class="w-2/10 text-center font-bold">Valor</div>
        <div class="w-2/10 text-center font-bold">Status</div>
      </div>

      <div
        v-for="transaction in filteredTransactions"
        v-bind:key="transaction.id"
        class="list-line flex w-full"
      >
        <div class="w-1/10 text-center text-black">
          {{ formatDateBr(transaction.transaction_date) }}
        </div>
        
        <div class="w-3/10 text-left text-black">
          <p v-if="transaction.invoice?.proposal?.opportunity?.name" class="font-medium">
            {{ transaction.invoice.proposal.opportunity.name }}
          </p>
          <p v-else class="text-gray-500">-</p>
        </div>
        
        <div class="w-3/10 text-left text-black">
          <div v-if="transaction.invoice">
            <p class="font-semibold">
              Fatura #{{ transaction.invoice.id }}
            </p>
            <p class="text-sm text-gray-600" v-if="transaction.invoice.proposal?.opportunity">
              {{ getClientName(transaction.invoice.proposal.opportunity) }}
            </p>
          </div>
          <div v-else class="text-gray-500">
            Transação avulsa
          </div>
        </div>
        
        <div class="w-2/10 text-center text-black">
          <p v-if="transaction.bank_account">
            {{ transaction.bank_account.name || transaction.bank_account.bank_name }}
          </p>
          <p v-else class="text-gray-500">-</p>
        </div>
        
        <div class="w-2/10 text-center text-black">
          <money-field name="amount" v-model="transaction.amount" :readonly="true" />
        </div>
        
        <div class="w-2/10 text-center">
          <span 
            class="status-badge"
            :class="getStatusClass(transaction.status)"
          >
            {{ getStatusLabel(transaction.status) }}
          </span>
        </div>
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