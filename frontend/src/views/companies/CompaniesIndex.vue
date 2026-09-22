<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-briefcase" class="page-icon" />
        <h1>EMPRESAS</h1>
      </div>
      <div class="page-action">
        <button type="button" class="btn-create" @click="openCreateCompanyModal">
          <font-awesome-icon icon="fa-solid fa-plus" class="me-2" />
          Nova Empresa
        </button>
      </div>
    </div>
    <companies-list ref="companiesList" template="index" />
  </div>
</template>

<script>
import { mapMutations } from "vuex";
import CompaniesList from "@/components/lists/CompaniesList.vue";

export default {
  components: {
    CompaniesList,
  },
  methods: {
    ...mapMutations(["openModal"]),
    openCreateCompanyModal() {
      this.openModal({
        component: "CompanyCreateForm",
        listeners: {
          "new-company-event": this.addCompanyCreated,
        },
      });
    },
    addCompanyCreated(newCompany) {
      // Atualiza a lista através do componente filho
      this.$refs.companiesList?.addCompanyCreated(newCompany);
    },
  },
};
</script>
