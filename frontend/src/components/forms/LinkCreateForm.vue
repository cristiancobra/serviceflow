<template>
  <ModalCard
    title="Novo Link"
    icon="fa-solid fa-link"
    :compact="compact"
    @close="closeModal"
  >
    <AddMessage :messageStatus="messageStatus" :messageText="messageText"
    @update:messageStatus="messageStatus = $event" />

    <form id="linkCreateForm" @submit.prevent="submitForm">
      <!-- Título -->
      <div class="mb-6">
        <TextInput
          label="Título"
          name="title"
          v-model="form.title"
          placeholder="Título do link"
        />
      </div>

      <!-- URL -->
      <div class="mb-6">
        <TextInput
          label="URL"
          name="url"
          v-model="form.url"
          placeholder="https://exemplo.com"
        />
      </div>

      <!-- Observações -->
      <div class="mb-6">
        <TextAreaInput
          label="Observações"
          name="observations"
          v-model="form.observations"
          placeholder="Observações opcionais"
          :rows="3"
        />
      </div>
    </form>

    <template #footer>
      <button
        type="button"
        class="px-6 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg font-semibold hover:bg-base-200 transition-colors"
        @click="closeModal"
      >
        Cancelar
      </button>
      <button
        type="submit"
        form="linkCreateForm"
        class="px-6 py-2 bg-primary hover:opacity-90 text-white rounded-lg font-semibold transition-colors flex items-center gap-2"
      >
        <font-awesome-icon icon="fa-solid fa-plus" />
        Criar Link
      </button>
    </template>
  </ModalCard>
</template>

<script>
import { submitFormCreate } from "@/utils/requests/httpUtils";
import AddMessage from "@/components/forms/messages/AddMessage.vue";
import TextInput from "./inputs/text/TextInput.vue";
import TextAreaInput from "./inputs/textarea/TextAreaInput.vue";
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
  name: "LinkCreateForm",
  emits: ["new-link-event", "close"],
  components: {
    AddMessage,
    TextInput,
    TextAreaInput,
    ModalCard,
  },
  props: {
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
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
      this.$emit("close");
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
        this.$emit("close");
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