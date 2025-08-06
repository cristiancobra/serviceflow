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
            fieldsToDisplay="name"
          />
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <CompaniesSelectInput
            label="Companies"
            fieldsToDisplay="legal_name"
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
      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-bs-dismiss="modal"
          @click="closeModal"
        >
          Fechar
        </button>
        <button type="submit" class="button-new" data-bs-dismiss="modal">
          criar
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { BACKEND_URL, LEAD_URL } from "@/config/apiConfig";
import axios from "axios";
import TextInput from "./inputs/text/TextInput";
import TextAreaInput from "./inputs/textarea/TextAreaInput.vue";
import CompaniesSelectInput from "./selects/CompaniesSelectInput.vue";
import UsersSelectInput from "./selects/UsersSelectInput.vue";

export default {
  name: "LeadCreateForm",
  components: {
    TextInput,
    TextAreaInput,
    CompaniesSelectInput,
    UsersSelectInput,
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
      isModalVisible: false,
      users: [],
      companies: [],
    };
  },
  methods: {
    async submitForm() {
      try {
        const response = await axios.post(
          `${BACKEND_URL}${LEAD_URL}`,
          this.form
        );
        this.newLead = response.data.data;
        console.log("lead-created", this.newLead);
        this.$emit("new-lead-event", this.newLead);
        this.clearForm();
      } catch (error) {
        console.log(error);
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
    closeModal() {
      this.isModalVisible = false;
    },
    openModal() {
      this.isModalVisible = true;
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