<template>
  <div>
    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <BankAccountsList 
      @error="handleError"
      @success="handleSuccess"
    />
  </div>
</template>

<script>
import BankAccountsList from "@/components/lists/BankAccountsList.vue";
import SuccessMessage from "@/components/forms/messages/SuccessMessage.vue";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";

export default {
  name: "BankAccountsIndexView",
  components: {
    BankAccountsList,
    SuccessMessage,
    ErrorMessage,
  },
  data() {
    return {
      isError: false,
      isSuccess: false,
      formResponse: null,
    };
  },
  methods: {
    handleError(response) {
      this.isError = true;
      this.isSuccess = false;
      this.formResponse = response;
      setTimeout(() => {
        this.isError = false;
        this.formResponse = null;
      }, 5000);
    },
    handleSuccess(response) {
      this.isSuccess = true;
      this.isError = false;
      this.formResponse = response;
      setTimeout(() => {
        this.isSuccess = false;
        this.formResponse = null;
      }, 5000);
    },
  },
};
</script>

<style scoped>
/* Add any specific styles for the index view if needed */
</style>
