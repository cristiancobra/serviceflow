<template>
  <div>
    <button type="button" class="button button-new d-flex justify-content-center" @click="openModal">
      <font-awesome-icon icon="fa-solid fa-plus" class="" />
    </button>

    <div v-if="isModalVisible" class="myModal">
      <div class="">
        <div class="modal-content">
          <div class="modal-header">
            <font-awesome-icon icon="fa-solid fa-link" class="icon pe-3 primary" />
            <h5 class="modal-title" id="linkModalLabel">Novo Link</h5>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm">

              <div class="row">
                <div class="col-12">
                  <TextInput label="Título" name="title" v-model="form.title" placeholder="Título do link" />
                </div>
              </div>

              <div class="row mt-4">
                <TextInput label="URL" name="url" v-model="form.url" placeholder="URL do link" />
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeModal">Fechar</button>
                <button type="submit" class="button-new">Criar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { submitFormCreate } from "@/utils/requests/httpUtils";
import TextInput from "./inputs/text/TextInput";

export default {
  name: "LinkCreateForm",
  emits: ["new-link-event"],
  components: {
    TextInput,
  },
  props: {
    taskId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      form: {
        title: "",
        url: "",
        opportunity_id: null,
        task_id: this.taskId,
      },
      isModalVisible: false,
    };
  },
  // inject: [
  //   'currentTask',
  //   'currentOpportunity',
  // ],
  methods: {
    submitFormCreate,
    clearForm() {
      this.form.title = "";
      this.form.url = "";
      this.form.task_id = this.taskId;
    },
    closeModal() {
      this.isModalVisible = false;
    },
    openModal() {
      this.isModalVisible = true;
    },
    async submitForm() {
      this.form.task_id = this.taskId; 
      
      const { data, error } = await this.submitFormCreate("links", this.form);

      if (data) {
        this.messageStatus = "success";
        this.messageText = "Link criado com sucesso!";
        this.isError = false;
        this.closeModal();
        this.clearForm();
        this.$emit("new-link-event", data);
      }
      if (error) {
        this.errors = error;
      }
    },
  },
  watch: {
    taskId(newTaskId) {
      this.form.task_id = newTaskId;
      console.log('watch.task_id', this.taskId);
    }
  },
};
</script>

<style scoped>
/* Estilos específicos para o componente */
</style>