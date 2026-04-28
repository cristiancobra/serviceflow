<template>
  <div class="flex flex-wrap justify-center gap-4 mt-10 mb-12 pt-10">
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-primary"
      :class="activeFilter === null ? 'bg-primary text-white' : 'bg-white text-primary hover:bg-primary hover:text-white'"
      @click="handleFilterClick(null)"
    >
      Todas
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-blue-500"
      :class="activeFilter === 'open' ? 'bg-blue-500 text-white' : 'bg-white text-blue-500 hover:bg-blue-500 hover:text-white'"
      @click="handleFilterClick('open')"
    >
      Abertas
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-orange-500"
      :class="activeFilter === 'today' ? 'bg-orange-500 text-white' : 'bg-white text-orange-500 hover:bg-orange-500 hover:text-white'"
      @click="handleFilterClick('today')"
    >
      Hoje
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-red-500"
      :class="activeFilter === 'week' ? 'bg-red-500 text-white' : 'bg-white text-red-500 hover:bg-red-500 hover:text-white'"
      @click="handleFilterClick('week')"
    >
      Esta Semana
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-gray-500"
      :class="activeFilter === 'month' ? 'bg-gray-500 text-white' : 'bg-white text-gray-500 hover:bg-gray-500 hover:text-white'"
      @click="handleFilterClick('month')"
    >
      Este Mês
    </button>
    
    <button
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-success"
      :class="activeFilter === 'closed' ? 'bg-success text-white' : 'bg-white text-success hover:bg-success hover:text-white'"
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
