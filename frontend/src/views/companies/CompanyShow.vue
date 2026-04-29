<template>
  <div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Messages -->
    <add-message 
      :messageStatus="messageStatus" 
      :messageText="messageText"
      @update:messageStatus="setMessageStatus"
    />

    <!-- Header Card -->
    <div class="bg-white border-2 border-primary rounded-xl p-6 mb-8 shadow-lg">
      <div class="flex items-center mb-4">
        <font-awesome-icon icon="fas fa-briefcase" class="text-primary text-3xl mr-4" />
        <h1 class="text-2xl font-black text-gray-800">EMPRESA</h1>
      </div>
      <div class="text-3xl font-black text-gray-900 mb-2">
        <text-editable-field
          name="business_name"
          :modelValue="company.business_name"
          @save="(value) => updateCompany('business_name', value)"
          placeholder="Nome fantasia da empresa..."
        />
      </div>
      <p class="text-gray-600 text-lg">
        <text-editable-field
          name="legal_name"
          :modelValue="company.legal_name"
          @save="(value) => updateCompany('legal_name', value)"
          placeholder="Razão social..."
        />
      </p>
    </div>

    <!-- Photo Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">
        Logo/Foto
      </h2>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="flex justify-center items-center p-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
          <div class="photo-container">
            <img v-if="company.photo" class="photo" :src="urlImagePhoto" alt="Logo da Empresa">
            <div v-else class="flex items-center justify-center h-full text-gray-400">
              <font-awesome-icon icon="fas fa-building" class="text-6xl" />
            </div>
          </div>
        </div>
        <div class="space-y-4">
          <form @submit.prevent="submitFormPhoto">
            <div class="flex flex-col mb-4">
              <label for="photo" class="mb-2 text-sm font-medium text-gray-700">Logo/Foto:</label>
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

    <!-- Company Information Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">
        Informações da Empresa
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-id-card" class="text-cyan-500 mr-3 w-5" />
          <span class="font-bold mr-2">CNPJ:</span>
          <text-editable-field
            name="cnpj"
            :modelValue="company.cnpj"
            @save="(value) => updateCompany('cnpj', value)"
            placeholder="00.000.000/0000-00"
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-envelope" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Email:</span>
          <text-editable-field
            name="email"
            :modelValue="company.email"
            @save="(value) => updateCompany('email', value)"
            placeholder="email@empresa.com"
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-phone" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Telefone:</span>
          <text-editable-field
            name="phone"
            :modelValue="company.phone"
            @save="(value) => updateCompany('phone', value)"
            placeholder="(00) 0000-0000"
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-mobile-alt" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Celular:</span>
          <text-editable-field
            name="cel_phone"
            :modelValue="company.cel_phone"
            @save="(value) => updateCompany('cel_phone', value)"
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
            :modelValue="company.linkedin"
            @save="(value) => updateCompany('linkedin', value)"
            placeholder="https://linkedin.com/company/..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fab fa-facebook" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Facebook:</span>
          <text-editable-field
            name="facebook"
            :modelValue="company.facebook"
            @save="(value) => updateCompany('facebook', value)"
            placeholder="https://facebook.com/..."
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
            :modelValue="company.address"
            @save="(value) => updateCompany('address', value)"
            placeholder="Rua, número..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-building" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Complemento:</span>
          <text-editable-field
            name="complement"
            :modelValue="company.complement"
            @save="(value) => updateCompany('complement', value)"
            placeholder="Sala, andar..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-home" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Bairro:</span>
          <text-editable-field
            name="neighborhood"
            :modelValue="company.neighborhood"
            @save="(value) => updateCompany('neighborhood', value)"
            placeholder="Bairro..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-city" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Cidade:</span>
          <text-editable-field
            name="city"
            :modelValue="company.city"
            @save="(value) => updateCompany('city', value)"
            placeholder="Cidade..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-flag" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">Estado:</span>
          <text-editable-field
            name="state"
            :modelValue="company.state"
            @save="(value) => updateCompany('state', value)"
            placeholder="UF..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-globe" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">País:</span>
          <text-editable-field
            name="country"
            :modelValue="company.country"
            @save="(value) => updateCompany('country', value)"
            placeholder="País..."
          />
        </div>
        <div class="flex items-center text-gray-700">
          <font-awesome-icon icon="fas fa-mail-bulk" class="text-primary mr-3 w-5" />
          <span class="font-bold mr-2">CEP:</span>
          <text-editable-field
            name="zip_code"
            :modelValue="company.zip_code"
            @save="(value) => updateCompany('zip_code', value)"
            placeholder="00000-000"
          />
        </div>
      </div>
    </div>

    <!-- Delete Button -->
    <div class="flex justify-end">
      <button 
        @click="deleteCompany()"
        class="bg-red-100 border-2 border-red-600 text-red-600 font-bold px-6 py-2 rounded-full hover:bg-red-600 hover:text-white transition-colors duration-200"
      >
        Excluir
      </button>
    </div>
  </div>
</template>

<script>
import { BACKEND_URL, COMPANY_URL_PARAMENTER, COMPANY_URL, IMAGES_PATH } from "@/config/apiConfig";
import axios from "axios";
import { formatDateBr } from "@/utils/date/dateUtils";
import { formatDuration } from "@/utils/date/dateUtils";
import TextEditableField from "@/components/fields/text/TextEditableField.vue";
import AddMessage from "@/components/forms/messages/AddMessage.vue";

export default {
  components: { 
    TextEditableField,
    AddMessage 
  },
  name: "CompanyShow",
  data() {
    return {
      companies: [],
      company: {},
      companyId: "",
      newPhoto: null,
      messageStatus: '',
      messageText: '',
    };
  },
  computed: {
    urlImagePhoto() {
      return `${IMAGES_PATH}${this.company.photo}`;
    },
  },
  methods: {
    formatDateBr,
    formatDuration,
    getCompany() {
      axios
        .get(`${BACKEND_URL}${COMPANY_URL_PARAMENTER}${this.companyId}`)
        .then((response) => {
          this.company = response.data.data;
          this.companyLoaded = true;
        })
        .catch((error) => {
          console.error('Erro ao carregar empresa:', error);
        });
    },
    setcompanyId(companyId) {
      this.companyId = companyId;
    },
    async deleteCompany() {
      axios
        .delete(`${BACKEND_URL}${COMPANY_URL_PARAMENTER}${this.companyId}`)
        .then((response) => {
          this.data = response.data;
          // this.newcompanyEvent(this.data);
          const successMessage = "company excluído com sucesso";
          this.$router.push({
            name: "companiesIndex",
            query: { successMessage },
          });
        })
        .catch((error) => {
          console.error("Erro ao deletar company:", error);
          // Lidar com o erro, se necessário
        });
    },
    async updateCompany(fieldName, editedValue) {
      try {
        const updatedField = { [fieldName]: editedValue };

        const response = await axios.put(
          `${BACKEND_URL}${COMPANY_URL_PARAMENTER}${this.companyId}`,
          updatedField
        );

        this.company = response.data.data;
      } catch (error) {
        console.error("Erro ao atualizar a tarefa:", error);
      }
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
        const response = await axios.post(`${BACKEND_URL}${COMPANY_URL}/${this.companyId}/photo`, formData);
        this.company.photo = response.data.data.photo;
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

  mounted() {
    this.setcompanyId(this.$route.params.id);
    this.getCompany();
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
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
