<template>
  <div>
    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <div id="form" class="form-container">
      <form @submit.prevent="submitForm">
        <div class="row">
          <div class="col">
            <TextInput
              size="full"
              label="Razão social"
              type="text"
              name="legal_name"
              v-model="form.legal_name"
              placeholder="nome legal da empresa"
            />
          </div>
        </div>

        <div class="row">
          <div class="col">
            <TextInput
              size="full"
              label="Nome fantasia"
              type="text"
              name="business_name"
              v-model="form.business_name"
              placeholder="nome fantasia da empresa"
            />
          </div>
        </div>

        <div class="row">
          <div class="col-6">
            <TextInput
              label="CNPJ"
              type="text"
              name="cnpj"
              v-model="form.cnpj"
              placeholder="Cadastro nacional de Pessoa Jurídica"
            />
          </div>
        </div>

        <div class="row">
          <div class="col-6">
            <TextInput
              label="Email"
              type="text"
              name="email"
              v-model="form.email"
              placeholder="email da empresa"
            />
          </div>

          <div class="col-6">
            <TextInput
              label="Telefone celular"
              type="text"
              name="cel_phone"
              v-model="form.cel_phone"
              placeholder="telefone celular da empresa"
            />
          </div>
        </div>

        <div class="row ms-5 me-5 mt-4 mb-2">
          <button type="submit" class="button-new">Criar</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import TextInput from "./inputs/TextInput";
import ErrorMessage from "./messages/ErrorMesssage.vue";
import SuccessMessage from "./messages/SuccessMessage.vue";

export default {
  name: "CompanyCreateForm",
  components: {
    ErrorMessage,
    SuccessMessage,
    TextInput,
  },
  emits: ["new-company-event"],
  data() {
    return {
      allStatus: [],
      isError: false,
      isSuccess: false,
      data: [],
      form: {
        legal_name: null,
        business_name: null,
        cnpj: null,
        email: null,
        cel_phone: null,
      },
      formResponse: null,
    };
  },
  methods: {
    async submitForm() {
      try {
        const response = await axios.post(
          "http://localhost:8191/api/companies",
          this.form
        );
        this.data = response.data;
        this.isSuccess = true;
        this.isError = false;
        this.newCompanyEvent(this.data);
        this.successMessage(this.data);
        this.clearForm();
      } catch (error) {
        console.error(error); // Imprima o objeto de erro para análise
        if (error.response.status == 422) {
          this.isError = true;
          this.isSuccess = false;
          this.formResponse = error.response.data;
          // console.log(error.response.data);
        }
        if (!error.response) {
          this.formResponse =
            "Ocorreu um erro ao enviar o formulário. Tente novamente.";
        }
      }
    },
    clearForm() {
      this.form.legal_name = null;
      this.form.business_name = null;
      this.form.cnpj = null;
      this.form.email = null;
      this.form.cel_phone = null;
    },
    newCompanyEvent(data) {
      this.$emit("new-company-event", data);
    },
    successMessage(data) {
      this.formResponse =
        "Empresa " + data.data.legal_name + " criada com sucesso!";
    },
    updateForm(value) {
      this.form.business_name = value;
    },
  },
};
</script>

<style scoped>
.labels {
  text-align: left;
  margin-left: 0;
  line-height: 2.5;
}
</style>