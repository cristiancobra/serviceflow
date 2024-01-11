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
  // methods: {
  //   async CheckLogin() {
  //     console.log(window.location.href);
  //     if (localStorage.getItem("access_token")) {
  //       this.isLogged = true;
  //       this.$router.push({ name: "home" });
  //     } else {
  //       this.isLogged = false;
  //       this.$router.push({ name: "login" });
  //     }
  //   },
  // },
  // watch: {
  //   isLogged: function () {
  //     console.log(this.isLogged);
  //     this.CheckLogin();
  //   },
  // },
};
</script>