<template>
  <LoginForm />
</template>


<script>
import LoginForm from "@/components/forms/LoginForm.vue";

export default {
  data() {
    return {
      // isLogged: false,
    };
  },
  components: {
    LoginForm,
  },
};
</script>