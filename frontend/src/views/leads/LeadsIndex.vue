<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-user" class="page-icon" />
        <h1>CONTATOS</h1>
      </div>
      <div class="page-action">
        <button type="button" class="btn-create" @click="isCreateLeadModalVisible = true">
          <font-awesome-icon icon="fa-solid fa-plus" class="me-2" />
          Novo Contato
        </button>
        <lead-create-form v-model="isCreateLeadModalVisible" @new-lead-event="addLeadCreated" />
      </div>
    </div>
    <leads-list template="index" />
  </div>
</template>

<script>
import LeadsList from "@/components/lists/LeadsList.vue";

export default {
  components: {
    LeadsList,
  },
};
</script>
