<template>
  <!-- Rota mantida só para links diretos (/leads/:id); a tela em si é o LeadDetailModal,
       que no resto da aplicação é aberto via openModal. -->
  <div class="flex justify-center pt-4 pb-8">
    <lead-detail-modal
      :lead-id="$route.params.id"
      @close="goBack"
    />
  </div>
</template>

<script>
import LeadDetailModal from "@/components/modals/details/LeadDetailModal.vue";

export default {
  components: {
    LeadDetailModal,
  },
  methods: {
    goBack() {
      if (window.history.state?.back) {
        this.$router.back();
      } else {
        this.$router.push({ name: "leadsIndex" });
      }
    },
  },
};
</script>
