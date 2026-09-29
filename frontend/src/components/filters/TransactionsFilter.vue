<template>
  <div class="flex flex-wrap justify-center gap-4 mt-10 mb-12">
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-success"
      :class="activeFilter === 'credit' ? 'bg-success text-white' : 'bg-base-100 text-success hover:bg-success hover:text-white'"
      @click="handleFilterClick('credit')"
    >
      <font-awesome-icon icon="fa-solid fa-arrow-up" class="mr-2" />
      Entradas
    </button>

    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-error"
      :class="activeFilter === 'debit' ? 'bg-error text-white' : 'bg-base-100 text-error hover:bg-error hover:text-white'"
      @click="handleFilterClick('debit')"
    >
      <font-awesome-icon icon="fa-solid fa-arrow-down" class="mr-2" />
      Saídas
    </button>
  </div>
</template>

<script>
export default {
  name: "TransactionsFilter",
  data() {
    return {
      activeFilter: null,
    };
  },
  methods: {
    handleFilterClick(type) {
      // Toggle: se clicar no filtro ativo, desativa
      this.activeFilter = this.activeFilter === type ? null : type;
      // Emite um único evento com o valor do filtro (ou null se desativado)
      this.$emit("filter-change", this.activeFilter);
    },
  },
};
</script>

<style scoped>
/* Usando Tailwind CSS */
</style>
