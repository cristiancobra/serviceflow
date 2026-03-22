<template>
  <div>
    <TransactionsFilter @filter-change="handleFilterChange" />
    
    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <TransactionsList :filterType="selectedType" />
  </div>
</template>

<script>
import TransactionsList from "@/components/lists/TransactionsList.vue";
import TransactionsFilter from "@/components/filters/TransactionsFilter.vue";
import SuccessMessage from "../../components/forms/messages/SuccessMessage.vue";
import ErrorMessage from "../../components/forms/messages/ErrorMessage.vue";

export default {
  name: "TransactionsIndexView",
  components: {
    TransactionsList,
    TransactionsFilter,
    SuccessMessage,
    ErrorMessage,
  },
  data() {
    return {
      isError: false,
      isSuccess: false,
      hasError: false,
      formResponse: null,
      selectedType: null,
    };
  },
  methods: {
    handleFilterChange(type) {
      this.selectedType = type;
    },
  },
};
</script>

<style scoped>
/* Removido CSS do Bootstrap, usando apenas Tailwind */
</style>