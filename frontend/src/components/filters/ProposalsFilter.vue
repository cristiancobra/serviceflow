<template>
  <div class="flex flex-wrap justify-center gap-4 mt-10 mb-12">
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-orange-500"
      :class="activeFilter === 'pending' ? 'bg-orange-500 text-white' : 'bg-white text-orange-500 hover:bg-orange-500 hover:text-white'"
      @click="handleFilterClick('pending')"
    >
      Pendentes
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-success"
      :class="activeFilter === 'accepted' ? 'bg-success text-white' : 'bg-white text-success hover:bg-success hover:text-white'"
      @click="handleFilterClick('accepted')"
    >
      Aceitas
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-red-500"
      :class="activeFilter === 'rejected' ? 'bg-red-500 text-white' : 'bg-white text-red-500 hover:bg-red-500 hover:text-white'"
      @click="handleFilterClick('rejected')"
    >
      Rejeitadas
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-gray-500"
      :class="activeFilter === 'draft' ? 'bg-gray-500 text-white' : 'bg-white text-gray-500 hover:bg-gray-500 hover:text-white'"
      @click="handleFilterClick('draft')"
    >
      Rascunhos
    </button>
  </div>
</template>

<script>
export default {
  name: "ProposalsFilter",
  data() {
    return {
      activeFilter: null,
    };
  },
  methods: {
    handleFilterClick(status) {
      // Toggle: se clicar no filtro ativo, desativa
      this.activeFilter = this.activeFilter === status ? null : status;
      // Emite um único evento com o valor do filtro (ou null se desativado)
      this.$emit("filter-change", this.activeFilter);
    },
  },
};
</script>

<style scoped>
/* Não precisa mais de CSS customizado, tudo em Tailwind! */
</style>
