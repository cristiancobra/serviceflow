<template>
  <div>
    <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.25)">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-gradient-to-r from-green-50 to-green-25 border-b border-gray-200 px-8 py-6 flex justify-between items-center">
          <div>
            <h3 class="text-2xl font-bold text-gray-800">Nova Empresa</h3>
            <p class="text-gray-600 text-sm mt-1">Adicione uma nova empresa ao sistema</p>
          </div>
          <button
            type="button"
            class="text-gray-400 hover:text-gray-600 transition-colors"
            @click="closeModal"
          >
            <font-awesome-icon icon="fa-solid fa-xmark" class="text-2xl" />
          </button>
        </div>

        <!-- Body -->
        <div class="px-8 py-6">
          <ErrorMessage v-if="formResponse" :formResponse="formResponse" />
          
          <form @submit.prevent="submitForm" class="space-y-6">
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
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-8 py-4 flex justify-end gap-3">
          <button
            type="button"
            class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
            @click="closeModal"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-6 py-2 bg-gradient-to-r from-green-600 to-green-800 text-white rounded-lg font-semibold hover:shadow-lg transition-all hover:-translate-y-0.5"
            @click="submitForm"
          >
            <font-awesome-icon icon="fa-solid fa-plus" class="me-2" />
            Criar Empresa
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { submitFormCreate } from "@/utils/requests/httpUtils";
import ErrorMessage from "./messages/ErrorMessage.vue";
import TextInput from "./inputs/text/TextInput";

export default {
  name: "CompanyCreateForm",
  components: {
    ErrorMessage,
    TextInput,
  },
  emits: ["new-company-event", "update:modelValue"],
  props: {
    modelValue: {
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
      this.$emit("update:modelValue", false);
      this.formResponse = null;
      this.clearForm();
    },
    async submitForm() {
      const { data, error } = await this.submitFormCreate(
        "companies",
        this.form
      );

      if (data) {
        this.$emit("update:modelValue", false);
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