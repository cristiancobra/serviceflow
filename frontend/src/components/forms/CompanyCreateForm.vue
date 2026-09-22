<template>
  <ModalCard
    title="Nova Empresa"
    subtitle="Adicione uma nova empresa ao sistema"
    icon="fa-solid fa-briefcase"
    :compact="compact"
    @close="closeModal"
  >
    <ErrorMessage v-if="formResponse" :formResponse="formResponse" />

    <form id="companyCreateForm" @submit.prevent="submitForm" class="space-y-6">
      <!-- Razão Social -->
      <div>
        <TextInput
          label="Razão Social"
          type="text"
          name="legal_name"
          v-model="form.legal_name"
          placeholder="Nome legal da empresa"
        />
      </div>

      <!-- Nome Fantasia -->
      <div>
        <TextInput
          label="Nome Fantasia"
          type="text"
          name="business_name"
          v-model="form.business_name"
          placeholder="Nome fantasia da empresa"
        />
      </div>

      <!-- CNPJ -->
      <div>
        <TextInput
          label="CNPJ"
          type="text"
          name="cnpj"
          v-model="form.cnpj"
          placeholder="00.000.000/0000-00"
        />
      </div>

      <!-- Email e Telefone -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <TextInput
            label="Email"
            type="email"
            name="email"
            v-model="form.email"
            placeholder="email@empresa.com"
          />
        </div>
        <div>
          <TextInput
            label="Telefone Celular"
            type="text"
            name="cel_phone"
            v-model="form.cel_phone"
            placeholder="(11) 99999-9999"
          />
        </div>
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
        form="companyCreateForm"
        class="px-6 py-2 bg-primary hover:opacity-90 text-white rounded-lg font-semibold transition-colors"
      >
        <font-awesome-icon icon="fa-solid fa-plus" class="me-2" />
        Criar Empresa
      </button>
    </template>
  </ModalCard>
</template>

<script>
import { submitFormCreate } from "@/utils/requests/httpUtils";
import ErrorMessage from "./messages/ErrorMessage.vue";
import TextInput from "./inputs/text/TextInput.vue";
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
  name: "CompanyCreateForm",
  components: {
    ErrorMessage,
    TextInput,
    ModalCard,
  },
  emits: ["new-company-event", "close"],
  props: {
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
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
    submitFormCreate,
    closeModal() {
      this.$emit("close");
      this.formResponse = null;
      this.clearForm();
    },
    async submitForm() {
      const { data, error } = await this.submitFormCreate(
        "companies",
        this.form
      );

      if (data) {
        this.$emit("close");
        this.$emit("new-company-event", data);
        this.clearForm();
        this.formResponse = null;
      }
      if (error) {
        this.formResponse = error.response?.data || { errors: { geral: ['Erro ao criar empresa'] } };
        console.error("Erro ao criar empresa:", error);
      }
    },
    clearForm() {
      this.form.legal_name = null;
      this.form.business_name = null;
      this.form.cnpj = null;
      this.form.email = null;
      this.form.cel_phone = null;
    },
  },
};
</script>