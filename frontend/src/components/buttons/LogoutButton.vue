<template>
  <div class="w-25">
    <button
      class="button-new"
      type="button"
      @click="logout"
    >
      Logout
    </button>
  </div>
</template>

<script>
import axios from "axios";
import { API_BASE_URL, LOGOUT_URL } from "@/config/apiConfig";

export default {
  data() {
    return {
    
    };
  },
  methods: {
    async logout() {
      try {
        
        axios.defaults.withCredentials = true;

        await axios.post(`${API_BASE_URL}${LOGOUT_URL}`);

        localStorage.removeItem('access_token');

        this.$router.push({ name: "login" });

        // this.$root.isLogged = false;

      } catch (error) {
        console.error("Erro de logout:", error);
      }
    },
  },
};
</script>
