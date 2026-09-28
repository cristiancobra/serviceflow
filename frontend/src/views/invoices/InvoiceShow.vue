<template>
  <!-- Rota mantida só para links diretos (/invoices/:id); a tela em si é o InvoiceDetailModal,
       que no resto da aplicação é aberto via openModal. -->
  <div class="flex justify-center py-8">
    <invoice-detail-modal
      :invoice-id="$route.params.id"
      @close="goBack"
    />
  </div>
</template>

<script>
import InvoiceDetailModal from "@/components/modals/details/InvoiceDetailModal.vue";

export default {
  components: {
    InvoiceDetailModal,
  },
  methods: {
    goBack() {
      if (window.history.state?.back) {
        this.$router.back();
      } else {
        this.$router.push({ name: "accountsPayable" });
      }
    },
  },
};
</script>
