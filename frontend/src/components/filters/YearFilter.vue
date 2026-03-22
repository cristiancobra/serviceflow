<template>
  <div class="flex flex-wrap justify-center gap-4 mt-10 mb-12">
    <button
      v-for="year in years"
      :key="year"
      class="px-6 py-2 border-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 border-blue-500"
      :class="activeYear === year ? 'bg-blue-500 text-white' : 'bg-white text-blue-500 hover:bg-blue-500 hover:text-white'"
      @click="handleYearClick(year)"
    >
      {{ year }}
    </button>
  </div>
</template>

<script>
export default {
  name: "YearFilter",
  data() {
    const currentYear = new Date().getFullYear();
    return {
      activeYear: currentYear,
      years: this.generateYears(currentYear),
    };
  },
  methods: {
    generateYears(currentYear) {
      // Gera os últimos 5 anos e os próximos 2 anos
      const years = [];
      for (let i = currentYear - 5; i <= currentYear + 2; i++) {
        years.push(i);
      }
      return years;
    },
    handleYearClick(year) {
      this.activeYear = year;
      this.$emit("year-change", this.activeYear);
    },
  },
  mounted() {
    // Emite o ano atual ao montar o componente
    this.$emit("year-change", this.activeYear);
  },
};
</script>

<style scoped>
/* Usando Tailwind CSS */
</style>
