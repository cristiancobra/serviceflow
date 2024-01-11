<template>
  <div class="form-container">
    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <form @submit.prevent="submitForm">
      <div class="row">
        <div class="col-12">
          <TextInput
            label="Nome"
            type="text"
            name="name"
            v-model="form.name"
            placeholder="Digite um nome para seu projeto"
          />
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <TextInput
            label="Email"
            type="text"
            name="email"
            v-model="form.email"
            placeholder="Email do contato"
          />
        </div>

        <div class="col-md-4">
          <TextInput
            label="Telefone celular"
            type="text"
            name="cel_phone"
            v-model="form.cel_phone"
            placeholder="Telefone celular do contato"
          />
        </div>
      </div>

      <div class="row">
        <TextAreaInput
          label="Observações"
          name="comments"
          v-model="form.comments"
          placeholder="Informações adicionais"
          :rows="3"
        />
      </div>

      <div class="row">
        <div class="col-md-4">
          <UsersSelectInput
            label="Adicionado por"
            v-model="form.user_id"
            fieldToDisplay="name"
          />
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <CompaniesSelectInput
            label="Companies"
            :items="users"
            fieldToDisplay="name"
          />
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2">
            <label class="labels" for="linkedin"> LinkedIn </label>
          </div>
          <div class="col-10">
            <input
              class="form-control"
              type="text"
              id="linkedin"
              v-model="form.linkedin"
              placeholder="Digite o perfil do LinkedIn"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2">
            <label class="labels" for="facebook"> Facebook </label>
          </div>
          <div class="col-10">
            <input
              class="form-control"
              type="text"
              id="facebook"
              v-model="form.facebook"
              placeholder="Digite o perfil do Facebook"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2">
            <label class="labels" for="instagram"> Instagram </label>
          </div>
          <div class="col-10">
            <input
              class="form-control"
              type="text"
              id="instagram"
              v-model="form.instagram"
              placeholder="Digite o perfil do Instagram"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2">
            <label class="labels" for="other_social_media">
              Outras redes sociais
            </label>
          </div>
          <div class="col-10">
            <input
              class="form-control"
              type="text"
              id="other_social_media"
              v-model="form.other_social_media"
              placeholder="Digite outras redes sociais"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2">
            <label class="labels" for="address"> Endereço </label>
          </div>
          <div class="col-10">
            <input
              class="form-control"
              type="text"
              id="address"
              v-model="form.address"
              placeholder="Endereço"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2">
            <label class="labels" for="address_complement"> Complemento </label>
          </div>
          <div class="col-10">
            <input
              class="form-control"
              type="text"
              id="address_complement"
              v-model="form.address_complement"
              placeholder="Complemento"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2">
            <label class="labels" for="neighborhood"> Bairro </label>
          </div>
          <div class="col-10">
            <input
              class="form-control"
              type="text"
              id="neighborhood"
              v-model="form.neighborhood"
              placeholder="Bairro"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2">
            <label class="labels" for="city"> Cidade </label>
          </div>
          <div class="col-10">
            <input
              class="form-control"
              type="text"
              id="city"
              v-model="form.city"
              placeholder="Cidade"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2">
            <label class="labels" for="state"> Estado </label>
          </div>
          <div class="col-10">
            <input
              class="form-control"
              type="text"
              id="state"
              v-model="form.state"
              placeholder="Estado"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2">
            <label class="labels" for="country"> País </label>
          </div>
          <div class="col-10">
            <input
              class="form-control"
              type="text"
              id="country"
              v-model="form.country"
              placeholder="País"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2">
            <label class="labels" for="zip_code"> CEP </label>
          </div>
          <div class="col-10">
            <input
              class="form-control"
              type="text"
              id="zip_code"
              v-model="form.zip_code"
              placeholder="CEP"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2">
            <label class="labels" for="contact_date"> Data de contato </label>
          </div>
          <div class="col-10">
            <input
              class="form-control"
              type="date"
              id="contact_date"
              v-model="form.contact_date"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2">
            <label class="labels" for="source"> Origem </label>
          </div>
          <div class="col-10">
            <input
              class="form-control"
              type="text"
              id="source"
              v-model="form.source"
              placeholder="Digite a origem"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2">
            <label class="labels" for="source_contact_channel">
              Canal de contato da origem
            </label>
          </div>
          <div class="col-10">
            <input
              class="form-control"
              type="text"
              id="source_contact_channel"
              v-model="form.source_contact_channel"
              placeholder="Digite o canal de contato da origem"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2">
            <label class="labels" for="reason_for_initial_contact">
              Motivo do primeiro contato
            </label>
          </div>
          <div class="col-10">
            <textarea
              class="form-control"
              id="reason_for_initial_contact"
              v-model="form.reason_for_initial_contact"
              placeholder="Digite o motivo do primeiro contato"
            ></textarea>
          </div>
        </div>
      </div>

      <div class="row ms-5 me-5 mt-4 mb-2">
        <button type="submit" class="button-new">Criar</button>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import ErrorMessage from "./messages/ErrorMesssage.vue";
import SuccessMessage from "./messages/SuccessMessage.vue";
import TextInput from "./inputs/text/TextInput";
import TextAreaInput from "./inputs/TextAreaInput.vue";
import CompaniesSelectInput from "./inputs/companies/CompaniesSelectInput.vue";
import UsersSelectInput from "./inputs/users/UsersSelectInput.vue";

export default {
  name: "LeadCreateForm",
  components: {
    TextInput,
    TextAreaInput,
    CompaniesSelectInput,
    UsersSelectInput,
    ErrorMessage,
    SuccessMessage,
  },
  emits: ["new-lead-event"],
  data() {
    return {
      allStatus: [],
      isError: false,
      isSuccess: false,
      data: [],
      form: {
        name: null,
        comments: null,
        email: null,
        cel_phone: null,
        user_id: null,
        linkedin: null,
        facebook: null,
        instagram: null,
        other_social_media: null,
        address: null,
        address_complement: null,
        neighborhood: null,
        city: null,
        state: null,
        country: null,
        zip_code: null,
        contact_date: null,
        source: null,
        source_contact_channel: null,
        reason_for_initial_contact: null,
      },
    };
  },
  methods: {
    async submitForm() {
      try {
        const response = await axios.post(
          "http://localhost:8191/api/leads",
          this.form
        );
        this.data = response.data.data;
        console.log(this.data);
        this.newLeadEvent(this.data);
        this.clearForm();
      } catch (error) {
        console.error(error);
        if (error.response && error.response.status === 422) {
          this.isError = true;
          this.isSuccess = false;
          this.formResponse = error.response.data;
          console.log(error.response.data);
        }
        if (!error.response) {
          this.formResponse =
            "Ocorreu um erro ao enviar o formulário. Tente novamente.";
        }
      }
    },
    clearForm() {
      this.form.name = null;
      this.form.comments = null;
      this.form.email = null;
      this.form.cel_phone = null;
      this.form.user_id = null;
      this.form.linkedin = null;
      this.form.facebook = null;
      this.form.instagram = null;
      this.form.other_social_media = null;
      this.form.address = null;
      this.form.address_complement = null;
      this.form.neighborhood = null;
      this.form.city = null;
      this.form.state = null;
      this.form.country = null;
      this.form.zip_code = null;
      this.form.contact_date = null;
      this.form.source = null;
      this.form.source_contact_channel = null;
      this.form.reason_for_initial_contact = null;
    },
    newLeadEvent(data) {
      console.log(data);
      this.$emit("new-lead-event", data);
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