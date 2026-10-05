<template>
  <ModalCard
    title="Empresa"
    :subtitle="company?.business_name || ''"
    :subtitle-editable="!!company"
    subtitle-placeholder="Nome fantasia"
    icon="fa-solid fa-briefcase"
    size="lg"
    :compact="compact"
    compact-size="max-w-3xl"
    @close="$emit('close')"
    @save-subtitle="updateCompanyField('business_name', $event)"
  >
    <div v-if="!company" class="p-5 text-center text-base-content/60">
      Carregando empresa...
    </div>

    <template v-else>
      <error-message v-if="validationErrors" :formResponse="validationErrors" />

      <div
        v-if="message"
        class="flex items-center justify-between gap-4 rounded-lg p-3 mb-6 text-sm font-medium"
        :class="message.status === 'success' ? 'bg-success/10 text-success border border-success/30' : 'bg-error/10 text-error border border-error/30'"
      >
        <span>
          <font-awesome-icon
            :icon="message.status === 'success' ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-exclamation'"
            class="me-2"
          />
          {{ message.text }}
        </span>
        <button type="button" class="opacity-70 hover:opacity-100" @click="message = null">
          <font-awesome-icon icon="fa-solid fa-times" />
        </button>
      </div>

      <!-- Logo + Razão social -->
      <div class="flex flex-wrap items-center gap-6 mb-6">
        <div class="relative flex-shrink-0">
          <button
            type="button"
            class="group relative w-24 h-24 rounded-full border-2 border-primary overflow-hidden bg-base-200 flex items-center justify-center cursor-pointer"
            title="Trocar logo (JPG, PNG, GIF ou WEBP, até 2MB)"
            @click="$refs.photo.click()"
          >
            <img v-if="company.photo" class="w-full h-full object-cover" :src="urlImagePhoto" alt="Logo da empresa" />
            <font-awesome-icon v-else icon="fas fa-building" class="text-4xl text-base-content/50" />
            <span class="absolute inset-0 flex items-center justify-center text-xl text-white bg-black/45 opacity-0 group-hover:opacity-100 transition-opacity">
              <font-awesome-icon icon="fa-solid fa-camera" />
            </span>
          </button>
          <input ref="photo" type="file" accept="image/*" class="hidden" @change="handlePhotoUpload" />
        </div>

        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 text-sm text-base-content/80">
            <font-awesome-icon icon="fas fa-landmark" class="text-primary w-4 flex-shrink-0" />
            <span class="font-semibold whitespace-nowrap">Razão social:</span>
            <text-editable-field
              name="legal_name"
              :modelValue="company.legal_name"
              @save="(value) => updateCompanyField('legal_name', value)"
              placeholder="Razão social..."
            />
          </div>
        </div>
      </div>

      <!-- Seções de campos editáveis -->
      <div
        v-for="section in fieldSections"
        :key="section.title"
        class="mb-6 pb-6 border-b border-base-300 last:mb-0 last:pb-0 last:border-b-0"
      >
        <h3 class="text-base font-bold text-base-content mb-3">{{ section.title }}</h3>
        <div class="grid grid-cols-1 gap-3" :class="compact ? '' : 'md:grid-cols-2'">
          <div v-for="field in section.fields" :key="field.name" class="flex items-center gap-2 text-sm text-base-content/80 min-w-0">
            <font-awesome-icon :icon="field.icon" class="text-primary w-4 flex-shrink-0" />
            <span class="font-semibold whitespace-nowrap">{{ field.label }}:</span>
            <text-editable-field
              :name="field.name"
              :modelValue="company[field.name]"
              @save="(value) => updateCompanyField(field.name, value)"
              :placeholder="field.placeholder"
            />
          </div>
        </div>
      </div>
    </template>

    <template v-if="company" #footer>
      <div class="flex w-full items-center justify-between">
        <button type="button" class="btn btn-error" title="Excluir empresa" @click="deleteCompany">
          <font-awesome-icon icon="fa-solid fa-trash" :class="{ 'me-2': !compact }" />
          <span v-if="!compact">Excluir</span>
        </button>
        <button type="button" class="btn" title="Fechar" @click="$emit('close')">
          <font-awesome-icon v-if="compact" icon="fa-solid fa-times" />
          <template v-else>Fechar</template>
        </button>
      </div>
    </template>
  </ModalCard>
</template>

<script>
import axios from "axios";
import { show, destroy, updateField } from "@/utils/requests/httpUtils";
import { BACKEND_URL, COMPANY_URL, IMAGES_PATH } from "@/config/apiConfig";
import ModalCard from "@/components/modals/ModalCard.vue";
import TextEditableField from "@/components/fields/text/TextEditableField.vue";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";
import { fetchAddressByCep } from "@/utils/address/viaCepUtils";

const FIELD_SECTIONS = [
  {
    title: "Informações da Empresa",
    fields: [
      { name: "cnpj", label: "CNPJ", icon: "fas fa-id-card", placeholder: "00.000.000/0000-00" },
      { name: "email", label: "Email", icon: "fas fa-envelope", placeholder: "email@empresa.com" },
      { name: "phone", label: "Telefone", icon: "fas fa-phone", placeholder: "(00) 0000-0000" },
      { name: "cel_phone", label: "Celular", icon: "fas fa-mobile-alt", placeholder: "DDD + número, só dígitos" },
      { name: "pix_key", label: "Chave Pix", icon: "fab fa-pix", placeholder: "CNPJ, e-mail, +55DDDNUMERO ou aleatória" },
    ],
  },
  {
    title: "Redes Sociais",
    fields: [
      { name: "linkedin", label: "LinkedIn", icon: "fab fa-linkedin", placeholder: "https://linkedin.com/company/..." },
      { name: "facebook", label: "Facebook", icon: "fab fa-facebook", placeholder: "https://facebook.com/..." },
    ],
  },
  {
    title: "Endereço",
    fields: [
      { name: "address", label: "Endereço", icon: "fas fa-map-marker-alt", placeholder: "Rua, número..." },
      { name: "complement", label: "Complemento", icon: "fas fa-building", placeholder: "Sala, andar..." },
      { name: "neighborhood", label: "Bairro", icon: "fas fa-home", placeholder: "Bairro..." },
      { name: "city", label: "Cidade", icon: "fas fa-city", placeholder: "Cidade..." },
      { name: "state", label: "Estado", icon: "fas fa-flag", placeholder: "UF..." },
      { name: "country", label: "País", icon: "fas fa-globe", placeholder: "País..." },
      { name: "zip_code", label: "CEP", icon: "fas fa-mail-bulk", placeholder: "00000-000" },
      { name: "ibge_city_code", label: "Código IBGE", icon: "fas fa-hashtag", placeholder: "Preenchido pelo CEP" },
    ],
  },
];

const MAX_PHOTO_SIZE = 2 * 1024 * 1024;
const ALLOWED_PHOTO_TYPES = ["image/jpeg", "image/png", "image/jpg", "image/gif", "image/webp"];

export default {
  name: "CompanyDetailModal",
  components: {
    ModalCard,
    TextEditableField,
    ErrorMessage,
  },
  props: {
    companyId: {
      type: [Number, String],
      required: true,
    },
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
  },
  // company-updated: qualquer alteração na empresa (payload: empresa atualizada)
  // company-deleted: empresa excluída (payload: id)
  emits: ["close", "company-updated", "company-deleted"],
  data() {
    return {
      company: null,
      validationErrors: null,
      message: null,
      fieldSections: FIELD_SECTIONS,
    };
  },
  computed: {
    urlImagePhoto() {
      return `${IMAGES_PATH}${this.company.photo}`;
    },
  },
  methods: {
    async getCompany() {
      this.company = await show("companies", this.companyId);
    },
    async updateCompanyField(fieldName, newValue) {
      try {
        this.validationErrors = null;
        const updatedCompany = await updateField("companies", this.companyId, fieldName, newValue);
        this.company[fieldName] = updatedCompany[fieldName];
        this.$emit("company-updated", this.company);

        // O CEP preenche o código IBGE do município, exigido na NFS-e
        if (fieldName === "zip_code") {
          const address = await fetchAddressByCep(newValue);
          if (address?.ibgeCityCode && address.ibgeCityCode !== this.company.ibge_city_code) {
            await this.updateCompanyField("ibge_city_code", address.ibgeCityCode);
          }
        }
      } catch (error) {
        console.error(`Erro ao atualizar ${fieldName}:`, error);
        if (error.response?.status === 422) {
          this.validationErrors = { errors: error.response.data.errors || {} };
        } else {
          this.message = { status: "error", text: "Erro ao atualizar a empresa. Tente novamente." };
        }
      }
    },
    async deleteCompany() {
      if (!confirm("Tem certeza que deseja excluir esta empresa? Esta ação não pode ser desfeita.")) {
        return;
      }
      try {
        await destroy("companies", this.companyId);
        this.$emit("company-deleted", this.company.id);
        this.$emit("close");
      } catch (error) {
        console.error("Erro ao excluir empresa:", error);
        this.message = { status: "error", text: "Erro ao excluir a empresa. Tente novamente." };
      }
    },
    async handlePhotoUpload() {
      const photo = this.$refs.photo.files[0];
      this.$refs.photo.value = "";
      if (!photo) return;

      if (photo.size > MAX_PHOTO_SIZE) {
        this.message = { status: "error", text: "Arquivo muito grande! O tamanho máximo é 2MB." };
        return;
      }
      if (!ALLOWED_PHOTO_TYPES.includes(photo.type)) {
        this.message = { status: "error", text: "Formato inválido! Use: JPG, PNG, GIF ou WEBP." };
        return;
      }

      const formData = new FormData();
      formData.append("photo", photo);
      try {
        const response = await axios.post(`${BACKEND_URL}${COMPANY_URL}/${this.companyId}/photo`, formData);
        this.company.photo = response.data.data.photo;
        this.message = { status: "success", text: "Logo enviado com sucesso!" };
        this.$emit("company-updated", this.company);
      } catch (error) {
        console.error("Erro ao fazer upload da foto:", error);
        let text = "Erro ao fazer upload do logo. Tente novamente.";
        if (error.response?.status === 422) {
          text = error.response.data.errors?.photo?.[0] || "Erro de validação. Verifique o arquivo.";
        } else if (error.response?.status === 413) {
          text = "Arquivo muito grande! Máximo 2MB.";
        }
        this.message = { status: "error", text };
      }
    },
  },
  watch: {
    companyId() {
      this.getCompany();
    },
  },
  mounted() {
    this.getCompany();
  },
};
</script>
