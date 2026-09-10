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

    <!-- Seção MEI -->
    <div v-if="account.is_mei" class="bg-white rounded-lg shadow-md p-6 mt-10">
      <h3
        class="text-xl font-bold text-gray-800 mb-4 border-b-2 border-purple-500 pb-2"
      >
        MEI - Microempreendedor Individual ({{ selectedYear }})
      </h3>

      <div class="space-y-4">
        <!-- Limite anual -->
        <div
          class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border-l-4 border-gray-400"
        >
          <span class="text-gray-700 font-semibold">Limite anual do MEI:</span>
          <div class="w-64">
            <money-field name="mei_limit" v-model="meiAnnualLimit" :readonly="true" />
          </div>
        </div>

        <!-- Faturado no ano -->
        <div
          class="flex items-center justify-between p-4 bg-purple-50 rounded-lg border-l-4 border-purple-500"
        >
          <span class="text-purple-700 font-semibold">Faturado no ano:</span>
          <div class="w-64">
            <money-field name="mei_revenue" v-model="meiRevenue" :readonly="true" />
          </div>
        </div>

        <!-- Disponível para faturar / Excedente -->
        <div
          class="flex items-center justify-between p-4 rounded-lg border-l-4"
          :class="
            meiExceeded > 0
              ? 'bg-red-50 border-red-500'
              : 'bg-green-50 border-green-500'
          "
        >
          <span
            class="font-semibold"
            :class="meiExceeded > 0 ? 'text-red-700' : 'text-green-700'"
          >
            {{ meiExceeded > 0 ? "Limite excedido em:" : "Ainda pode faturar:" }}
          </span>
          <div class="w-64">
            <money-field
              name="mei_available"
              v-model="meiAvailableDisplay"
              :readonly="true"
            />
          </div>
        </div>

        <!-- Barra de progresso do limite -->
        <div class="p-4 bg-gray-50 rounded-lg">
          <div class="flex justify-between text-sm text-gray-600 mb-1">
            <span>Uso do limite anual</span>
            <span>{{ meiUsagePercentage.toFixed(1) }}%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-3">
            <div
              class="h-3 rounded-full transition-all"
              :class="
                meiExceeded > 0
                  ? 'bg-red-500'
                  : meiUsagePercentage >= 80
                  ? 'bg-yellow-500'
                  : 'bg-green-500'
              "
              :style="{ width: meiUsagePercentage + '%' }"
            ></div>
          </div>
        </div>

        <!-- Apuração de lucro isento x tributável -->
        <div class="pt-2">
          <h4 class="text-md font-semibold text-gray-700 mb-3">
            Apuração de lucro para o IRPF (presunção de 32% - prestação de
            serviços)
          </h4>
          <div class="space-y-4">
            <div
              class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-500"
            >
              <span class="text-yellow-700 font-semibold"
                >Lucro tributável (32%):</span
              >
              <div class="w-64">
                <money-field
                  name="mei_taxable_profit"
                  v-model="meiTaxableProfit"
                  :readonly="true"
                />
              </div>
            </div>
            <div
              class="flex items-center justify-between p-4 bg-teal-50 rounded-lg border-l-4 border-teal-500"
            >
              <span class="text-teal-700 font-semibold"
                >Lucro isento (68%):</span
              >
              <div class="w-64">
                <money-field
                  name="mei_exempt_profit"
                  v-model="meiExemptProfit"
                  :readonly="true"
                />
              </div>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-2">
            Valores de referência para a Declaração de Ajuste Anual do IRPF,
            considerando o percentual de presunção de lucro de 32% aplicável a
            atividades de prestação de serviços (rendimento tributável x
            rendimento isento e não tributável recebido de pessoa jurídica).
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  getTotalProposals,
  getTransactionsTotals,
  show,
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
      account: {
        is_mei: false,
        mei_annual_limit: 0,
      },
    };
  },
  components: {
    MoneyField,
    YearFilter,
  },
  computed: {
    meiAnnualLimit() {
      return this.account.mei_annual_limit || 0;
    },
    meiRevenue() {
      return this.transactionsTotals.totalEntries || 0;
    },
    meiRemaining() {
      return Math.max(this.meiAnnualLimit - this.meiRevenue, 0);
    },
    meiExceeded() {
      return Math.max(this.meiRevenue - this.meiAnnualLimit, 0);
    },
    meiAvailableDisplay() {
      return this.meiExceeded > 0 ? this.meiExceeded : this.meiRemaining;
    },
    meiUsagePercentage() {
      if (!this.meiAnnualLimit) return 0;
      return Math.min((this.meiRevenue / this.meiAnnualLimit) * 100, 100);
    },
    meiTaxableProfit() {
      return this.meiRevenue * 0.32;
    },
    meiExemptProfit() {
      return this.meiRevenue * 0.68;
    },
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
    async fetchAccount() {
      try {
        const accountId =
          this.$store.state.userData?.account_id || this.$store.state.accountId;
        if (accountId) {
          this.account = await show("accounts", accountId);
        }
      } catch (error) {
        console.error("Erro ao buscar dados da conta:", error);
      }
    },
  },
  created() {
    this.fetchTotalProposals();
    this.fetchTransactionsTotals(this.selectedYear);
    this.fetchAccount();
  },
};
</script>

<style scoped>
</style>