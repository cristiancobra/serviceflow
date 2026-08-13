<template>
  <div class="page-container">
    <!-- Header -->
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-file-invoice-dollar" class="page-icon text-red-600" />
        <h1>CONTAS A PAGAR</h1>
      </div>
      <button @click="showCreateForm = true"
        class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold text-sm">
        <font-awesome-icon icon="fa-solid fa-plus" />
        Nova Conta a Pagar
      </button>
    </div>

    <!-- Summary Cards -->
    <section class="section-container">
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
    </section>

    <!-- Filters -->
    <section class="section-container">
      <div class="flex flex-wrap gap-2 mb-3">
        <button v-for="f in filterOptions" :key="f.value" @click="setFilter(f.value)" :class="[
          'px-4 py-2 rounded-lg text-sm font-semibold transition-colors',
          activeFilter === f.value ? f.activeClass : f.inactiveClass,
        ]">
          <font-awesome-icon :icon="f.icon" class="mr-1" />
          {{ f.label }}
          <span v-if="f.count !== undefined" class="ml-1 text-xs opacity-75">({{ f.count }})</span>
        </button>
      </div>

      <!-- Department Filter -->
      <div v-if="departments.length > 0" class="flex flex-wrap gap-2 mb-4">
        <button @click="activeDepartment = null" :class="[
          'px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors border',
          activeDepartment === null
            ? 'bg-gray-800 text-white border-gray-800'
            : 'bg-white text-gray-600 border-gray-300 hover:border-gray-400',
        ]">
          Todos os departamentos
        </button>
        <button v-for="dept in departments" :key="dept.id" @click="activeDepartment = dept.id" :class="[
          'px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors border',
          activeDepartment === dept.id ? 'text-white border-transparent' : 'bg-white border-gray-300',
        ]" :style="activeDepartment === dept.id
          ? { backgroundColor: dept.color, borderColor: dept.color }
          : { color: dept.color }">
          <font-awesome-icon :icon="dept.icon" class="mr-1" />
          {{ dept.name }}
        </button>
      </div>
    </section>

    <!-- Search -->
    <section class="section-container">
      <div class="relative mb-4">
        <font-awesome-icon icon="fa-solid fa-search"
          class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm" />
        <input v-model="searchTerm" type="text" placeholder="Buscar por nome, fornecedor..."
          class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm" />
      </div>
    </section>

    <!-- Loading -->
    <section class="section-container">
      <div v-if="isLoading" class="flex items-center justify-center py-16">
        <font-awesome-icon icon="fa-solid fa-spinner" class="animate-spin text-3xl text-red-500" />
      </div>

      <!-- Invoice List grouped by month -->
      <AccountsPayableList v-if="!isLoading" :invoices="filteredInvoices" />
    </section>

    <!-- Create Form -->
    <StandaloneDebitInvoiceCreateForm v-model="showCreateForm" @invoice-created="handleInvoiceCreated" />
  </div>
</template>

<script>
import { BACKEND_URL } from "@/config/apiConfig";
import axios from "axios";
import StandaloneDebitInvoiceCreateForm from "@/components/forms/StandaloneDebitInvoiceCreateForm.vue";
import AccountsPayableList from "@/components/lists/AccountsPayableList.vue";

export default {
  name: "AccountsPayableIndex",
  components: {
    StandaloneDebitInvoiceCreateForm,
    AccountsPayableList,
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
    formatCurrency(value) {
      return new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(value || 0);
    },
  },
  mounted() {
    this.fetchInvoices();
    this.fetchDepartments();
  },
};
</script>
