<template>
  <div class="app-container">
    <NavbarUser v-if="showNavbar" />
    <div class="main">
      <router-view />
    </div>
  </div>
</template>


<script>
import { mapActions } from 'vuex';
import NavbarUser from "./components/layout/NavbarUser.vue";

export default {
  data() {
    return {
      showNavbar: null,
    };
  },
  components: {
    NavbarUser,
  },
  methods: {
    ...mapActions(['checkAuthentication']),
    async startAuthCheck() {
      this.checkAuthentication();
      setInterval(async () => {
        await this.checkAuthentication();
      }, 60000); // Verifica a cada 60 segundos
    }
  },
  created() {
    this.showNavbar = this.$route.name !== 'login'; // Inicializa a condição da navbar
    this.startAuthCheck(); // Inicia a verificação periódica de autenticação
  },
  watch: {
    $route(to) {
      this.showNavbar = to.name !== 'login';
    }
  },
};
</script>

<style>
.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.router-view {
  flex: 1;
}
</style>