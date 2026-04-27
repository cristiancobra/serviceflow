<template>
  <div>
    <AddMessage :messageStatus="messageStatus" :messageText="messageText"
    @update:messageStatus="messageStatus = $event" />

    <div v-if="modelValue" class="myModal">
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

              <div class="row mt-4">
                <TextAreaInput label="Observações" name="observations" v-model="form.observations" placeholder="Observações opcionais" :rows="3" />
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
import AddMessage from "@/components/forms/messages/AddMessage.vue";
import TextInput from "./inputs/text/TextInput.vue";
import TextAreaInput from "./inputs/textarea/TextAreaInput.vue";

export default {
  name: "LinkCreateForm",
  emits: ["new-link-event", "update:modelValue"],
  components: {
    AddMessage,
    TextInput,
    TextAreaInput,
  },
  props: {
    modelValue: {
      type: Boolean,
      default: false,
    },
    opportunityId: {
      type: Number,
      required: false,
      default: 0,
    },
    taskId: {
      type: Number,
      required: false,
      default: 0,
    }
  },
  data() {
    return {
      form: {
        title: "",
        url: "",
        observations: "",
        opportunity_id: this.opportunityId,
        task_id: this.taskId,
      },
      messageStatus: "",
      messageText: "",
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
      this.form.observations = "";
      this.form.opportunity_id = this.opportunityId;
      this.form.task_id = this.taskId;
    },
    closeModal() {
      this.$emit("update:modelValue", false);
      this.clearForm();
      this.messageStatus = "";
      this.messageText = "";
    },
    setMessageStatus(status) {
      this.messageStatus = status;

      if (status === "error") {
        this.messageText = "Erro ao adicionar LINK!";
      } else if (status === "success") {
        this.messageText = "LINK adicionado com sucesso!";
      }

      setTimeout(() => {
        this.messageStatus = "";
      }, 20000);
    },
    async submitForm() {
      // Prepara os dados do formulário, apenas incluindo task_id e opportunity_id se não forem 0
      const formData = {
        title: this.form.title,
        url: this.form.url,
        observations: this.form.observations,
      };
      
      if (this.taskId && this.taskId !== 0) {
        formData.task_id = this.taskId;
      }
      
      if (this.opportunityId && this.opportunityId !== 0) {
        formData.opportunity_id = this.opportunityId;
      }
      
      const { data, error } = await this.submitFormCreate("links", formData);

      if (data) {
        this.messageStatus = "success";
        this.messageText = "Link criado com sucesso!";
        this.$emit("update:modelValue", false);
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