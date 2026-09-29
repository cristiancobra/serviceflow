<template>
  <div class="flex flex-wrap justify-center gap-4 mt-10 mb-12">
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-warning"
      :class="activeFilter === 'pending' ? 'bg-warning text-white' : 'bg-base-100 text-warning hover:bg-warning hover:text-white'"
      @click="handleFilterClick('pending')"
    >
      Pendentes
    </button>

    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-purple-500"
      :class="activeFilter === 'submitted' ? 'bg-purple-500 text-white' : 'bg-base-100 text-purple-500 hover:bg-purple-500 hover:text-white'"
      @click="handleFilterClick('submitted')"
    >
      Enviadas
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-success"
      :class="activeFilter === 'accepted' ? 'bg-success text-white' : 'bg-base-100 text-success hover:bg-success hover:text-white'"
      @click="handleFilterClick('accepted')"
    >
      Aceitas
    </button>

    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-success"
      :class="activeFilter === 'paid' ? 'bg-success text-white' : 'bg-base-100 text-success hover:bg-success hover:text-white'"
      @click="handleFilterClick('paid')"
    >
      Pagas
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-error"
      :class="activeFilter === 'rejected' ? 'bg-error text-white' : 'bg-base-100 text-error hover:bg-error hover:text-white'"
      @click="handleFilterClick('rejected')"
    >
      Rejeitadas
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-base-content/20"
      :class="activeFilter === 'draft' ? 'bg-gray-500 text-white' : 'bg-base-100 text-base-content/60 hover:bg-gray-500 hover:text-white'"
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
