<template>
  <!-- Rota mantida só para links diretos (/users/:id); a tela em si é o UserDetailModal,
       que no resto da aplicação é aberto via openModal. -->
  <div class="flex justify-center py-8">
    <user-detail-modal
      :user-id="$route.params.id"
      @close="goBack"
    />
  </div>
</template>

<script>
import UserDetailModal from "@/components/modals/details/UserDetailModal.vue";

export default {
  components: {
    UserDetailModal,
  },
  methods: {
    goBack() {
      if (window.history.state?.back) {
        this.$router.back();
      } else {
        this.$router.push({ name: "home" });
      }
    },
  },
};
</script>
