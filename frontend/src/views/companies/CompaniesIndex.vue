<template>
  <div class="container">
    <LeadsFilter @toggle="toggle" />    
      <form-modal
        :is-visible="isModalVisible"
        title="Criar nova empresa"
        icon="fa-solid fa-building"
        @close="toggle"
        @submit="toggle"
      >
      <company-create-form @new-company-event="addCompanyCreated($event)" />
      </form-modal>
    <template v-if="companies.length > 0">
      <div class="row">
        <CompaniesList :companies="companies" />
      </div>
    </template>
    <template v-else>
      <NoLeadsMessage @new-company-event="addCompanyCreated($event)" />
    </template>

  </div>
</template>

<script>
import { BACKEND_URL, COMPANY_URL } from "@/config/apiConfig";
import axios from "axios";
import LeadsFilter from "@/components/filters/LeadsFilter.vue";
import CompaniesList from "@/components/lists/CompaniesList.vue";
import NoLeadsMessage from '@/components/messages/NoLeadsMessage.vue';
import FormModal from '../../components/layout/FormModal.vue';
import CompanyCreateForm from "@/components/forms/CompanyCreateForm.vue";

export default {
  name: "CompaniesIndex",
  components: {
    LeadsFilter,
    CompanyCreateForm,
    CompaniesList,
    NoLeadsMessage,
    FormModal,
  },
  data() {
    return {
      isModalVisible: false,
      hasError: false,
      data: null,
      companies: [],
      newCompany: {},
    };
  },
  methods: {
    toggle() {
      this.isModalVisible = !this.isModalVisible;
    },
    getCompanies() {
      axios
      .get(`${BACKEND_URL}${COMPANY_URL}`)
        .then((response) => {
          this.companies = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    addCompanyCreated(newCompany) {
      console.log("Empresa criada:", newCompany);
      this.companies.push(newCompany);
      console.log("Nova EMPRESA adicionada:", newCompany.legal_name);
      this.isModalVisible = false;
      // this.hasError = false;
      // !this.toggle();
    },
  },
  mounted() {
    this.getCompanies();
  },
  computed: {
    companiesData() {
      return this.companies || [];
    },
  },
};
</script>

<style scoped>
.filters-container {
  margin-top: 40px;
  margin-bottom: 50px;
  margin-left: 25%;
  margin-right: 25%;
  display: flex;
  justify-content: center;
}
.slot {
  border-width: 2px;
  border-style: solid;
  border-color: white;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  margin: 0 4px 0 4px;
  color: white;
  font-weight: 800;
  width: 120px;
}
.done {
  background-color: white;
  border-color: #2cb48d;
  color: #2cb48d;
}
.done:hover {
  background-color: #2cb48d;
  color: white;
}
.doing {
  background-color: white;
  border-color: #e78d1f;
  color: #e78d1f;
}
.doing:hover {
  background-color: #e78d1f;
  color: white;
}
.late {
  background-color: white;
  border-color: #b1388d;
  color: #b1388d;
}
.late:hover {
  background-color: #b1388d;
  color: white;
}
.new {
  border-radius: 20px 20px 20px 20px;
  background-color: white;
  border-color: #ff3eb5;
  color: #ff3eb5;
  margin-left: 50px;
  font-size: 16px;
}
.new:hover {
  background-color: #ff3eb5;
  color: white;
  margin-left: 50px;
}
.hidden {
  display: none;
  transition: display 2s;
}
.show {
  display: block;
  transition: display 2s;
}
</style>
