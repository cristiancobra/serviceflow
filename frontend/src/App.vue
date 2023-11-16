<template>
  <LoginForm v-if="!isLogged" />
  <NavbarUser />
</template>


<script>
import LoginForm from "./components/forms/LoginForm.vue";
// import NavbarGuest from "./components/layout/NavbarGuest.vue";
import NavbarUser from "./components/layout/NavbarUser.vue";

export default {
  data() {
    return {
      isLogged: false,
    };
  },
  components: {
    NavbarUser,
    LoginForm,
  },
  methods: {
    async CheckLogin() {
      if (localStorage.getItem("access_token")) {
        this.isLogged = true;
      } else {
        this.isLogged = false;
      }
    },
  },
  watch: {
    isLogged: function () {
      console.log(this.isLogged);
      this.CheckLogin();
    },
  },
};
</script>