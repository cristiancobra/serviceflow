<template>
  <button class="" type="button" @click="logout">
    <font-awesome-icon icon="fas fa-sign-out" />
    Logout
  </button>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, LOGOUT_URL } from "@/config/apiConfig";

export default {
  data() {
    return {};
  },
  methods: {
    async logout() {
      try {
        axios.defaults.withCredentials = true;

        await axios.post(`${BACKEND_URL}${LOGOUT_URL}`);

        localStorage.removeItem("access_token");

        this.$router.push({ name: "login" });

        // this.$root.isLogged = false;
      } catch (error) {
        console.error("Erro de logout:", error);
      }
    },
  },
};
</script>
