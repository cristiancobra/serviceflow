<template>
  <div class="flex flex-wrap justify-center gap-4 mt-10 mb-12 pt-10">
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-primary"
      :class="activeFilter === null ? 'bg-primary text-white' : 'bg-base-100 text-primary hover:bg-primary hover:text-white'"
      @click="handleFilterClick(null)"
    >
      Todas
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-info"
      :class="activeFilter === 'open' ? 'bg-info text-white' : 'bg-base-100 text-info hover:bg-info hover:text-white'"
      @click="handleFilterClick('open')"
    >
      Abertas
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-warning"
      :class="activeFilter === 'today' ? 'bg-warning text-white' : 'bg-base-100 text-warning hover:bg-warning hover:text-white'"
      @click="handleFilterClick('today')"
    >
      Hoje
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-error"
      :class="activeFilter === 'week' ? 'bg-error text-white' : 'bg-base-100 text-error hover:bg-error hover:text-white'"
      @click="handleFilterClick('week')"
    >
      Esta Semana
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-base-content/20"
      :class="activeFilter === 'month' ? 'bg-gray-500 text-white' : 'bg-base-100 text-base-content/60 hover:bg-gray-500 hover:text-white'"
      @click="handleFilterClick('month')"
    >
      Este Mês
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-success"
      :class="activeFilter === 'closed' ? 'bg-success text-white' : 'bg-base-100 text-success hover:bg-success hover:text-white'"
      @click="handleFilterClick('closed')"
    >
      Fechadas
    </button>
  </div>
</template>

<script>
export default {
  name: "JourneysFilter",
  data() {
    return {
      activeFilter: null,
    };
  },
  methods: {
    handleFilterClick(filter) {
      this.activeFilter = filter;
      this.$emit("filter-change", this.activeFilter);
    },
  },
};
</script>

<style scoped>
/* Não precisa mais de CSS customizado, tudo em Tailwind! */
</style>
