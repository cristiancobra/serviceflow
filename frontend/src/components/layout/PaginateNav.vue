<template>
  <div v-if="dataLoaded" class="row">
    <div class="col-12 pagination-row">
      <nav class="pagination-nav" aria-label="Pagination navigation">
        <ul class="pagination">
          <li
            class="page-item"
            v-bind:class="{
              'page-item-disabled': !links.prev,
            }"
          >
            <a
              class="prev-next-link"
              v-if="links.prev"
              v-on:click="fetchData(links.prev)"
            >
              Anterior
            </a>
            <span
              class="prev-next-link prev-next-link-disabled"
              v-else
              style="pointer-events: none"
            >
              Anterior
            </span>
          </li>
          <li
            class="page-item"
            v-bind:class="{
              'page-item-disabled': !links.next,
            }"
          >
            <a
              class="prev-next-link"
              v-if="links.next"
              v-on:click="fetchData(links.next)"
            >
              Próximo
            </a>
            <span
              class="prev-next-link prev-next-link-disabled"
              v-else
              style="pointer-events: none"
            >
              Próximo
            </span>
          </li>
        </ul>
      </nav>
    </div>
  </div>
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
  mounted() {
    if (this.paginationData && this.paginationData.links) {
      this.links = this.paginationData.links;
    }
  },
  watch: {
    paginationData: {
      handler(newVal) {
        if (newVal && newVal.links) {

          this.links = newVal.links;

          this.dataLoaded = true;
        }
      },
    },
  },
};
</script>
  
  <style scoped>
/* paginate */
/* Adapte as cores e estilos conforme necessário */

.pagination {
  display: flex;
  list-style: none;
  padding: 0;
}

.pagination-nav {
  margin: 0px;
  padding: 0px;
}

.pagination-row {
  margin: 0px;
  padding: 0px;
  display: flex;
  justify-content: right;
  align-items: flex-end;
}

.page-item {
  margin-right: 5px;
}

.page-link {
  display: block;
  padding: 10px;
  background-color: #3498db;
  color: #fff;
  text-align: center;
  text-decoration: none;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s;
}

.page-link:hover {
  background-color: #2980b9;
}

.page-item-disabled .page-link {
  background-color: red;
  color: #666;
  cursor: not-allowed;
}

.prev-next-link {
  display: block;
  padding: 10px;
  background-color: var(--purple);
  color: #fff;
  text-align: center;
  text-decoration: none;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s;
}

.prev-next-link:hover {
  background-color: var(--purple-light);
  color: var(--purple);
  border-color: var(--purple);
  border-style:solid;
  border-width: 1px;
}

.prev-next-link-disabled {
  background-color: var(--gray);
  color: white;
  cursor: not-allowed;
}
</style>
  