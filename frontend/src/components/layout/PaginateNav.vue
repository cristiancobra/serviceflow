<template>
  <nav
    v-if="dataLoaded && (links.prev || links.next)"
    class="flex justify-end gap-2"
    aria-label="Pagination navigation"
  >
    <button
      type="button"
      class="btn btn-primary btn-sm"
      :disabled="!links.prev"
      @click="fetchData(links.prev)"
    >
      Anterior
    </button>
    <button
      type="button"
      class="btn btn-primary btn-sm"
      :disabled="!links.next"
      @click="fetchData(links.next)"
    >
      Próximo
    </button>
  </nav>
</template>
  
<script>
import axios from "axios";

export default {
  name: "PaginateNav",
  props: ["paginationData"],
  data() {
    return {
      items: [],
      dataLoaded: false,
      links: {
        prev: null,
        next: null,
      },
    };
  },
  methods: {
    async fetchData(url) {
      if (this.paginationData && this.paginationData.links && this.links) {
        try {
          const response = await axios.get(url);

          this.items = response.data.data;
          this.links = response.data.links;

          this.$emit("update-data", this.items);

          // Emitir os dados combinados atualizados para o componente pai
          // this.$emit("pagination-data-updated", {
          //   links: response.data.links,
          //   meta: response.data.meta,
          // });
        } catch (error) {
          console.error("Erro ao acessar jornadas:", error);
        }
      }
    },
  },

  watch: {
    paginationData: {
      handler(newVal) {
        if (newVal && newVal.links) {
          this.links = newVal.links;
          this.dataLoaded = true;
        }
      },
      immediate: true,
    },
  },
};
</script>
  