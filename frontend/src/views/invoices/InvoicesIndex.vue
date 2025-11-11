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