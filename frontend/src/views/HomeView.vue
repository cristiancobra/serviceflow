<template>
  <div class="home">
    <p style="margin-top: 120px">
      Bem vindo, hoje é
      <span style="font-weight: bolder">
        {{ dateNow }}
      </span>
    </p>
    <p>
    </p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      dateNow: "", // Inicializando a propriedade dateNow
    };
  },
  mounted() {
    this.getDateNow();
  },
  methods: {
    getDateNow() {
      const data = new Date();
      const options = { year: "numeric", month: "long", day: "numeric" };
      this.dateNow = data.toLocaleDateString("pt-BR", options);
    },
  },
};
</script>

