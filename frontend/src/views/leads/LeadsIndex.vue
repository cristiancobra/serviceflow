<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-user" class="page-icon" />
        <h1>CONTATOS</h1>
      </div>
      <div class="page-action">
        <button type="button" class="btn-create" @click="openCreateLeadModal">
          <font-awesome-icon icon="fa-solid fa-plus" class="me-2" />
          Novo Contato
        </button>
      </div>
    </div>
    <leads-list ref="leadsList" template="index" />
  </div>
</template>

<script>
import { mapMutations } from "vuex";
import LeadsList from "@/components/lists/LeadsList.vue";

export default {
  components: {
    LeadsList,
  },
  methods: {
    ...mapMutations(["openModal"]),
    openCreateLeadModal() {
      this.openModal({
        component: "LeadCreateForm",
        listeners: {
          "new-lead-event": this.addLeadCreated,
        },
      });
    },
    addLeadCreated(newLead) {
      // Atualiza a lista através do componente filho
      this.$refs.leadsList?.addLeadCreated(newLead);
    },
  },
};
</script>
