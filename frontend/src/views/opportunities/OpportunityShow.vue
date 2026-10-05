<template>
  <!-- Rota mantida só para links diretos (/opportunities/:id); a tela em si é o OpportunityDetailModal,
       que no resto da aplicação é aberto via openModal. -->
  <div class="flex justify-center pt-4 pb-8">
    <opportunity-detail-modal
      :opportunity-id="$route.params.id"
      :initial-tab="$route.query.tab || 'info'"
      :highlight-element-id="$route.hash ? $route.hash.substring(1) : null"
      @tab-changed="updateTabQuery"
      @opportunity-deleted="handleDeleted"
      @close="goBack"
    />
  </div>
</template>

<script>
import OpportunityDetailModal from "@/components/modals/details/OpportunityDetailModal.vue";

export default {
  components: {
    OpportunityDetailModal,
  },
  data() {
    return {
      deleted: false,
    };
  },
  methods: {
    updateTabQuery(tab) {
      this.$router.replace({ query: { ...this.$route.query, tab }, hash: this.$route.hash });
    },
    handleDeleted() {
      // O modal emite close logo depois; a página da oportunidade excluída não deve voltar ao histórico
      this.deleted = true;
      this.goToIndex();
    },
    goToIndex() {
      this.$router.push({ name: "opportunitiesIndex" });
    },
    goBack() {
      if (this.deleted) return;
      if (window.history.state?.back) {
        this.$router.back();
      } else {
        this.goToIndex();
      }
    },
  },
};
</script>
