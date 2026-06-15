<template>
  <div>
    <AddMessage :messageStatus="messageStatus" :messageText="messageText"
    @update:messageStatus="messageStatus = $event" />

    <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-gradient-to-r from-blue-50 to-blue-25 border-b border-gray-200 px-8 py-6">
          <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
              <font-awesome-icon icon="fa-solid fa-link" class="text-2xl text-primary" />
              <h3 class="text-2xl font-bold text-gray-800">Novo Link</h3>
            </div>
            <button 
              type="button" 
              class="text-gray-400 hover:text-gray-600 transition-colors"
              @click="closeModal" 
              aria-label="Close"
            >
              <font-awesome-icon icon="fa-solid fa-times" class="text-2xl" />
            </button>
          </div>
        </div>

        <!-- Body -->
        <div class="px-8 py-6">
          <form @submit.prevent="submitForm">
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

            <!-- Footer com Ações -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
              <button 
                type="button" 
                class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                @click="closeModal"
              >
                Fechar
              </button>
              <button 
                type="submit" 
                class="px-6 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-blue-600 transition-colors"
              >
                <font-awesome-icon icon="fa-solid fa-plus" class="me-2" />
                Criar Link
              </button>
            </div>
          </form>
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