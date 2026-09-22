<template>
  <ModalCard
    title="Novo Contato"
    subtitle="Adicione um novo contato ao sistema"
    icon="fa-solid fa-user-plus"
    :compact="compact"
    @close="closeModal"
  >
    <ErrorMessage v-if="formResponse" :formResponse="formResponse" />

    <form id="leadCreateForm" @submit.prevent="submitForm" class="space-y-6">
      <!-- Nome -->
      <div>
        <TextInput
          label="Nome"
          type="text"
          name="name"
          v-model="form.name"
          placeholder="Nome completo do contato"
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
            placeholder="email@exemplo.com"
            @blur="validateEmail"
          />
          <small v-if="errors.email" class="text-red-600 text-sm block mt-2">
            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="me-1" />
            {{ errors.email }}
          </small>
        </div>
        <div>
          <TextInput
            label="Telefone Celular"
            type="text"
            name="cel_phone"
            v-model="form.cel_phone"
            placeholder="(11) 99999-9999"
            @blur="validatePhone"
          />
          <small v-if="errors.cel_phone" class="text-red-600 text-sm block mt-2">
            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="me-1" />
            {{ errors.cel_phone }}
          </small>
        </div>
      </div>

      <!-- Observações -->
      <div>
        <TextAreaInput
          label="Observações"
          name="comments"
          v-model="form.comments"
          placeholder="Informações adicionais sobre o contato"
          :rows="3"
        />
      </div>

      <!-- Usuário e Empresa -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <UsersSelectInput
            label="Adicionado por"
            v-model="form.user_id"
            fieldsToDisplay="name"
            autoSelect="true"
          />
        </div>
        <div>
          <CompaniesSelectInput
            label="Empresa"
            v-model="form.company_id"
            :fieldsToDisplay="['business_name', 'legal_name']"
            fieldNull="Nenhuma"
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
        form="leadCreateForm"
        class="px-6 py-2 bg-primary hover:opacity-90 text-white rounded-lg font-semibold transition-colors"
      >
        <font-awesome-icon icon="fa-solid fa-plus" class="me-2" />
        Criar Contato
      </button>
    </template>
  </ModalCard>
</template>

<script>
import { submitFormCreate } from "@/utils/requests/httpUtils";
import TextInput from "./inputs/text/TextInput.vue";
import TextAreaInput from "./inputs/textarea/TextAreaInput.vue";
import CompaniesSelectInput from "./selects/CompaniesSelectInput.vue";
import UsersSelectInput from "./selects/UsersSelectInput.vue";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
  name: "LeadCreateForm",
  components: {
    TextInput,
    TextAreaInput,
    CompaniesSelectInput,
    UsersSelectInput,
    ErrorMessage,
    ModalCard,
  },
  emits: ["new-lead-event", "close"],
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
        name: null,
        comments: null,
        email: null,
        cel_phone: null,
        user_id: null,
        company_id: null,
      },
      formResponse: null,
      errors: {
        email: null,
        cel_phone: null,
      },
    };
  },
  methods: {
    submitFormCreate,
    closeModal() {
      this.$emit("close");
      this.formResponse = null;
      this.clearErrors();
    },
    async submitForm() {
      const { data, error } = await this.submitFormCreate(
        "leads",
        this.form
      );

      if (data) {
        this.$emit("close");
        this.$emit("new-lead-event", data);
        this.clearForm();
        this.formResponse = null;
      }
      if (error) {
        this.formResponse = error.response?.data || { errors: { geral: ['Erro ao criar contato'] } };
        console.error("Erro ao criar contato:", error);
      }
    },
    clearForm() {
      this.form.name = null;
      this.form.comments = null;
      this.form.email = null;
      this.form.cel_phone = null;
      this.form.user_id = null;
      this.form.company_id = null;
      this.clearErrors();
    },
    clearErrors() {
      this.errors.email = null;
      this.errors.cel_phone = null;
    },
    validateEmail() {
      if (!this.form.email) {
        this.errors.email = null;
        return;
      }
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      this.errors.email = emailRegex.test(this.form.email)
        ? null
        : "Email inválido.";
    },
    validatePhone() {
      if (!this.form.cel_phone) {
        this.errors.cel_phone = null;
        return;
      }
      const phoneRegex = /^\(\d{2}\) \d{5}-\d{4}$/;
      this.errors.cel_phone = phoneRegex.test(this.form.cel_phone)
        ? null
        : "Telefone inválido. Use o formato (11) 99999-9999.";
    },
    formatPhone(value) {
      if (!value) return '';
      const digits = value.replace(/\D/g, '');
      
      if (digits.length <= 2) {
        return digits;
      } else if (digits.length <= 6) {
        return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
      } else {
        return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7, 11)}`;
      }
    },
  },
  watch: {
    'form.cel_phone'(newVal) {
      if (newVal && !newVal.includes('(')) {
        this.form.cel_phone = this.formatPhone(newVal);
      }
    },
  },
};
</script>