<template>
  <div class="page-container">
    <PageHeader title="CONTATOS" icon="fa-solid fa-user">
      <template #actions>
        <button type="button" class="btn-create" @click="openCreateLeadModal">
          <font-awesome-icon icon="fa-solid fa-plus" class="me-2" />
          Novo Contato
        </button>
      </template>
    </PageHeader>
    <leads-list ref="leadsList" template="index" />
  </div>
</template>

<script>
import { mapMutations } from "vuex";
import LeadsList from "@/components/lists/LeadsList.vue";
import PageHeader from "@/components/layout/PageHeader.vue";

export default {
  components: {
    PageHeader,
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
