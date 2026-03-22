<template>
  <div class="page-container">
    <YearFilter @year-change="handleYearChange" />

    <!-- Seção de Propostas -->
    
      <div class="page-header">
        <div class="page-title">
          <font-awesome-icon icon="fa-solid fa-tools" class="page-icon" />
          <h1
          >
            Propostas
          </h1>
        </div>
      </div>
      <div
        class="flex items-center justify-between mb-4 p-4 bg-blue-50 rounded-lg"
      >
        <span class="text-gray-700 font-semibold">Propostas abertas:</span>
        <span class="text-2xl font-bold text-blue-600">{{
          reports.acceptedProposalsCount
        }}</span>
      </div>

      <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
        <span class="text-gray-700 font-semibold">Total em propostas:</span>
        <div class="w-64">
          <money-field
            name="total_price"
            v-model="reports.total"
            :readonly="true"
          />
        </div>
      </div>


    <!-- Seção de Transações -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-10">
      <h3
        class="text-xl font-bold text-gray-800 mb-4 border-b-2 border-green-500 pb-2"
      >
        Transações de {{ selectedYear }}
      </h3>

      <div class="space-y-4">
        <!-- Total de Entradas -->
        <div
          class="flex items-center justify-between p-4 bg-green-50 rounded-lg border-l-4 border-green-500"
        >
          <span class="text-green-700 font-semibold flex items-center">
            <svg
              class="w-5 h-5 mr-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M7 11l5-5m0 0l5 5m-5-5v12"
              ></path>
            </svg>
            Total de Entradas:
          </span>
          <div class="w-64">
            <money-field
              name="total_entries"
              v-model="transactionsTotals.totalEntries"
              :readonly="true"
            />
          </div>
        </div>

        <!-- Total de Saídas -->
        <div
          class="flex items-center justify-between p-4 bg-red-50 rounded-lg border-l-4 border-red-500"
        >
          <span class="text-red-700 font-semibold flex items-center">
            <svg
              class="w-5 h-5 mr-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 13l-5 5m0 0l-5-5m5 5V6"
              ></path>
            </svg>
            Total de Saídas:
          </span>
          <div class="w-64">
            <money-field
              name="total_exits"
              v-model="transactionsTotals.totalExits"
              :readonly="true"
            />
          </div>
        </div>

        <!-- Saldo -->
        <div
          class="flex items-center justify-between p-4 rounded-lg border-l-4"
          :class="
            transactionsTotals.balance >= 0
              ? 'bg-blue-50 border-blue-500'
              : 'bg-orange-50 border-orange-500'
          "
        >
          <span
            class="font-bold flex items-center"
            :class="
              transactionsTotals.balance >= 0
                ? 'text-blue-700'
                : 'text-orange-700'
            "
          >
            <svg
              class="w-5 h-5 mr-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              ></path>
            </svg>
            Saldo:
          </span>
          <div class="w-64">
            <money-field
              name="balance"
              v-model="transactionsTotals.balance"
              :readonly="true"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  getTotalProposals,
  getTransactionsTotals,
} from "../../utils/requests/httpUtils";
import MoneyField from "../../components/fields/number/MoneyField.vue";
import YearFilter from "../../components/filters/YearFilter.vue";

export default {
  data() {
    return {
      reports: "",
      selectedYear: new Date().getFullYear(),
      transactionsTotals: {
        totalEntries: 0,
        totalExits: 0,
        balance: 0,
      },
    };
  },
  components: {
    MoneyField,
    YearFilter,
  },
  methods: {
    async fetchTotalProposals() {
      try {
        this.reports = await getTotalProposals();
      } catch (error) {
        console.error("Erro ao buscar o total de propostas:", error);
      }
    },
    async fetchTransactionsTotals(year) {
      try {
        this.transactionsTotals = await getTransactionsTotals(year);
      } catch (error) {
        console.error("Erro ao buscar totais de transações:", error);
        // Define valores padrão em caso de erro
        this.transactionsTotals = {
          totalEntries: 0,
          totalExits: 0,
          balance: 0,
        };
      }
    },
    handleYearChange(year) {
      this.selectedYear = year;
      this.fetchTransactionsTotals(year);
    },
  },
  created() {
    this.fetchTotalProposals();
    this.fetchTransactionsTotals(this.selectedYear);
  },
};
</script>

<style scoped>
</style>