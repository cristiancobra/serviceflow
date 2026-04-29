<template>
  <div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Messages -->
    <add-message 
      :messageStatus="messageStatus" 
      :messageText="messageText"
      @update:messageStatus="setMessageStatus"
    />
    <error-message v-if="validationErrors" :formResponse="validationErrors" />

    <!-- Header Card -->
    <div class="bg-white border-2 border-primary rounded-xl p-6 mb-8 shadow-lg">
      <div class="flex items-center mb-4">
        <font-awesome-icon icon="fas fa-user-plus" class="text-primary text-3xl mr-4" />
        <h1 class="text-2xl font-black text-gray-800">LEAD</h1>
      </div>
      <div class="text-3xl font-black text-gray-900 mb-2">
        <text-editable-field
          name="name"
          :modelValue="lead.name"
          @save="(value) => updateLeadField('name', value)"
          placeholder="Nome do lead..."
        />
      </div>
      <p class="text-gray-600 text-lg">
        <text-editable-field
          name="comments"
          :modelValue="lead.comments"
          @save="(value) => updateLeadField('comments', value)"
          placeholder="Comentários..."
        />
      </p>
    </div>

    <!-- Photo Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">
        Foto
      </h2>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="flex justify-center items-center p-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
          <div class="photo-container">
            <img v-if="lead.photo" class="photo" :src="urlImagePhoto" alt="Foto do Lead">
            <div v-else class="flex items-center justify-center h-full text-gray-400">
              <font-awesome-icon icon="fas fa-user" class="text-6xl" />
            </div>
          </div>
        </div>
        <div class="space-y-4">
          <form @submit.prevent="submitFormPhoto">
            <div class="flex flex-col mb-4">
              <label for="photo" class="mb-2 text-sm font-medium text-gray-700">Foto:</label>
              <input 
                type="file" 
                id="photo" 
                ref="photo" 
                @change="handlePhotoUpload"
                accept="image/*"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
              >
              <p class="mt-2 text-xs text-gray-500">
                Formatos: JPG, PNG, GIF ou WEBP. Tamanho máximo: 2MB
              </p>
            </div>
            <div class="flex justify-start">
              <button 
                type="submit" 
                class="px-6 py-2 text-white bg-primary hover:bg-primary-content hover:text-primary font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md"
              >
                Enviar Foto
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Contact Information Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">
        Informações de Contato
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-tag" class="text-cyan-500 mr-3 w-5" />
          <span class="font-bold mr-2">Tipo:</span>
          <select-editable-input
            name="category"
            :modelValue="lead.category"
            :options="leadTypeOptions"
            @save="updateLeadType"
            class-text="font-semibold"
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-envelope" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Email:</span>
          <text-editable-field
            name="email"
            :modelValue="lead.email"
            @save="(value) => updateLeadField('email', value)"
            placeholder="email@exemplo.com"
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-mobile-alt" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Celular:</span>
          <text-editable-field
            name="cel_phone"
            :modelValue="lead.cel_phone"
            @save="(value) => updateLeadField('cel_phone', value)"
            placeholder="(00) 00000-0000"
          />
        </div>
      </div>
    </div>

    <!-- Social Media Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">
        Redes Sociais
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fab fa-linkedin" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">LinkedIn:</span>
          <text-editable-field
            name="linkedin"
            :modelValue="lead.linkedin"
            @save="(value) => updateLeadField('linkedin', value)"
            placeholder="https://linkedin.com/in/..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fab fa-facebook" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Facebook:</span>
          <text-editable-field
            name="facebook"
            :modelValue="lead.facebook"
            @save="(value) => updateLeadField('facebook', value)"
            placeholder="https://facebook.com/..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fab fa-instagram" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Instagram:</span>
          <text-editable-field
            name="instagram"
            :modelValue="lead.instagram"
            @save="(value) => updateLeadField('instagram', value)"
            placeholder="@usuario"
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-external-link-alt" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Outras redes sociais:</span>
          <text-editable-field
            name="other_social_media"
            :modelValue="lead.other_social_media"
            @save="(value) => updateLeadField('other_social_media', value)"
            placeholder="Outras redes..."
          />
        </div>
      </div>
    </div>

    <!-- Contact Details Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">
        Detalhes do Contato
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-calendar-alt" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Data do primeiro contato:</span>
          <date-editable-input
            name="contact_date"
            :modelValue="lead.contact_date"
            @save="(value) => updateLeadField('contact_date', value)"
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-link" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Origem:</span>
          <text-editable-field
            name="source"
            :modelValue="lead.source"
            @save="(value) => updateLeadField('source', value)"
            placeholder="Ex: Google, Indicação..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-comments" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Canal do primeiro contato:</span>
          <text-editable-field
            name="source_contact_channel"
            :modelValue="lead.source_contact_channel"
            @save="(value) => updateLeadField('source_contact_channel', value)"
            placeholder="Ex: Email, Telefone..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-info-circle" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Razão do primeiro contato:</span>
          <text-editable-field
            name="reason_for_initial_contact"
            :modelValue="lead.reason_for_initial_contact"
            @save="(value) => updateLeadField('reason_for_initial_contact', value)"
            placeholder="Motivo do contato..."
          />
        </div>
      </div>
    </div>

    <!-- Address Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
      <h2 class="text-xl font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">
        Endereço
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-map-marker-alt" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Endereço:</span>
          <text-editable-field
            name="address"
            :modelValue="lead.address"
            @save="(value) => updateLeadField('address', value)"
            placeholder="Rua, número..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-building" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Complemento:</span>
          <text-editable-field
            name="address_complement"
            :modelValue="lead.address_complement"
            @save="(value) => updateLeadField('address_complement', value)"
            placeholder="Apto, sala..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-home" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Bairro:</span>
          <text-editable-field
            name="neighborhood"
            :modelValue="lead.neighborhood"
            @save="(value) => updateLeadField('neighborhood', value)"
            placeholder="Bairro..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-city" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Cidade:</span>
          <text-editable-field
            name="city"
            :modelValue="lead.city"
            @save="(value) => updateLeadField('city', value)"
            placeholder="Cidade..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-flag" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Estado:</span>
          <text-editable-field
            name="state"
            :modelValue="lead.state"
            @save="(value) => updateLeadField('state', value)"
            placeholder="UF..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-globe" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">País:</span>
          <text-editable-field
            name="country"
            :modelValue="lead.country"
            @save="(value) => updateLeadField('country', value)"
            placeholder="País..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-mail-bulk" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">CEP:</span>
          <text-editable-field
            name="zip_code"
            :modelValue="lead.zip_code"
            @save="(value) => updateLeadField('zip_code', value)"
            placeholder="00000-000"
          />
        </div>
      </div>
    </div>

    <!-- Financial Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-800 border-b border-gray-200 pb-2 flex items-center">
          <font-awesome-icon icon="fas fa-money-bill-wave" class="text-green-500 mr-3" />
          FINANCEIRO
        </h2>
        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
          {{ proposals.length }} proposta{{ proposals.length !== 1 ? 's' : '' }}
        </span>
      </div>
      
      <!-- Proposals List -->
      <div v-if="opportunitiesWithProposals.length > 0" class="space-y-6">
        <div 
          v-for="opportunity in opportunitiesWithProposals" 
          :key="opportunity.id"
          class="border border-gray-300 rounded-lg p-5 bg-gray-50"
        >
          <!-- Opportunity Header -->
          <div class="mb-4 pb-3 border-b border-gray-300">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <font-awesome-icon icon="fas fa-bullseye" class="text-blue-500 mr-3" />
                <router-link 
                  :to="{ name: 'opportunityShow', params: { id: opportunity.id } }"
                  class="text-lg font-bold text-blue-600 hover:text-blue-800 transition-colors"
                >
                  Oportunidade: {{ opportunity.name || `#${opportunity.id}` }}
                </router-link>
              </div>
              <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                {{ opportunity.proposals.length }} proposta{{ opportunity.proposals.length !== 1 ? 's' : '' }}
              </span>
            </div>
            <div v-if="opportunity.description" class="text-sm text-gray-600 mt-2">
              {{ opportunity.description }}
            </div>
          </div>
          
          <!-- Proposals within this Opportunity -->
          <div class="space-y-3">
            <div 
              v-for="proposal in opportunity.proposals" 
              :key="proposal.id"
              class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200"
            >
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <div class="flex items-center mb-2">
                    <router-link 
                      :to="{ name: 'proposalShow', params: { id: proposal.id } }"
                      class="text-lg font-semibold text-cyan-600 hover:text-cyan-800 transition-colors"
                    >
                      Proposta #{{ proposal.id }}
                    </router-link>
                    <span 
                      :class="getStatusClass(proposal.status)"
                      class="ml-3 px-3 py-1 rounded-full text-xs font-medium"
                    >
                      {{ translateStatus(proposal.status) }}
                    </span>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 text-sm text-gray-600">
                    <div class="flex items-center">
                      <font-awesome-icon icon="fas fa-calendar-alt" class="text-gray-400 mr-2 w-4" />
                      <span class="font-medium mr-1">Data:</span>
                      <span>{{ formatDateBr(proposal.date) }}</span>
                    </div>
                    
                    <div class="flex items-center justify-end">
                      <font-awesome-icon icon="fas fa-dollar-sign" class="text-gray-400 mr-2 w-4" />
                      <span class="font-medium mr-1">Valor:</span>
                      <span class="text-blue-600 font-bold">
                        {{ formatCurrency(proposal.total_price) }}
                      </span>
                    </div>
                    <div class="flex items-center justify-end">
                      <font-awesome-icon icon="fas fa-check-circle" class="text-gray-400 mr-2 w-4" />
                      <span class="font-medium mr-1">Pago:</span>
                      <span class="text-green-600 font-bold">
                        {{ formatCurrency(proposal.total_paid) }}
                      </span>
                    </div>
                    
                    <div class="flex items-center justify-end">
                      <font-awesome-icon icon="fas fa-balance-scale" class="text-gray-400 mr-2 w-4" />
                      <span class="font-medium mr-1">Saldo:</span>
                      <span 
                        :class="calculateBalance(proposal) === 0 ? 'text-green-600' : 'text-orange-600'"
                        class="font-bold"
                      >
                        {{ formatCurrency(calculateBalance(proposal)) }}
                      </span>
                    </div>
                  </div>
                  
                  <div v-if="proposal.description" class="mt-2 text-sm text-gray-700">
                    <span class="font-medium">Descrição:</span>
                    <p class="mt-1">{{ getShortDescription(proposal.description) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-else class="text-center py-8">
        <font-awesome-icon icon="fas fa-file-invoice-dollar" class="text-gray-300 text-4xl mb-3" />
        <p class="text-gray-500 text-lg">Nenhuma proposta encontrada para este lead</p>
        <p class="text-gray-400 text-sm">As propostas aparecerão aqui quando forem criadas</p>
      </div>
      
      <!-- Financial Summary -->
      <div v-if="totalProposalsCount > 0" class="mt-6 pt-4 border-t border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
         
          <div class="bg-blue-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">
              {{ acceptedProposalsCount }}
            </div>
            <div class="text-sm text-blue-700 font-medium">Propostas Aceitas</div>
          </div>
          
          <div class="bg-orange-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-orange-600">
              {{ pendingProposalsCount }}
            </div>
            <div class="text-sm text-orange-700 font-medium">Propostas Pendentes</div>
          </div>
          
          <div class="bg-green-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600">
              {{ formatCurrency(totalProposalsValue) }}
            </div>
            <div class="text-sm text-green-700 font-medium">Valor Total das Propostas</div>
          </div>

          <div 
            :class="totalBalance === 0 ? 'bg-gray-50' : 'bg-red-50'"
            class="rounded-lg p-4 text-center"
          >
            <div 
              :class="totalBalance === 0 ? 'text-gray-600' : 'text-red-600'"
              class="text-2xl font-bold"
            >
              {{ formatCurrency(totalBalance) }}
            </div>
            <div 
              :class="totalBalance === 0 ? 'text-gray-700' : 'text-red-700'"
              class="text-sm font-medium"
            >
              Saldo Total
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Button -->
    <div class="flex justify-end">
      <button 
        @click="deleteLead()"
        class="bg-red-100 border-2 border-red-600 text-red-600 font-bold px-6 py-2 rounded-full hover:bg-red-600 hover:text-white transition-colors duration-200"
      >
        Excluir
      </button>
    </div>
  </div>
</template>

<script>
import { show, destroy, updateField } from '@/utils/requests/httpUtils';
import { formatDateBr } from '@/utils/date/dateUtils'; 
import { BACKEND_URL, LEAD_URL, IMAGES_PATH } from '@/config/apiConfig';
import axios from 'axios';
import SelectEditableInput from '@/components/fields/select/SelectEditableInput.vue';
import TextEditableField from '@/components/fields/text/TextEditableField.vue';
import DateEditableInput from '@/components/fields/date/DateEditableInput.vue';
import ErrorMessage from '@/components/forms/messages/ErrorMessage.vue';
import AddMessage from '@/components/forms/messages/AddMessage.vue';

export default {
  name: "LeadShow",
  components: {
    SelectEditableInput,
    TextEditableField,
    DateEditableInput,
    ErrorMessage,
    AddMessage,
  },
  data() {
    return {
      lead: [],
      leadId: "",
      proposals: [],
      validationErrors: null,
      newPhoto: null,
      messageStatus: '',
      messageText: '',
      leadTypeOptions: [
        { value: 'client', label: 'Cliente' },
        { value: 'supplier', label: 'Fornecedor' },
        { value: 'partner', label: 'Parceiro' },
        { value: 'employee', label: 'Funcionário' },
        { value: 'freelancer', label: 'Freelancer' },
      ],
    };
  },
  computed: {
    totalProposalsValue() {
      let total = 0;
      if (this.lead.opportunities) {
        this.lead.opportunities.forEach(opportunity => {
          if (opportunity.proposals) {
            opportunity.proposals.forEach(proposal => {
              total += parseFloat(proposal.total_price) || 0;
            });
          }
        });
      }
      return total;
    },
    acceptedProposalsCount() {
      let count = 0;
      if (this.lead.opportunities) {
        this.lead.opportunities.forEach(opportunity => {
          if (opportunity.proposals) {
            count += opportunity.proposals.filter(proposal => 
              proposal.status === 'accepted'
            ).length;
          }
        });
      }
      return count;
    },
    pendingProposalsCount() {
      let count = 0;
      if (this.lead.opportunities) {
        this.lead.opportunities.forEach(opportunity => {
          if (opportunity.proposals) {
            count += opportunity.proposals.filter(proposal => 
              proposal.status === 'pendente' || proposal.status === 'em_analise'
            ).length;
          }
        });
      }
      return count;
    },
    totalProposalsCount() {
      let count = 0;
      if (this.lead.opportunities) {
        this.lead.opportunities.forEach(opportunity => {
          if (opportunity.proposals) {
            count += opportunity.proposals.length;
          }
        });
      }
      return count;
    },
    opportunitiesWithProposals() {
      if (!this.lead.opportunities) return [];
      return this.lead.opportunities.filter(opportunity => 
        opportunity.proposals && opportunity.proposals.length > 0
      );
    },
    statusTranslations() {
      return {
        'draft': 'rascunho',
        'submitted': 'enviada',
        'accepted': 'aceita',
        'rejected': 'rejeitada',
        'canceled': 'cancelada',        
      };
    },
    totalBalance() {
      let total = 0;
      if (this.lead.opportunities) {
        this.lead.opportunities.forEach(opportunity => {
          if (opportunity.proposals) {
            opportunity.proposals.forEach(proposal => {
              total += this.calculateBalance(proposal);
            });
          }
        });
      }
      return total;
    },
    urlImagePhoto() {
      return `${IMAGES_PATH}${this.lead.photo}`;
    },
  },
  methods: {
    formatDateBr,
    async getLead() {
      this.lead = await show('leads', this.leadId);
    },
    async updateLeadField(fieldName, newValue) {
      try {
        this.validationErrors = null; // Limpa erros anteriores
        const updatedLead = await updateField('leads', this.leadId, fieldName, newValue);
        this.lead[fieldName] = updatedLead[fieldName];
      } catch (error) {
        console.error(`Erro ao atualizar ${fieldName}:`, error);
        
        // Se for erro de validação (422), exibe as mensagens
        if (error.response && error.response.status === 422) {
          this.validationErrors = {
            errors: error.response.data.errors || {}
          };
          
          // Scroll para o topo para ver o erro
          window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
          alert(`Erro ao atualizar ${fieldName}`);
        }
      }
    },
    async updateLeadType(newValue) {
      await this.updateLeadField('category', newValue);
    },
    formatCurrency(value) {
      if (!value) return 'R$ 0,00';
      return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
      }).format(parseFloat(value));
    },
    getStatusClass(status) {
      const statusClasses = {
        'draft': '!bg-yellow-50 !text-yellow-700 !border-yellow-200',
        'submitted': '!bg-blue-50 !text-blue-700 !border-blue-200',
        'accepted': '!bg-green-100 !text-green-800 !border-green-300',
        'rejected': '!bg-red-50 !text-red-700 !border-red-200',
        'canceled': '!bg-gray-50 !text-gray-700 !border-gray-200',
      };
      return statusClasses[status] || '!bg-gray-100 !text-gray-800 !border-gray-300';
    },
    getShortDescription(description, maxLength = 100) {
      if (!description) return '---';
      if (description.length > maxLength) {
        return description.substring(0, maxLength) + '...';
      }
      return description;
    },
    translateStatus(status) {
      return this.statusTranslations[status] || status;
    },
    calculateBalance(proposal) {
      const totalPrice = parseFloat(proposal.total_price) || 0;
      const totalPaid = parseFloat(proposal.total_paid) || 0;
      return totalPrice - totalPaid;
    },
    setLeadId(leadId) {
      this.leadId = leadId;
    },
    async deleteLead() {
      this.response = await destroy('leads', this.leadId);
      this.$router.push({ name: "leadsIndex" });
    },
    async handlePhotoUpload() {
      this.newPhoto = this.$refs.photo.files[0];
      
      if (this.newPhoto) {
        // Validar tamanho do arquivo (máximo 2MB)
        const maxSize = 2 * 1024 * 1024; // 2MB em bytes
        if (this.newPhoto.size > maxSize) {
          this.messageStatus = 'error';
          this.messageText = 'Arquivo muito grande! O tamanho máximo é 2MB.';
          this.$refs.photo.value = ''; // Limpa o input
          this.newPhoto = null;
          return;
        }

        // Validar tipo de arquivo
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(this.newPhoto.type)) {
          this.messageStatus = 'error';
          this.messageText = 'Formato inválido! Use: JPG, PNG, GIF ou WEBP.';
          this.$refs.photo.value = ''; // Limpa o input
          this.newPhoto = null;
          return;
        }

        await this.submitFormPhoto();
      }
    },
    async submitFormPhoto() {
      let formData = new FormData();
      if (this.newPhoto) {
        formData.append('photo', this.newPhoto);
      }

      try {
        const response = await axios.post(`${BACKEND_URL}${LEAD_URL}/${this.leadId}/photo`, formData);
        this.lead.photo = response.data.data.photo;
        this.newPhoto = null;
        
        // Mensagem de sucesso
        this.messageStatus = 'success';
        this.messageText = 'Foto enviada com sucesso!';
      } catch (error) {
        console.error('Erro ao fazer upload da foto:', error);
        
        // Mensagem de erro com detalhes
        if (error.response && error.response.status === 422) {
          // Erro de validação
          const errors = error.response.data.errors;
          if (errors && errors.photo) {
            this.messageText = errors.photo[0];
          } else {
            this.messageText = 'Erro de validação. Verifique o arquivo.';
          }
        } else if (error.response && error.response.status === 413) {
          this.messageText = 'Arquivo muito grande! Máximo 2MB.';
        } else {
          this.messageText = 'Erro ao fazer upload da foto. Tente novamente.';
        }
        this.messageStatus = 'error';
      }
    },
    setMessageStatus(status) {
      this.messageStatus = status;
    },
  },
  async mounted() {
    this.setLeadId(this.$route.params.id);
    this.getLead();
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
p {
  text-align: left;
  font-weight: 400;
}
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: rgb(61, 61, 61);
}
a:link {
  text-decoration: none;
}
a:visited {
  text-decoration: none;
}
a:hover {
  text-decoration: none;
}
a:active {
  text-decoration: none;
}
.label {
  font-weight: 800;
}
.card {
  text-align: left;
  background-color: white;
  border-style: solid;
  border-width: 3px;
  border-color: #48d1cc;
  border-radius: 20px;
  padding-top: 10px;
  padding-left: 20px;
  padding-bottom: 10px;
}
.title {
  font-size: 32px;
  font-weight: 900;
  padding-top: 10px;
  padding-bottom: 10px;
}
.container-card {
  margin-left: 180px;
  margin-right: 180px;
  margin-bottom: 60px;
  margin-top: 60px;
}

.container {
  margin-left: 200px;
  margin-right: 180px;
  margin-bottom: 60px;
}

.myButton {
  border-width: 2px;
  border-style: solid;
  border-color: white;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  /* margin: 0 4px 0 4px; */
  color: white;
  font-weight: 800;
  /* width: 120px; */
}
.delete {
  background-color: #ffa1a1;
  border-color: #c82333;
  color: #c82333;
}
.delete:hover {
  background-color: #c82333;
  border-color: #c82333;
  color: white;
}

.photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.photo-container {
  width: 200px;
  height: 200px;
  border: 2px solid #48d1cc;
  border-radius: 50%;
  overflow: hidden;
  background-color: #f9fafb;
}
</style>
