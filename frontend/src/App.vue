<template>
  <div>
  <NavbarUser v-if="showNavbar"/>
  <router-view />

  </div>
</template>


<script>
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
  watch: {
    // isLogged: function () {
    //   console.log(this.isLogged);
    //   // this.CheckLogin();
    // },
    $route(to) {
      // Atualize a condição para mostrar ou ocultar a barra de navegação com base na rota atual
      this.showNavbar = to.name !== 'login';
    }
  },
};
</script>