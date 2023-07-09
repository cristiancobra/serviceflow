<template>
  <div class="home">
    <img :src="require('@/assets/logo-serviceflow-ROXO.png')" alt="logo-serviceflow">
    <p style="margin-top: 120px;">
      Bem vindo, hoje é
      <span style="font-weight: bolder;">
        {{ dateNow }}
      </span>
    </p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      dateNow: '' // Inicializando a propriedade dateNow
    };
  },
  mounted() {
    this.getDateNow();
  },
  methods: {
    getDateNow() {
      const data = new Date();
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      this.dateNow = data.toLocaleDateString('pt-BR', options);
    }
  }
};
</script>

