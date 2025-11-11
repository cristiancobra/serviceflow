<template>
  <div>
    <InvoicesFilter
      @filter-pending="getInvoicesPending"
      @filter-paid="getInvoicesPaid"
      @filter-overdue="getInvoicesOverdue"
      @filter-canceled="getInvoicesCanceled"
      @filter-all="getInvoicesAll"
    />

    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <invoices-list template="index" />
  </div>
</template>

<script>
import InvoicesList from "@/components/lists/InvoicesList.vue";
import InvoicesFilter from "@/components/filters/InvoicesFilter.vue";
import SuccessMessage from "@/components/forms/messages/SuccessMessage.vue";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";

export default {
  name: "InvoicesIndexView",
  components: {
    InvoicesList,
    InvoicesFilter,
    SuccessMessage,
    ErrorMessage,
  },
  data() {
    return {
      isError: false,
      isSuccess: false,
      hasError: false,
      data: null,
      invoices: [],
      filteredInvoices: [],
      newInvoice: null,
      formResponse: null,
    };
  },
  methods: {
    getInvoicesPending() {
      this.$emit('filter-changed', 'pending');
    },
    getInvoicesPaid() {
      this.$emit('filter-changed', 'paid');
    },
    getInvoicesOverdue() {
      this.$emit('filter-changed', 'overdue');
    },
    getInvoicesCanceled() {
      this.$emit('filter-changed', 'canceled');
    },
    getInvoicesAll() {
      this.$emit('filter-changed', 'all');
    },
  },
};
</script>

<style scoped>
.headers-line {
  margin-top: 40px;
  margin-bottom: 50px;
  margin-left: 80px;
  margin-right: 80px;
  display: flex;
  justify-content: center;
}

.slot {
  border-width: 2px;
  border-style: solid;
  border-color: white;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  margin: 0 4px 0 4px;
  color: white;
  font-weight: 800;
  width: 120px;
}

.new {
  border-radius: 20px 20px 20px 20px;
  background-color: white;
  border-color: #ff3eb5;
  color: #ff3eb5;
  margin-left: 50px;
  width: 60px;
  font-size: 16px;
}

.new:hover {
  background-color: #ff3eb5;
  color: white;
  margin-left: 50px;
  width: 60px;
}

.show {
  display: block;
  transition: display 2s;
}
</style>