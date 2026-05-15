<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-briefcase" class="page-icon" />
        <h1>EMPRESAS</h1>
      </div>
      <div class="page-action">
        <button type="button" class="btn-create" @click="isCreateCompanyModalVisible = true">
          <font-awesome-icon icon="fa-solid fa-plus" class="me-2" />
          Nova Empresa
        </button>
        <company-create-form v-model="isCreateCompanyModalVisible" @new-company-event="addCompanyCreated" />
      </div>
    </div>
    <companies-list ref="companiesList" template="index" />
  </div>
</template>

<script>
import CompaniesList from "@/components/lists/CompaniesList.vue";
import CompanyCreateForm from "@/components/forms/CompanyCreateForm.vue";

export default {
  components: {
    CompaniesList,
    CompanyCreateForm,
  },
  data() {
    return {
      isCreateCompanyModalVisible: false,
    };
  },
  methods: {
    addCompanyCreated(newCompany) {
      this.isCreateCompanyModalVisible = false;
      // Atualiza a lista através do componente filho
      this.$refs.companiesList?.addCompanyCreated(newCompany);
    },
  },
};
</script>
