<template>
  <div class="page-container">
    <PageHeader title="EMPRESAS" icon="fa-solid fa-briefcase">
      <template #actions>
        <button type="button" class="btn-create" @click="openCreateCompanyModal">
          <font-awesome-icon icon="fa-solid fa-plus" class="me-2" />
          Nova Empresa
        </button>
      </template>
    </PageHeader>
    <companies-list ref="companiesList" template="index" />
  </div>
</template>

<script>
import { mapMutations } from "vuex";
import CompaniesList from "@/components/lists/CompaniesList.vue";
import PageHeader from "@/components/layout/PageHeader.vue";

export default {
  components: {
    PageHeader,
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
