<template>
  <div class="page-container">
    <!-- Header -->
    <PageHeader
      title="CONTAS A PAGAR"
      icon="fa-solid fa-file-invoice-dollar"
      icon-class="text-error"
    >
      <template #actions>
        <button type="button" class="btn bg-base-100 text-error border-0 hover:bg-base-200" @click="openCreateInvoiceModal">
          <font-awesome-icon icon="fa-solid fa-plus" />
          Nova Conta a Pagar
        </button>
      </template>
    </PageHeader>

    <!-- Summary Cards -->
    <section class="px-8 mt-4 mb-6">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl border border-error/30 bg-error/10 p-4 shadow-sm">
          <div class="text-xs font-semibold text-error uppercase tracking-wide">Total Pendente</div>
          <div class="mt-1 text-xl font-bold text-error">
            {{ formatCurrency(summaries.totalPending) }}
          </div>
          <div class="text-xs text-error mt-0.5">{{ summaries.countPending }} faturas</div>
        </div>
        <div class="rounded-xl border border-warning/30 bg-warning/10 p-4 shadow-sm">
          <div class="text-xs font-semibold text-warning uppercase tracking-wide">Vencidas</div>
          <div class="mt-1 text-xl font-bold text-warning">
            {{ formatCurrency(summaries.totalOverdue) }}
          </div>
          <div class="text-xs text-warning mt-0.5">{{ summaries.countOverdue }} faturas</div>
        </div>
        <div class="rounded-xl border border-warning/30 bg-warning/10 p-4 shadow-sm">
          <div class="text-xs font-semibold text-warning uppercase tracking-wide">A Vencer em 30d</div>
          <div class="mt-1 text-xl font-bold text-warning">
            {{ formatCurrency(summaries.totalUpcoming) }}
          </div>
          <div class="text-xs text-warning mt-0.5">{{ summaries.countUpcoming }} faturas</div>
        </div>
        <div class="rounded-xl border border-success/30 bg-success/10 p-4 shadow-sm">
          <div class="text-xs font-semibold text-success uppercase tracking-wide">Pagas este Mês</div>
          <div class="mt-1 text-xl font-bold text-success">
            {{ formatCurrency(summaries.totalPaidThisMonth) }}
          </div>
          <div class="text-xs text-success mt-0.5">{{ summaries.countPaidThisMonth }} faturas</div>
        </div>
      </div>
    </section>

    <!-- Filters -->
    <section class="px-8 mb-6">
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
            : 'bg-base-100 text-base-content/70 border-base-300 hover:border-base-content/20',
        ]">
          Todos os departamentos
        </button>
        <button v-for="dept in departments" :key="dept.id" @click="activeDepartment = dept.id" :class="[
          'px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors border',
          activeDepartment === dept.id ? 'text-white border-transparent' : 'bg-base-100 border-base-300',
        ]" :style="activeDepartment === dept.id
          ? { backgroundColor: dept.color, borderColor: dept.color }
          : { color: dept.color }">
          <font-awesome-icon :icon="dept.icon" class="mr-1" />
          {{ dept.name }}
        </button>
      </div>
    </section>

    <!-- Search -->
    <section class="px-8 mb-6">
      <div class="relative">
        <font-awesome-icon icon="fa-solid fa-search"
          class="absolute left-3 top-1/2 -translate-y-1/2 text-base-content/50 text-sm" />
        <input v-model="searchTerm" type="text" placeholder="Buscar por nome, fornecedor..."
          class="w-full pl-9 pr-4 py-2 border border-base-300 rounded-lg focus:ring-2 focus:ring-error focus:border-transparent text-sm" />
      </div>
    </section>

    <!-- Loading -->
    <section class="px-8 mb-20">
      <div v-if="isLoading" class="flex items-center justify-center py-16">
        <font-awesome-icon icon="fa-solid fa-spinner" class="animate-spin text-3xl text-error" />
      </div>

      <!-- Invoice List grouped by month -->
      <AccountsPayableList
        v-if="!isLoading"
        :invoices="filteredInvoices"
        :selected-ids="selectedIds"
        @toggle-select="toggleSelect"
        @invoice-updated="replaceInvoice"
        @invoice-deleted="removeInvoice" />
    </section>

    <!-- Barra de pagamento em lote -->
    <div v-if="selectedInvoices.length > 0"
      class="fixed bottom-6 left-1/2 -translate-x-1/2 z-30 flex items-center gap-4 px-5 py-3 rounded-xl shadow-2xl bg-gray-900 text-white">
      <span class="text-sm">
        <strong>{{ selectedInvoices.length }}</strong>
        {{ selectedInvoices.length === 1 ? 'conta selecionada' : 'contas selecionadas' }}
        • <strong>{{ formatCurrency(selectedTotal) }}</strong>
      </span>
      <button @click="selectedIds = []"
        class="px-3 py-1.5 rounded-lg text-sm font-semibold text-base-content/30 hover:text-white hover:bg-gray-700 transition-colors">
        Limpar
      </button>
      <button @click="openBatchPaymentModal" :disabled="selectedInvoices.length < 2"
        :title="selectedInvoices.length < 2 ? 'Selecione pelo menos duas contas' : ''"
        class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-sm font-semibold bg-error hover:bg-error transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
        <font-awesome-icon icon="fa-solid fa-layer-group" />
        Pagar em lote
      </button>
    </div>
  </div>
</template>

<script>
import { mapMutations } from "vuex";
import { BACKEND_URL } from "@/config/apiConfig";
import axios from "axios";
import AccountsPayableList from "@/components/lists/AccountsPayableList.vue";
import PageHeader from "@/components/layout/PageHeader.vue";

export default {
  name: "AccountsPayableIndex",
  components: {
    PageHeader,
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
      // Invoices marcadas para pagar juntas em uma única movimentação
      selectedIds: [],
    };
  },
  computed: {
    selectedInvoices() {
      return this.invoices.filter((i) => this.selectedIds.includes(i.id));
    },
    selectedTotal() {
      return this.selectedInvoices.reduce((s, i) => s + (Number(i.balance) || 0), 0);
    },
    filterOptions() {
      return [
        {
          value: "all",
          label: "Todas",
          icon: "fa-solid fa-list",
          activeClass: "bg-gray-800 text-white",
          inactiveClass: "bg-base-100 border border-base-300 text-base-content/80 hover:border-base-content/20",
          count: this.invoices.length,
        },
        {
          value: "overdue",
          label: "Vencidas",
          icon: "fa-solid fa-exclamation-circle",
          activeClass: "bg-warning text-white",
          inactiveClass: "bg-warning/10 border border-warning/30 text-warning hover:bg-warning/10",
          count: this.invoices.filter((i) => i.status === "overdue").length,
        },
        {
          value: "upcoming_7",
          label: "A Vencer 7d",
          icon: "fa-solid fa-clock",
          activeClass: "bg-warning text-white",
          inactiveClass: "bg-warning/10 border border-warning/30 text-warning hover:bg-warning/10",
        },
        {
          value: "upcoming_30",
          label: "A Vencer 30d",
          icon: "fa-solid fa-calendar",
          activeClass: "bg-info text-white",
          inactiveClass: "bg-info/10 border border-info/30 text-info hover:bg-info/10",
        },
        {
          value: "paid",
          label: "Pagas",
          icon: "fa-solid fa-check-circle",
          activeClass: "bg-success text-white",
          inactiveClass: "bg-success/10 border border-success/30 text-success hover:bg-success/10",
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
    ...mapMutations(["openModal"]),
    openCreateInvoiceModal() {
      this.openModal({
        component: "StandaloneDebitInvoiceCreateForm",
        listeners: {
          "invoice-created": this.handleInvoiceCreated,
        },
      });
    },
    toggleSelect(invoice) {
      const index = this.selectedIds.indexOf(invoice.id);
      if (index === -1) {
        this.selectedIds.push(invoice.id);
      } else {
        this.selectedIds.splice(index, 1);
      }
    },
    openBatchPaymentModal() {
      this.openModal({
        component: "BatchPaymentForm",
        props: { invoices: this.selectedInvoices },
        listeners: {
          "batch-paid": this.handleBatchPaid,
        },
      });
    },
    replaceInvoice(updated) {
      const index = this.invoices.findIndex((inv) => inv.id === updated.id);
      if (index !== -1) this.invoices.splice(index, 1, updated);
    },
    removeInvoice(id) {
      this.invoices = this.invoices.filter((inv) => inv.id !== id);
      this.selectedIds = this.selectedIds.filter((selectedId) => selectedId !== id);
    },
    handleBatchPaid() {
      this.selectedIds = [];
      this.fetchInvoices();
    },
    async fetchInvoices() {
      this.isLoading = true;
      try {
        let page = 1;
        let lastPage = 1;
        const all = [];

        do {
          const response = await axios.get(`${BACKEND_URL}invoices`, {
            params: { type: "debit", per_page: 500, page },
          });
          all.push(...(response.data?.data || []));
          lastPage = response.data?.meta?.last_page || 1;
          page++;
        } while (page <= lastPage);

        this.invoices = all;
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
