<template>
  <SectionCard title="Nota fiscal de serviço (NFS-e)">
    <error-message v-if="validationErrors" :formResponse="validationErrors" />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="space-y-4">
        <div>
          <TextEditableField
            name="ibge_city_code"
            :modelValue="account.ibge_city_code"
            label="Código IBGE do município:"
            placeholder="7 dígitos"
            @save="$emit('update-field', 'ibge_city_code', $event)"
          />
          <p class="text-xs text-base-content/60 mt-1">Preenchido automaticamente ao salvar o CEP.</p>
        </div>

        <div>
          <TextEditableField
            name="nfse_default_service_code"
            :modelValue="account.nfse_default_service_code"
            label="Código de tributação nacional padrão:"
            placeholder="6 dígitos"
            @save="$emit('update-field', 'nfse_default_service_code', $event)"
          />
          <p class="text-xs text-base-content/60 mt-1">
            Usado quando o serviço da fatura não tem código próprio. Confira o seu no Emissor Nacional (nfse.gov.br).
          </p>
        </div>

        <fieldset class="fieldset">
          <legend class="fieldset-legend">Ambiente de emissão</legend>
          <select
            class="select w-full"
            :value="account.nfse_environment"
            @change="$emit('update-field', 'nfse_environment', $event.target.value)"
          >
            <option value="restricted">Produção restrita (testes)</option>
            <option value="production">Produção</option>
          </select>
          <p v-if="account.nfse_environment === 'production'" class="text-xs text-warning">
            Notas emitidas em produção têm validade fiscal.
          </p>
          <p v-else class="text-xs text-base-content/60">
            Notas de teste, sem validade fiscal.
          </p>
        </fieldset>

        <div class="grid grid-cols-2 gap-4">
          <TextEditableField
            name="nfse_dps_series"
            :modelValue="account.nfse_dps_series"
            label="Série da DPS:"
            @save="$emit('update-field', 'nfse_dps_series', $event)"
          />
          <TextEditableField
            name="nfse_next_dps_number"
            :modelValue="account.nfse_next_dps_number"
            label="Próximo número da DPS:"
            @save="$emit('update-field', 'nfse_next_dps_number', $event)"
          />
        </div>
      </div>

      <div class="space-y-4">
        <h3 class="font-semibold text-base-content">Certificado digital A1</h3>

        <div v-if="certificate.uploaded" class="rounded-lg bg-base-200 p-4 space-y-2">
          <div class="flex items-start gap-2 text-sm">
            <font-awesome-icon icon="fa-solid fa-certificate" class="text-primary mt-1" />
            <span class="break-all">{{ certificate.holder }}</span>
          </div>
          <div class="flex items-center gap-2 text-sm">
            <span class="font-semibold">Validade:</span>
            <span>{{ expiresAtFormatted }}</span>
            <span class="badge badge-sm" :class="expirationBadge.class">{{ expirationBadge.label }}</span>
          </div>
          <div class="flex gap-2 pt-2">
            <button type="button" class="btn btn-sm btn-secondary" @click="showUploadForm = !showUploadForm">
              <font-awesome-icon icon="fa-solid fa-arrows-rotate" />
              Substituir
            </button>
            <button type="button" class="btn btn-sm btn-error" :disabled="isSubmitting" @click="removeCertificate">
              <font-awesome-icon icon="fa-solid fa-trash" />
              Remover
            </button>
          </div>
        </div>
        <p v-else class="text-sm text-base-content/60">
          Nenhum certificado enviado. Sem ele não é possível emitir notas.
        </p>

        <form v-if="!certificate.uploaded || showUploadForm" class="space-y-3" @submit.prevent="uploadCertificate">
          <fieldset class="fieldset">
            <legend class="fieldset-legend">Arquivo do certificado (.pfx ou .p12)</legend>
            <input
              ref="certificateFile"
              type="file"
              accept=".pfx,.p12"
              class="file-input w-full"
              @change="certificateFile = $event.target.files[0] || null"
            />
          </fieldset>
          <fieldset class="fieldset">
            <legend class="fieldset-legend">Senha do certificado</legend>
            <input v-model="certificatePassword" type="password" class="input w-full" autocomplete="new-password" />
            <p class="text-xs text-base-content/60">A senha é guardada criptografada e nunca é exibida.</p>
          </fieldset>
          <button type="submit" class="btn btn-primary" :disabled="isSubmitting || !certificateFile || !certificatePassword">
            <span v-if="isSubmitting" class="loading loading-spinner loading-sm"></span>
            <font-awesome-icon v-else icon="fa-solid fa-upload" />
            Enviar certificado
          </button>
        </form>
      </div>
    </div>
  </SectionCard>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, ACCOUNT_URL } from "@/config/apiConfig";
import SectionCard from "@/components/common/SectionCard.vue";
import TextEditableField from "@/components/fields/text/TextEditableField.vue";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";

const DAY_IN_MS = 24 * 60 * 60 * 1000;

export default {
  name: "AccountNfseSection",
  components: {
    SectionCard,
    TextEditableField,
    ErrorMessage,
  },
  props: {
    account: {
      type: Object,
      required: true,
    },
  },
  // update-field: campo simples alterado (payload: nome do campo, valor)
  // account-updated: certificado enviado ou removido (payload: conta atualizada)
  emits: ["update-field", "account-updated"],
  data() {
    return {
      certificateFile: null,
      certificatePassword: "",
      showUploadForm: false,
      isSubmitting: false,
      validationErrors: null,
    };
  },
  computed: {
    certificate() {
      return this.account.nfse_certificate || { uploaded: false };
    },
    expiresAtFormatted() {
      if (!this.certificate.expires_at) return "";
      return new Date(this.certificate.expires_at).toLocaleDateString("pt-BR");
    },
    expirationBadge() {
      const daysLeft = Math.floor((new Date(this.certificate.expires_at) - new Date()) / DAY_IN_MS);
      if (daysLeft < 0) return { label: "vencido", class: "badge-error" };
      if (daysLeft <= 30) return { label: `vence em ${daysLeft} dias`, class: "badge-warning" };
      return { label: "válido", class: "badge-success" };
    },
  },
  methods: {
    async uploadCertificate() {
      const formData = new FormData();
      formData.append("certificate", this.certificateFile);
      formData.append("password", this.certificatePassword);

      this.isSubmitting = true;
      this.validationErrors = null;
      try {
        const response = await axios.post(`${BACKEND_URL}${ACCOUNT_URL}/${this.account.id}/nfse-certificate`, formData);
        this.$emit("account-updated", response.data.data);
        this.resetUploadForm();
      } catch (error) {
        this.handleError(error, "Erro ao enviar o certificado. Tente novamente.");
      } finally {
        this.isSubmitting = false;
      }
    },
    async removeCertificate() {
      if (!confirm("Remover o certificado? Sem ele não será possível emitir notas.")) {
        return;
      }

      this.isSubmitting = true;
      this.validationErrors = null;
      try {
        const response = await axios.delete(`${BACKEND_URL}${ACCOUNT_URL}/${this.account.id}/nfse-certificate`);
        this.$emit("account-updated", response.data.data);
      } catch (error) {
        this.handleError(error, "Erro ao remover o certificado. Tente novamente.");
      } finally {
        this.isSubmitting = false;
      }
    },
    resetUploadForm() {
      this.certificateFile = null;
      this.certificatePassword = "";
      this.showUploadForm = false;
      if (this.$refs.certificateFile) {
        this.$refs.certificateFile.value = "";
      }
    },
    handleError(error, fallbackText) {
      console.error(error);
      const errors = error.response?.status === 422
        ? error.response.data.errors || {}
        : { certificate: [fallbackText] };
      this.validationErrors = { errors };
    },
  },
};
</script>
