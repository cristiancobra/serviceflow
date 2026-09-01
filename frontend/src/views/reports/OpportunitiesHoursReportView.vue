<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-hourglass-half" class="page-icon" />
        <h1>PREVISTO X REALIZADO</h1>
      </div>
    </div>

    <p class="text-gray-500 text-sm -mt-2 mb-4">
      Compara, por oportunidade, o valor de hora previsto na proposta aceita com o valor
      estimado a partir das horas reais apontadas nas jornadas das tarefas.
    </p>

    <YearFilter @year-change="handleYearChange" />

    <!-- Summary Cards -->
    <section class="section-container">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 shadow-sm">
          <div class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Valor Previsto</div>
          <div class="mt-1 text-xl font-bold text-blue-800">
            {{ formatCurrency(totals.predicted_value) }}
          </div>
        </div>
        <div class="rounded-xl border border-purple-200 bg-purple-50 p-4 shadow-sm">
          <div class="text-xs font-semibold text-purple-700 uppercase tracking-wide">Valor Real (estimado)</div>
          <div class="mt-1 text-xl font-bold text-purple-800">
            {{ formatCurrency(totals.real_value) }}
          </div>
        </div>
        <div
          class="rounded-xl border p-4 shadow-sm"
          :class="totals.difference_value < 0 ? 'border-red-200 bg-red-50' : 'border-green-200 bg-green-50'"
        >
          <div
            class="text-xs font-semibold uppercase tracking-wide"
            :class="totals.difference_value < 0 ? 'text-red-700' : 'text-green-700'"
          >
            Lucro/Prejuízo (previsto - real)
          </div>
          <div
            class="mt-1 text-xl font-bold"
            :class="totals.difference_value < 0 ? 'text-red-800' : 'text-green-800'"
          >
            {{ formatCurrency(totals.difference_value) }}
          </div>
        </div>
      </div>
    </section>

    <!-- Table -->
    <section class="section-container">
      <div v-if="!isLoading && opportunities.length > 0" class="flex flex-wrap gap-2 mb-3">
        <button
          @click="sortMode = 'date'"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-semibold transition-colors',
            sortMode === 'date' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
          ]"
        >
          <font-awesome-icon icon="fa-solid fa-calendar" class="mr-1" />
          Ordenar por Data
        </button>
        <button
          @click="sortMode = 'profit-desc'"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-semibold transition-colors',
            sortMode === 'profit-desc' ? 'bg-green-700 text-white' : 'bg-green-50 text-green-700 hover:bg-green-100',
          ]"
        >
          <font-awesome-icon icon="fa-solid fa-arrow-up" class="mr-1" />
          Maior Lucro
        </button>
        <button
          @click="sortMode = 'profit-asc'"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-semibold transition-colors',
            sortMode === 'profit-asc' ? 'bg-red-700 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100',
          ]"
        >
          <font-awesome-icon icon="fa-solid fa-arrow-down" class="mr-1" />
          Menor Lucro
        </button>
      </div>

      <div v-if="isLoading" class="text-center text-gray-500 py-10">
        Carregando...
      </div>

      <div v-else-if="opportunities.length === 0" class="text-center text-gray-500 py-10">
        Nenhuma oportunidade encontrada em {{ selectedYear }}.
      </div>

      <div v-else class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
            <tr>
              <th class="px-4 py-3 text-left">Oportunidade</th>
              <th class="px-4 py-3 text-left">Status</th>
              <th class="px-4 py-3 text-left">Empresa</th>
              <th class="px-4 py-3 text-right">Horas Previstas</th>
              <th class="px-4 py-3 text-right">Valor/Hora Previsto</th>
              <th class="px-4 py-3 text-right">Valor Previsto</th>
              <th class="px-4 py-3 text-right">Horas Reais</th>
              <th class="px-4 py-3 text-right">Valor Real (estimado)</th>
              <th class="px-4 py-3 text-right">Lucro/Prejuízo</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr
              v-for="opportunity in sortedOpportunities"
              :key="opportunity.opportunity_id"
              class="hover:bg-gray-50"
            >
              <td class="px-4 py-3 font-medium text-gray-800">
                {{ opportunity.opportunity_name }}
              </td>
              <td class="px-4 py-3">
                <span
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                  :class="statusBadge(opportunity.status).classes"
                >
                  {{ statusBadge(opportunity.status).label }}
                </span>
              </td>
              <td class="px-4 py-3 text-gray-600">
                {{ opportunity.company_name || '-' }}
              </td>
              <td class="px-4 py-3 text-right text-gray-600">
                {{ opportunity.predicted_hours !== null ? formatHours(opportunity.predicted_hours) : '-' }}
              </td>
              <td class="px-4 py-3 text-right text-gray-600">
                {{ opportunity.predicted_hourly_rate !== null ? formatCurrency(opportunity.predicted_hourly_rate) : '-' }}
              </td>
              <td class="px-4 py-3 text-right text-gray-800 font-semibold">
                {{ opportunity.predicted_value !== null ? formatCurrency(opportunity.predicted_value) : '-' }}
              </td>
              <td class="px-4 py-3 text-right text-gray-600">
                {{ formatHours(opportunity.real_hours) }}
              </td>
              <td class="px-4 py-3 text-right text-gray-800 font-semibold">
                {{ opportunity.real_value !== null ? formatCurrency(opportunity.real_value) : '-' }}
              </td>
              <td
                class="px-4 py-3 text-right font-semibold"
                :class="differenceClass(opportunity.difference_value)"
              >
                <template v-if="opportunity.difference_value !== null">
                  {{ formatCurrency(opportunity.difference_value) }}
                  <span class="block text-xs font-normal">
                    ({{ opportunity.difference_percentage }}%)
                    <font-awesome-icon
                      v-if="opportunity.status === 'open'"
                      icon="fa-solid fa-circle-exclamation"
                      class="text-amber-500"
                      title="Oportunidade em andamento — valores ainda parciais"
                    />
                  </span>
                </template>
                <template v-else>-</template>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<script>
import { getOpportunitiesHoursReport } from "@/utils/requests/httpUtils";
import YearFilter from "@/components/filters/YearFilter.vue";

export default {
  name: "OpportunitiesHoursReportView",
  components: {
    YearFilter,
  },
  data() {
    return {
      isLoading: false,
      selectedYear: new Date().getFullYear(),
      opportunities: [],
      sortMode: "date",
      totals: {
        predicted_value: 0,
        real_value: 0,
        difference_value: 0,
      },
    };
  },
  computed: {
    sortedOpportunities() {
      if (this.sortMode === "date") {
        return this.opportunities;
      }

      return [...this.opportunities].sort((a, b) => {
        if (a.difference_value === null) return 1;
        if (b.difference_value === null) return -1;

        return this.sortMode === "profit-desc"
          ? b.difference_value - a.difference_value
          : a.difference_value - b.difference_value;
      });
    },
  },
  methods: {
    async fetchReport(year) {
      this.isLoading = true;
      try {
        const response = await getOpportunitiesHoursReport(year);
        this.opportunities = response.data || [];
        this.totals = response.totals || {
          predicted_value: 0,
          real_value: 0,
          difference_value: 0,
        };
      } catch (error) {
        console.error("Erro ao buscar relatório de horas das oportunidades:", error);
        this.opportunities = [];
      } finally {
        this.isLoading = false;
      }
    },
    handleYearChange(year) {
      this.selectedYear = year;
      this.fetchReport(year);
    },
    formatCurrency(value) {
      return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
      }).format(value || 0);
    },
    formatHours(hours) {
      return `${new Intl.NumberFormat("pt-BR", { maximumFractionDigits: 1 }).format(hours || 0)}h`;
    },
    differenceClass(value) {
      if (value === null || value === undefined) return "text-gray-400";
      return value < 0 ? "text-red-700" : value > 0 ? "text-green-700" : "text-gray-600";
    },
    statusBadge(status) {
      const badges = {
        open: { label: "Aberta", classes: "bg-amber-100 text-amber-800" },
        concluded: { label: "Concluída", classes: "bg-green-100 text-green-800" },
        canceled: { label: "Cancelada", classes: "bg-gray-200 text-gray-600" },
      };
      return badges[status] || badges.open;
    },
  },
};
</script>

<style scoped>
</style>
