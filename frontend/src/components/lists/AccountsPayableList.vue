<template>
  <div>
    <!-- Empty State -->
    <div v-if="invoices.length === 0"
      class="flex flex-col items-center justify-center py-16 text-gray-400">
      <font-awesome-icon icon="fa-solid fa-file-invoice-dollar" class="text-5xl mb-4 opacity-30" />
      <p class="text-lg font-medium">Nenhuma conta encontrada</p>
      <p class="text-sm mt-1">Crie sua primeira conta a pagar clicando em "Nova Conta a Pagar"</p>
    </div>

    <!-- Grouped by month -->
    <div v-for="monthGroup in groupedInvoices" :key="monthGroup.monthKey" class="mb-8">
      <!-- Month Header -->
      <div class="flex items-center mb-4 sticky top-0 z-10">
        <div class="flex items-center gap-3 bg-white pe-6 pb-1 pt-10">
          <span class="font-bold text-lg whitespace-nowrap text-red-700">{{ monthGroup.monthLabel }}</span>
          <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
            {{ monthGroup.invoices.length }} {{ monthGroup.invoices.length === 1 ? 'conta' : 'contas' }}
          </span>
          <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-full border border-red-200">
            {{ formatCurrency(monthGroup.total) }}
          </span>
        </div>
      </div>

      <!-- Invoice cards -->
      <div class="space-y-2">
        <router-link
          v-for="invoice in monthGroup.invoices"
          :key="invoice.id"
          :to="{ name: 'invoiceShow', params: { id: invoice.id } }"
          class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-200 hover:border-red-300 hover:shadow-sm transition-all duration-200 no-underline"
          :class="{
            'border-l-4 border-l-orange-400': invoice.status === 'overdue',
            'border-l-4 border-l-green-400': invoice.status === 'paid',
            'border-l-4 border-l-gray-300': invoice.status === 'cancelled',
          }">
          <!-- Icon + Name -->
          <div class="flex items-center gap-3 min-w-0 flex-1">
            <div class="flex items-center justify-center w-10 h-10 rounded-full flex-shrink-0"
              :class="getIconBg(invoice)">
              <font-awesome-icon :icon="getCategoryIcon(invoice.category)" class="text-white text-sm" />
            </div>
            <div class="min-w-0">
              <p class="font-semibold text-gray-900 text-sm truncate">
                {{ invoice.name || ('Fatura #' + invoice.id) }}
              </p>
              <p class="text-xs text-gray-500 truncate">
                {{ getSupplierName(invoice) }}
                <span v-if="invoice.proposal" class="ml-1 text-indigo-500">• Proposta #{{ invoice.proposal.id }}</span>
              </p>
              <!-- Department badge -->
              <span v-if="invoice.department"
                class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-full text-xs font-semibold"
                :style="{ backgroundColor: invoice.department.color + '20', color: invoice.department.color }">
                <font-awesome-icon :icon="invoice.department.icon" class="text-xs" />
                {{ invoice.department.name }}
              </span>
            </div>
          </div>

          <!-- Task badge -->
          <div v-if="invoice.tasks && invoice.tasks.length > 0" class="mx-3 flex-shrink-0">
            <span
              class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
              <font-awesome-icon icon="fa-solid fa-check-circle" class="text-xs" />
              Tarefa
            </span>
          </div>

          <!-- Date + Status -->
          <div class="flex flex-col items-end gap-1 flex-shrink-0 ml-3">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
              :class="getStatusClass(invoice.status)">
              {{ getStatusLabel(invoice.status) }}
            </span>
            <span class="text-xs text-gray-500">
              {{ formatDateBr(invoice.date_due) }}
            </span>
          </div>

          <!-- Amount -->
          <div class="flex flex-col items-end ml-4 flex-shrink-0 min-w-[90px]">
            <span class="font-bold text-gray-900 text-sm">{{ formatCurrency(invoice.price) }}</span>
            <span v-if="invoice.balance > 0" class="text-xs text-red-500">
              Saldo: {{ formatCurrency(invoice.balance) }}
            </span>
            <span v-else class="text-xs text-green-600 font-medium">Pago</span>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { formatDateBr } from "@/utils/date/dateUtils";

export default {
  name: "AccountsPayableList",
  props: {
    invoices: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  computed: {
    groupedInvoices() {
      const groups = {};

      this.invoices.forEach((invoice) => {
        const date = new Date(invoice.date_due);
        const monthKey = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}`;
        const monthLabel = new Intl.DateTimeFormat("pt-BR", {
          month: "long",
          year: "numeric",
        }).format(date);

        if (!groups[monthKey]) {
          groups[monthKey] = {
            monthKey,
            monthLabel: monthLabel.charAt(0).toUpperCase() + monthLabel.slice(1),
            invoices: [],
            total: 0,
          };
        }

        groups[monthKey].invoices.push(invoice);
        groups[monthKey].total += Number(invoice.balance) || 0;
      });

      return Object.values(groups).sort((a, b) => b.monthKey.localeCompare(a.monthKey));
    },
  },
  methods: {
    formatDateBr,
    formatCurrency(value) {
      return new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(value || 0);
    },
    getSupplierName(invoice) {
      if (invoice.lead?.name) return invoice.lead.name;
      if (invoice.company?.name) return invoice.company.name;
      if (invoice.proposal?.opportunity?.name) return invoice.proposal.opportunity.name;
      return "Sem fornecedor";
    },
    getCategoryIcon(category) {
      const map = {
        fixed_cost: "fa-solid fa-home",
        recurring: "fa-solid fa-rotate",
        supplier: "fa-solid fa-truck",
        operational: "fa-solid fa-briefcase",
        other: "fa-solid fa-receipt",
      };
      return map[category] || "fa-solid fa-receipt";
    },
    getIconBg(invoice) {
      if (invoice.status === "paid") return "bg-gray-400";
      if (invoice.status === "overdue") return "bg-orange-500";
      if (invoice.status === "cancelled") return "bg-gray-300";
      return "bg-red-500";
    },
    getStatusClass(status) {
      const map = {
        pending: "bg-yellow-100 text-yellow-800",
        partial: "bg-blue-100 text-blue-800",
        paid: "bg-green-100 text-green-800",
        overdue: "bg-orange-100 text-orange-800",
        cancelled: "bg-gray-100 text-gray-600",
      };
      return map[status] || "bg-gray-100 text-gray-600";
    },
    getStatusLabel(status) {
      const map = {
        pending: "Pendente",
        partial: "Parcial",
        paid: "Pago",
        overdue: "Vencida",
        cancelled: "Cancelada",
      };
      return map[status] || status;
    },
  },
};
</script>
