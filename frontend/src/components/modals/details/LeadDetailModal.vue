<template>
  <ModalCard
    title="Contato"
    :subtitle="lead?.name || ''"
    icon="fa-solid fa-user"
    size="lg"
    :compact="compact"
    compact-size="max-w-3xl"
    @close="$emit('close')"
  >
    <div v-if="!lead" class="p-5 text-center text-gray-500">
      Carregando contato...
    </div>

    <template v-else>
      <error-message v-if="validationErrors" :formResponse="validationErrors" />

      <div
        v-if="message"
        class="flex items-center justify-between gap-4 rounded-lg p-3 mb-6 text-sm font-medium"
        :class="message.status === 'success' ? 'bg-green-50 text-green-800 border border-green-300' : 'bg-red-50 text-red-800 border border-red-300'"
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

      <!-- Foto + Nome + Tipo -->
      <div class="flex flex-wrap items-center gap-6 mb-6">
        <div class="relative flex-shrink-0">
          <button
            type="button"
            class="photo-container group"
            title="Trocar foto (JPG, PNG, GIF ou WEBP, até 2MB)"
            @click="$refs.photo.click()"
          >
            <img v-if="lead.photo" class="photo" :src="urlImagePhoto" alt="Foto do contato" />
            <font-awesome-icon v-else icon="fas fa-user" class="text-4xl text-gray-400" />
            <span class="photo-overlay">
              <font-awesome-icon icon="fa-solid fa-camera" />
            </span>
          </button>
          <input ref="photo" type="file" accept="image/*" class="hidden" @change="handlePhotoUpload" />
        </div>

        <div class="flex-1 min-w-0">
          <div class="text-2xl font-bold text-gray-800">
            <text-editable-field
              name="name"
              :modelValue="lead.name"
              @save="(value) => updateLeadField('name', value)"
              placeholder="Nome do contato..."
            />
          </div>
          <div class="text-gray-600">
            <text-editable-field
              name="comments"
              :modelValue="lead.comments"
              @save="(value) => updateLeadField('comments', value)"
              placeholder="Comentários..."
            />
          </div>
          <div class="flex items-center gap-2 text-sm text-gray-700 mt-2">
            <font-awesome-icon icon="fas fa-tag" class="text-cyan-500" />
            <span class="font-semibold">Tipo:</span>
            <select-editable-input
              name="category"
              :modelValue="lead.category"
              :options="leadTypeOptions"
              @save="(value) => updateLeadField('category', value)"
              class-text="font-semibold"
            />
          </div>
        </div>
      </div>

      <!-- Seções de campos editáveis -->
      <div v-for="section in fieldSections" :key="section.title" class="lead-section">
        <h3 class="lead-section-title">{{ section.title }}</h3>
        <div class="grid grid-cols-1 gap-3" :class="compact ? '' : 'md:grid-cols-2'">
          <div v-for="field in section.fields" :key="field.name" class="flex items-center gap-2 text-sm text-gray-700 min-w-0">
            <font-awesome-icon :icon="field.icon" class="text-primary w-4 flex-shrink-0" />
            <span class="font-semibold whitespace-nowrap">{{ field.label }}:</span>
            <date-editable-input
              v-if="field.type === 'date'"
              :name="field.name"
              :modelValue="lead[field.name]"
              @save="(value) => updateLeadField(field.name, value)"
            />
            <text-editable-field
              v-else
              :name="field.name"
              :modelValue="lead[field.name]"
              @save="(value) => updateLeadField(field.name, value)"
              :placeholder="field.placeholder"
            />
          </div>
        </div>
      </div>

      <!-- Financeiro -->
      <div class="lead-section">
        <div class="flex items-center justify-between mb-3">
          <h3 class="lead-section-title !mb-0">
            <font-awesome-icon icon="fas fa-money-bill-wave" class="text-green-500 me-2" />
            Financeiro
          </h3>
          <button type="button" class="btn btn-primary btn-sm" title="Nova oportunidade" @click="openCreateOpportunityModal">
            <font-awesome-icon icon="fa-solid fa-plus" class="text-white" />
          </button>
        </div>

        <div v-if="lead.opportunities && lead.opportunities.length > 0" class="space-y-3">
          <div
            v-for="opportunity in lead.opportunities"
            :key="opportunity.id"
            class="border border-gray-200 rounded-lg p-4 bg-gray-50"
          >
            <div class="flex items-center justify-between gap-2">
              <router-link
                :to="{ name: 'opportunityShow', params: { id: opportunity.id } }"
                class="font-bold text-primary hover:underline flex items-center gap-2 min-w-0"
                @click="$emit('close')"
              >
                <font-awesome-icon icon="fas fa-bullseye" />
                <span class="truncate">{{ opportunity.name || `#${opportunity.id}` }}</span>
              </router-link>
              <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs font-medium whitespace-nowrap">
                {{ opportunity.proposals?.length || 0 }} proposta{{ (opportunity.proposals?.length || 0) !== 1 ? "s" : "" }}
              </span>
            </div>
            <p v-if="opportunity.description" class="text-sm text-gray-600 mt-1">{{ opportunity.description }}</p>

            <div v-if="opportunity.proposals && opportunity.proposals.length > 0" class="space-y-2 mt-3">
              <div
                v-for="proposal in opportunity.proposals"
                :key="proposal.id"
                class="bg-white border border-gray-200 rounded-lg px-3 py-2"
              >
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                  <router-link
                    :to="{ name: 'proposalShow', params: { id: proposal.id } }"
                    class="font-semibold text-cyan-600 hover:text-cyan-800"
                    @click="$emit('close')"
                  >
                    Proposta #{{ proposal.id }}
                  </router-link>
                  <span :class="getStatusClass(proposal.status)" class="px-2 py-0.5 rounded-full text-xs font-medium border">
                    {{ translateStatus(proposal.status) }}
                  </span>
                  <span class="text-gray-500">{{ formatDateBr(proposal.date) }}</span>
                  <span class="ms-auto flex gap-3">
                    <span>Valor: <b class="text-blue-600">{{ formatCurrency(proposal.total_price) }}</b></span>
                    <span>Pago: <b class="text-green-600">{{ formatCurrency(proposal.total_paid) }}</b></span>
                    <span>
                      Saldo:
                      <b :class="calculateBalance(proposal) === 0 ? 'text-green-600' : 'text-orange-600'">
                        {{ formatCurrency(calculateBalance(proposal)) }}
                      </b>
                    </span>
                  </span>
                </div>
                <p v-if="proposal.description" class="mt-1 text-xs text-gray-600">
                  {{ getShortDescription(proposal.description) }}
                </p>
              </div>
            </div>
            <p v-else class="text-gray-500 text-sm mt-2">Nenhuma proposta criada para esta oportunidade ainda</p>
          </div>
        </div>

        <div v-else class="text-center py-6">
          <font-awesome-icon icon="fas fa-bullseye" class="text-gray-300 text-3xl mb-2" />
          <p class="text-gray-500">Nenhuma oportunidade encontrada para este contato</p>
        </div>

        <!-- Resumo financeiro -->
        <div v-if="totalProposalsCount > 0" class="mt-4 grid grid-cols-2 gap-3" :class="compact ? '' : 'md:grid-cols-4'">
          <div class="bg-blue-50 rounded-lg p-3 text-center">
            <div class="text-xl font-bold text-blue-600">{{ acceptedProposalsCount }}</div>
            <div class="text-xs text-blue-700 font-medium">Propostas Aceitas</div>
          </div>
          <div class="bg-orange-50 rounded-lg p-3 text-center">
            <div class="text-xl font-bold text-orange-600">{{ pendingProposalsCount }}</div>
            <div class="text-xs text-orange-700 font-medium">Propostas Pendentes</div>
          </div>
          <div class="bg-green-50 rounded-lg p-3 text-center">
            <div class="text-xl font-bold text-green-600">{{ formatCurrency(totalProposalsValue) }}</div>
            <div class="text-xs text-green-700 font-medium">Valor Total</div>
          </div>
          <div class="rounded-lg p-3 text-center" :class="totalBalance === 0 ? 'bg-gray-50' : 'bg-red-50'">
            <div class="text-xl font-bold" :class="totalBalance === 0 ? 'text-gray-600' : 'text-red-600'">
              {{ formatCurrency(totalBalance) }}
            </div>
            <div class="text-xs font-medium" :class="totalBalance === 0 ? 'text-gray-700' : 'text-red-700'">Saldo Total</div>
          </div>
        </div>
      </div>
    </template>

    <template v-if="lead" #footer>
      <div class="flex w-full items-center justify-between">
        <button type="button" class="btn btn-error" title="Excluir contato" @click="deleteLead">
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
import { mapMutations } from "vuex";
import { show, destroy, updateField } from "@/utils/requests/httpUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import { BACKEND_URL, LEAD_URL, IMAGES_PATH } from "@/config/apiConfig";
import ModalCard from "@/components/modals/ModalCard.vue";
import SelectEditableInput from "@/components/fields/select/SelectEditableInput.vue";
import TextEditableField from "@/components/fields/text/TextEditableField.vue";
import DateEditableInput from "@/components/fields/date/DateEditableInput.vue";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";

const LEAD_TYPE_OPTIONS = [
  { value: "client", label: "Cliente" },
  { value: "supplier", label: "Fornecedor" },
  { value: "partner", label: "Parceiro" },
  { value: "employee", label: "Funcionário" },
  { value: "freelancer", label: "Freelancer" },
];

const FIELD_SECTIONS = [
  {
    title: "Informações de Contato",
    fields: [
      { name: "email", label: "Email", icon: "fas fa-envelope", placeholder: "email@exemplo.com" },
      { name: "cel_phone", label: "Celular", icon: "fas fa-mobile-alt", placeholder: "(00) 00000-0000" },
      { name: "pix_key", label: "Chave Pix", icon: "fab fa-pix", placeholder: "CPF, e-mail, +55DDDNUMERO ou aleatória" },
    ],
  },
  {
    title: "Redes Sociais",
    fields: [
      { name: "linkedin", label: "LinkedIn", icon: "fab fa-linkedin", placeholder: "https://linkedin.com/in/..." },
      { name: "facebook", label: "Facebook", icon: "fab fa-facebook", placeholder: "https://facebook.com/..." },
      { name: "instagram", label: "Instagram", icon: "fab fa-instagram", placeholder: "@usuario" },
      { name: "other_social_media", label: "Outras", icon: "fas fa-external-link-alt", placeholder: "Outras redes..." },
    ],
  },
  {
    title: "Detalhes do Contato",
    fields: [
      { name: "contact_date", label: "Primeiro contato", icon: "fas fa-calendar-alt", type: "date" },
      { name: "source", label: "Origem", icon: "fas fa-link", placeholder: "Ex: Google, Indicação..." },
      { name: "source_contact_channel", label: "Canal", icon: "fas fa-comments", placeholder: "Ex: Email, Telefone..." },
      { name: "reason_for_initial_contact", label: "Razão", icon: "fas fa-info-circle", placeholder: "Motivo do contato..." },
    ],
  },
  {
    title: "Endereço",
    fields: [
      { name: "address", label: "Endereço", icon: "fas fa-map-marker-alt", placeholder: "Rua, número..." },
      { name: "address_complement", label: "Complemento", icon: "fas fa-building", placeholder: "Apto, sala..." },
      { name: "neighborhood", label: "Bairro", icon: "fas fa-home", placeholder: "Bairro..." },
      { name: "city", label: "Cidade", icon: "fas fa-city", placeholder: "Cidade..." },
      { name: "state", label: "Estado", icon: "fas fa-flag", placeholder: "UF..." },
      { name: "country", label: "País", icon: "fas fa-globe", placeholder: "País..." },
      { name: "zip_code", label: "CEP", icon: "fas fa-mail-bulk", placeholder: "00000-000" },
    ],
  },
];

const STATUS_TRANSLATIONS = {
  draft: "rascunho",
  submitted: "enviada",
  accepted: "aceita",
  rejected: "rejeitada",
  canceled: "cancelada",
};

const STATUS_CLASSES = {
  draft: "bg-yellow-50 text-yellow-700 border-yellow-200",
  submitted: "bg-blue-50 text-blue-700 border-blue-200",
  accepted: "bg-green-100 text-green-800 border-green-300",
  rejected: "bg-red-50 text-red-700 border-red-200",
  canceled: "bg-gray-50 text-gray-700 border-gray-200",
};

const MAX_PHOTO_SIZE = 2 * 1024 * 1024;
const ALLOWED_PHOTO_TYPES = ["image/jpeg", "image/png", "image/jpg", "image/gif", "image/webp"];

export default {
  name: "LeadDetailModal",
  components: {
    ModalCard,
    SelectEditableInput,
    TextEditableField,
    DateEditableInput,
    ErrorMessage,
  },
  props: {
    leadId: {
      type: [Number, String],
      required: true,
    },
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
  },
  // lead-updated: qualquer alteração no contato (payload: lead atualizado)
  // lead-deleted: contato excluído (payload: id)
  emits: ["close", "lead-updated", "lead-deleted"],
  data() {
    return {
      lead: null,
      validationErrors: null,
      message: null,
      leadTypeOptions: LEAD_TYPE_OPTIONS,
      fieldSections: FIELD_SECTIONS,
    };
  },
  computed: {
    allProposals() {
      return (this.lead?.opportunities || []).flatMap((opportunity) => opportunity.proposals || []);
    },
    totalProposalsCount() {
      return this.allProposals.length;
    },
    totalProposalsValue() {
      return this.allProposals.reduce((total, proposal) => total + (parseFloat(proposal.total_price) || 0), 0);
    },
    acceptedProposalsCount() {
      return this.allProposals.filter((proposal) => proposal.status === "accepted").length;
    },
    pendingProposalsCount() {
      return this.allProposals.filter((proposal) => ["draft", "submitted"].includes(proposal.status)).length;
    },
    totalBalance() {
      return this.allProposals.reduce((total, proposal) => total + this.calculateBalance(proposal), 0);
    },
    urlImagePhoto() {
      return `${IMAGES_PATH}${this.lead.photo}`;
    },
  },
  methods: {
    ...mapMutations(["openModal"]),
    formatDateBr,
    async getLead() {
      this.lead = await show("leads", this.leadId);
    },
    async updateLeadField(fieldName, newValue) {
      try {
        this.validationErrors = null;
        const updatedLead = await updateField("leads", this.leadId, fieldName, newValue);
        this.lead[fieldName] = updatedLead[fieldName];
        this.$emit("lead-updated", this.lead);
      } catch (error) {
        console.error(`Erro ao atualizar ${fieldName}:`, error);
        if (error.response?.status === 422) {
          this.validationErrors = { errors: error.response.data.errors || {} };
        } else {
          this.message = { status: "error", text: "Erro ao atualizar o contato. Tente novamente." };
        }
      }
    },
    async deleteLead() {
      if (!confirm("Tem certeza que deseja excluir este contato? Esta ação não pode ser desfeita.")) {
        return;
      }
      try {
        await destroy("leads", this.leadId);
        this.$emit("lead-deleted", this.lead.id);
        this.$emit("close");
      } catch (error) {
        console.error("Erro ao excluir contato:", error);
        this.message = { status: "error", text: "Erro ao excluir o contato. Tente novamente." };
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
        const response = await axios.post(`${BACKEND_URL}${LEAD_URL}/${this.leadId}/photo`, formData);
        this.lead.photo = response.data.data.photo;
        this.message = { status: "success", text: "Foto enviada com sucesso!" };
        this.$emit("lead-updated", this.lead);
      } catch (error) {
        console.error("Erro ao fazer upload da foto:", error);
        let text = "Erro ao fazer upload da foto. Tente novamente.";
        if (error.response?.status === 422) {
          text = error.response.data.errors?.photo?.[0] || "Erro de validação. Verifique o arquivo.";
        } else if (error.response?.status === 413) {
          text = "Arquivo muito grande! Máximo 2MB.";
        }
        this.message = { status: "error", text };
      }
    },
    openCreateOpportunityModal() {
      this.openModal({
        component: "OpportunityCreateForm",
        props: { currentLead: this.lead },
        listeners: {
          "new-opportunity-event": this.addOpportunityCreated,
        },
      });
    },
    addOpportunityCreated(newOpportunity) {
      if (!this.lead.opportunities) {
        this.lead.opportunities = [];
      }
      this.lead.opportunities.unshift({ proposals: [], ...newOpportunity });
      this.message = { status: "success", text: "Oportunidade criada com sucesso!" };
    },
    formatCurrency(value) {
      return new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(parseFloat(value) || 0);
    },
    getStatusClass(status) {
      return STATUS_CLASSES[status] || "bg-gray-100 text-gray-800 border-gray-300";
    },
    translateStatus(status) {
      return STATUS_TRANSLATIONS[status] || status;
    },
    getShortDescription(description, maxLength = 100) {
      if (!description) return "";
      return description.length > maxLength ? `${description.substring(0, maxLength)}...` : description;
    },
    calculateBalance(proposal) {
      return (parseFloat(proposal.total_price) || 0) - (parseFloat(proposal.total_paid) || 0);
    },
  },
  watch: {
    leadId() {
      this.getLead();
    },
  },
  mounted() {
    this.getLead();
  },
};
</script>

<style scoped>
.lead-section {
  margin-bottom: 1.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid var(--color-base-300);
}

.lead-section:last-child {
  margin-bottom: 0;
  padding-bottom: 0;
  border-bottom: none;
}

.lead-section-title {
  font-size: 1rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.75rem;
}

.photo-container {
  position: relative;
  width: 96px;
  height: 96px;
  border: 2px solid var(--primary);
  border-radius: 50%;
  overflow: hidden;
  background-color: #f9fafb;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.photo-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
  background-color: rgba(0, 0, 0, 0.45);
  opacity: 0;
  transition: opacity 0.2s;
}

.photo-container:hover .photo-overlay {
  opacity: 1;
}
</style>
