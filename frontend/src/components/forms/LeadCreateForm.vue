<template>
  <div>
    <div class="mt-5 mb-5 success">
      {{ message }}
    </div>

    <div id="form" class="container">
      <form @submit.prevent="submitForm">
        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="name"> Nome </label>
            </div>
            <div class="col-10">
              <input
                class="form-control"
                type="text"
                id="name"
                v-model="form.name"
                placeholder="Digite um nome para seu projeto"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="comments"> Observações </label>
            </div>
            <div class="col-10">
              <input
                class="form-control"
                type="text"
                id="comments"
                v-model="form.comments"
                placeholder="Digite o nome do responsável por garantir a execução do projeto"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="email"> Email </label>
            </div>
            <div class="col-10">
              <input
                class="form-control"
                type="text"
                id="email"
                v-model="form.email"
                placeholder="Digite o nome da empresa"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="cel_phone"> Celular </label>
            </div>
            <div class="col-10">
              <input
                class="form-control"
                type="text"
                id="cel_phone"
                v-model="form.cel_phone"
                placeholder="Digite o nome da pessoa que solicitou o projeto"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="user_id"> Responsável </label>
            </div>
            <div class="col-10">
              <input
                class="form-control"
                type="text"
                id="user_id"
                v-model="form.user_id"
                placeholder="Digite o nome do responsável por garantir a execução do projeto"
              />
            </div>
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
          <button type="submit" class="btn new">Criar</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "LeadCreateForm",
  emits: ["new-lead-event"],
  data() {
    return {
      allStatus: [],
      message: null,
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
        contact_date: null,
        source: null,
        source_contact_channel: null,
        reason_for_initial_contact: null,
      },
    };
  },
  methods: {
    // getLeadsStatus() {
    //     axios
    //         .get("http://localhost:8191/api/leads/status")
    //         .then((response) => {
    //             this.allStatus = response.data;
    //         })
    //         .catch((error) => console.log(error));
    // },
    async submitForm() {
      axios
        .post("http://localhost:8191/api/leads", this.form)
        .then((response) => {
          this.data = response.data;
          this.newLeadEvent(this.data);
        });
    },
    newLeadEvent(data) {
      this.$emit("new-lead-event", data);
    },
  },
  // mounted() {
  //     this.getLeadsStatus();
  // },
};
</script>

<style scoped>
.container {
  border-style: solid;
  border-color: #ff3eb5;
  border-width: 2px;
  margin-left: 180px;
  margin-right: 180px;
  margin-bottom: 60px;
  margin-top: 60px;
  padding: 20px;
  border-radius: 16px;
  transition: all 0.5s;
  text-align: left;
  font-weight: 800;
}
.labels {
  text-align: left;
  margin-left: 0;
}
.new {
  background-color: #ff3eb5;
  color: white;
  font-weight: 800;
  padding: 10px 20px 10px 20px;
}
</style>