<template>
  <!-- Rota mantida só para links diretos (/companies/:id); a tela em si é o CompanyDetailModal,
       que no resto da aplicação é aberto via openModal. -->
  <div class="flex justify-center pt-4 pb-8">
    <company-detail-modal
      :company-id="$route.params.id"
      @close="goBack"
    />
  </div>
</template>

<script>
import CompanyDetailModal from "@/components/modals/details/CompanyDetailModal.vue";

export default {
  components: {
    CompanyDetailModal,
  },
  methods: {
    goBack() {
      if (window.history.state?.back) {
        this.$router.back();
      } else {
        this.$router.push({ name: "companiesIndex" });
      }
    },
  },
};
</script>
