<template>
  <div>
    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <CreditCardsList
      @error="handleError"
      @success="handleSuccess"
    />
  </div>
</template>

<script>
import CreditCardsList from "@/components/lists/CreditCardsList.vue";
import SuccessMessage from "@/components/forms/messages/SuccessMessage.vue";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";

export default {
  name: "CreditCardsIndexView",
  components: {
    CreditCardsList,
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
