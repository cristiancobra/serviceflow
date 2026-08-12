<template>
  <div class="page-container">
    <!-- Header -->
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-file-invoice-dollar" class="page-icon text-red-600" />
        <h1>CONTAS A PAGAR</h1>
      </div>
      <button
        @click="showCreateForm = true"
        class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold text-sm"
      >
        <font-awesome-icon icon="fa-solid fa-plus" />
        Nova Conta a Pagar
      </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm">
        <div class="text-xs font-semibold text-red-700 uppercase tracking-wide">Total Pendente</div>
        <div class="mt-1 text-xl font-bold text-red-800">
          {{ formatCurrency(summaries.totalPending) }}
        </div>
        <div class="text-xs text-red-500 mt-0.5">{{ summaries.countPending }} faturas</div>
      </div>
      <div class="rounded-xl border border-orange-200 bg-orange-50 p-4 shadow-sm">
        <div class="text-xs font-semibold text-orange-700 uppercase tracking-wide">Vencidas</div>
        <div class="mt-1 text-xl font-bold text-orange-800">
          {{ formatCurrency(summaries.totalOverdue) }}
        </div>
        <div class="text-xs text-orange-500 mt-0.5">{{ summaries.countOverdue }} faturas</div>
      </div>
      <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 shadow-sm">
        <div class="text-xs font-semibold text-yellow-700 uppercase tracking-wide">A Vencer em 30d</div>
        <div class="mt-1 text-xl font-bold text-yellow-800">
          {{ formatCurrency(summaries.totalUpcoming) }}
        </div>
        <div class="text-xs text-yellow-500 mt-0.5">{{ summaries.countUpcoming }} faturas</div>
      </div>
      <div class="rounded-xl border border-green-200 bg-green-50 p-4 shadow-sm">
        <div class="text-xs font-semibold text-green-700 uppercase tracking-wide">Pagas este Mês</div>
        <div class="mt-1 text-xl font-bold text-green-800">
          {{ formatCurrency(summaries.totalPaidThisMonth) }}
        </div>
        <div class="text-xs text-green-500 mt-0.5">{{ summaries.countPaidThisMonth }} faturas</div>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-2 mb-3">
      <button
        v-for="f in filterOptions"
        :key="f.value"
        @click="setFilter(f.value)"
        :class="[
          'px-4 py-2 rounded-lg text-sm font-semibold transition-colors',
          activeFilter === f.value ? f.activeClass : f.inactiveClass,
        ]"
      >
        <font-awesome-icon :icon="f.icon" class="mr-1" />
        {{ f.label }}
        <span v-if="f.count !== undefined" class="ml-1 text-xs opacity-75">({{ f.count }})</span>
      </button>
    </div>

    <!-- Department Filter -->
    <div v-if="departments.length > 0" class="flex flex-wrap gap-2 mb-4">
      <button
        @click="activeDepartment = null"
        :class="[
          'px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors border',
          activeDepartment === null
            ? 'bg-gray-800 text-white border-gray-800'
            : 'bg-white text-gray-600 border-gray-300 hover:border-gray-400',
        ]"
      >
        Todos os departamentos
      </button>
      <button
        v-for="dept in departments"
        :key="dept.id"
        @click="activeDepartment = dept.id"
        :class="[
          'px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors border',
          activeDepartment === dept.id ? 'text-white border-transparent' : 'bg-white border-gray-300',
        ]"
        :style="activeDepartment === dept.id
          ? { backgroundColor: dept.color, borderColor: dept.color }
          : { color: dept.color }"
      >
        <font-awesome-icon :icon="dept.icon" class="mr-1" />
        {{ dept.name }}
      </button>
    </div>

    <!-- Search -->
    <div class="relative mb-4">
      <font-awesome-icon icon="fa-solid fa-search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm" />
      <input
        v-model="searchTerm"
        type="text"
        placeholder="Buscar por nome, fornecedor..."
        class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm"
      />
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="flex items-center justify-center py-16">
      <font-awesome-icon icon="fa-solid fa-spinner" class="animate-spin text-3xl text-red-500" />
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredInvoices.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
      <font-awesome-icon icon="fa-solid fa-file-invoice-dollar" class="text-5xl mb-4 opacity-30" />
      <p class="text-lg font-medium">Nenhuma conta encontrada</p>
      <p class="text-sm mt-1">Crie sua primeira conta a pagar clicando em "Nova Conta a Pagar"</p>
    </div>

    <!-- Invoice List -->
    <div v-else class="space-y-2">
      <router-link
        v-for="invoice in filteredInvoices"
        :key="invoice.id"
        :to="{ name: 'invoiceShow', params: { id: invoice.id } }"
        class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-200 hover:border-red-300 hover:shadow-sm transition-all duration-200 no-underline"
        :class="{
          'border-l-4 border-l-orange-400': invoice.status === 'overdue',
          'border-l-4 border-l-green-400': invoice.status === 'paid',
          'border-l-4 border-l-gray-300': invoice.status === 'cancelled',
        }"
      >
        <!-- Icon + Name -->
        <div class="flex items-center gap-3 min-w-0 flex-1">
          <div
            class="flex items-center justify-center w-10 h-10 rounded-full flex-shrink-0"
            :class="getIconBg(invoice)"
          >
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
            <span
              v-if="invoice.department"
              class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-full text-xs font-semibold"
              :style="{ backgroundColor: invoice.department.color + '20', color: invoice.department.color }"
            >
              <font-awesome-icon :icon="invoice.department.icon" class="text-xs" />
              {{ invoice.department.name }}
            </span>
          </div>
        </div>

        <!-- Task badge -->
        <div v-if="invoice.tasks && invoice.tasks.length > 0" class="mx-3 flex-shrink-0">
          <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
            <font-awesome-icon icon="fa-solid fa-check-circle" class="text-xs" />
            Tarefa
          </span>
        </div>

        <!-- Date + Status -->
        <div class="flex flex-col items-end gap-1 flex-shrink-0 ml-3">
          <span
            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
            :class="getStatusClass(invoice.status)"
          >
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

    <!-- Create Form -->
    <StandaloneDebitInvoiceCreateForm
      v-model="showCreateForm"
      @invoice-created="handleInvoiceCreated"
    />
  </div>
</template>

<script>
import { BACKEND_URL } from "@/config/apiConfig";
import axios from "axios";
import { formatDateBr } from "@/utils/date/dateUtils";
import StandaloneDebitInvoiceCreateForm from "@/components/forms/StandaloneDebitInvoiceCreateForm.vue";

export default {
  name: "AccountsPayableIndex",
  components: {
    StandaloneDebitInvoiceCreateForm,
  },
  data() {
    return {
      invoices: [],
      departments: [],
      isLoading: false,
      activeFilter: "all",
      activeDepartment: null,
      searchTerm: "",
      showCreateForm: false,
    };
  },
  computed: {
    filterOptions() {
      return [
        {
          value: "all",
          label: "Todas",
          icon: "fa-solid fa-list",
          activeClass: "bg-gray-800 text-white",
          inactiveClass: "bg-white border border-gray-300 text-gray-700 hover:border-gray-400",
          count: this.invoices.length,
        },
        {
          value: "overdue",
          label: "Vencidas",
          icon: "fa-solid fa-exclamation-circle",
          activeClass: "bg-orange-600 text-white",
          inactiveClass: "bg-orange-50 border border-orange-300 text-orange-700 hover:bg-orange-100",
          count: this.invoices.filter((i) => i.status === "overdue").length,
        },
        {
          value: "upcoming_7",
          label: "A Vencer 7d",
          icon: "fa-solid fa-clock",
          activeClass: "bg-yellow-600 text-white",
          inactiveClass: "bg-yellow-50 border border-yellow-300 text-yellow-700 hover:bg-yellow-100",
        },
        {
          value: "upcoming_30",
          label: "A Vencer 30d",
          icon: "fa-solid fa-calendar",
          activeClass: "bg-blue-600 text-white",
          inactiveClass: "bg-blue-50 border border-blue-300 text-blue-700 hover:bg-blue-100",
        },
        {
          value: "paid",
          label: "Pagas",
          icon: "fa-solid fa-check-circle",
          activeClass: "bg-green-600 text-white",
          inactiveClass: "bg-green-50 border border-green-300 text-green-700 hover:bg-green-100",
        },
      ];
    },
    filteredInvoices() {
      let list = this.invoices;

      if (this.activeFilter === "overdue") {
        list = list.filter((i) => i.status === "overdue");
      } else if (this.activeFilter === "paid") {
        list = list.filter((i) => i.status === "paid");
      } else if (this.activeFilter === "upcoming_7") {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const plus7 = new Date(today);
        plus7.setDate(plus7.getDate() + 7);
        list = list.filter((i) => {
          const due = new Date(i.date_due);
          return due >= today && due <= plus7 && !["paid", "cancelled"].includes(i.status);
        });
      } else if (this.activeFilter === "upcoming_30") {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const plus30 = new Date(today);
        plus30.setDate(plus30.getDate() + 30);
        list = list.filter((i) => {
          const due = new Date(i.date_due);
          return due >= today && due <= plus30 && !["paid", "cancelled"].includes(i.status);
        });
      }

      if (this.searchTerm.trim()) {
        const term = this.searchTerm.toLowerCase();
        list = list.filter((i) => {
          const name = (i.name || "fatura #" + i.id).toLowerCase();
          const supplier = this.getSupplierName(i).toLowerCase();
          return name.includes(term) || supplier.includes(term);
        });
      }

      if (this.activeDepartment !== null) {
        list = list.filter((i) => i.department_id === this.activeDepartment);
      }

      return list;
    },
    summaries() {
      const now = new Date();
      now.setHours(0, 0, 0, 0);
      const plus30 = new Date(now);
      plus30.setDate(plus30.getDate() + 30);
      const thisMonthStart = new Date(now.getFullYear(), now.getMonth(), 1);
      const thisMonthEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0);

      const pending = this.invoices.filter((i) => ["pending", "partial"].includes(i.status));
      const overdue = this.invoices.filter((i) => i.status === "overdue");
      const upcoming = this.invoices.filter((i) => {
        const due = new Date(i.date_due);
        return due >= now && due <= plus30 && !["paid", "cancelled"].includes(i.status);
      });
      const paidThisMonth = this.invoices.filter((i) => {
        if (i.status !== "paid") return false;
        const updated = new Date(i.updated_at);
        return updated >= thisMonthStart && updated <= thisMonthEnd;
      });

      return {
        totalPending: pending.reduce((s, i) => s + (Number(i.balance) || 0), 0),
        countPending: pending.length,
        totalOverdue: overdue.reduce((s, i) => s + (Number(i.balance) || 0), 0),
        countOverdue: overdue.length,
        totalUpcoming: upcoming.reduce((s, i) => s + (Number(i.balance) || 0), 0),
        countUpcoming: upcoming.length,
        totalPaidThisMonth: paidThisMonth.reduce((s, i) => s + (Number(i.price) || 0), 0),
        countPaidThisMonth: paidThisMonth.length,
      };
    },
  },
  methods: {
    async fetchInvoices() {
      this.isLoading = true;
      try {
        const response = await axios.get(`${BACKEND_URL}invoices?type=debit&per_page=500`);
        const data = response.data?.data || response.data || [];
        this.invoices = data.filter((i) => i.type === "debit");
      } catch (e) {
        console.error("Erro ao carregar contas a pagar:", e);
      } finally {
        this.isLoading = false;
      }
    },
    async fetchDepartments() {
      try {
        const response = await axios.get(`${BACKEND_URL}departments`);
        const data = response.data?.data || response.data || [];
        this.departments = (Array.isArray(data) ? data : []).filter((d) => d.active);
      } catch (e) {
        // silently fail
      }
    },
    setFilter(value) {
      this.activeFilter = value;
    },
    handleInvoiceCreated(newInvoices) {
      const arr = Array.isArray(newInvoices) ? newInvoices : [newInvoices];
      this.invoices.unshift(...arr);
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
    formatCurrency(value) {
      return new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(value || 0);
    },
    formatDateBr,
  },
  mounted() {
    this.fetchInvoices();
    this.fetchDepartments();
  },
};
</script>
