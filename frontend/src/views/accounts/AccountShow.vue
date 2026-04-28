<template>
  <div class="page-container">
    <AddMessage
      :messageStatus="messageStatus"
      :messageText="messageText"
      @update:messageStatus="messageStatus = $event"
    />
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-cogs" class="page-icon" />
        <h1>Configurações</h1>
      </div>
    </div>

    <section class="section-container">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-6">
          <div class="mb-4">
            <TextEditableField
              name="name"
              v-model="account.name"
              placeholder="nome da conta"
              label="Nome da empresa:"
              @save="updateAccount('name', $event)"
            />
          </div>
          <div class="mb-4">
            <TextEditableField
              name="email"
              v-model="account.email"
              placeholder="email da conta"
              label="Email:"
              @save="updateAccount('email', $event)"
            />
          </div>
          <div class="flex items-end gap-2 mb-4">
            <div class="flex-1">
              <TextEditableField
                class="pt-3"
                name="cnpj"
                v-model="account.cnpj"
                placeholder="cnpj da empresa"
                label="CNPJ:"
                @save="updateAccount('cnpj', $event)"
              />
            </div>
            <button
              @click="copyCnpjWithSymbols"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md"
              title="Copiar com símbolos"
            >
              <font-awesome-icon icon="fa-solid fa-copy" />
            </button>
            <button
              @click="copyCnpjWithoutSymbols"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md"
              title="Copiar apenas números"
            >
              <font-awesome-icon icon="fa-solid fa-hashtag" />
            </button>
          </div>
          <div class="mb-4">
            <TextEditableField
              name="inscricao_municipal"
              v-model="account.inscricao_municipal"
              placeholder="inscricao_municipal da empresa"
              label="Insc. Municipal:"
              @save="updateAccount('inscricao_municipal', $event)"
            />
          </div>
          <div class="mb-4">
            <TextEditableField
              name="phone"
              v-model="account.phone"
              placeholder="telefone da empresa"
              label="Telefone:"
              @save="updateAccount('phone', $event)"
            />
          </div>
          <div class="mb-4">
            <TextEditableField
              name="address"
              v-model="account.address"
              placeholder="Endereço da empresa"
              label="Endereço:"
              @save="updateAccount('address', $event)"
            />
          </div>
          <div class="mb-4">
            <TextEditableField
              name="address_city"
              v-model="account.address_city"
              label="Cidade:"
              placeholder="Cidade:"
              @save="updateAccount('address_city', $event)"
            />
          </div>
        </div>
        <div class="space-y-6">
          <div class="flex justify-center items-center p-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
            <img 
              :src="urlImageLogo" 
              alt="Logo" 
              class="max-w-full h-auto max-h-64 object-contain"
            />
          </div>
          <form @submit.prevent="submitFormLogo" class="space-y-4">
            <div class="flex flex-col">
              <label for="logo" class="mb-2 text-sm font-medium text-gray-700">Logo:</label>
              <input
                type="file"
                id="logo"
                ref="logo"
                @change="handleLogoUpload"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
              />
            </div>
            <div class="flex justify-start">
              <button 
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md" 
                type="submit"
              >
                Enviar Logo
              </button>
            </div>
          </form>
          <div class="flex flex-col">
            <label for="primary_color" class="mb-2 text-sm font-medium text-gray-700">Cor Principal</label>
            <input
              type="color"
              name="primary_color"
              id="primary_color"
              class="h-12 w-full rounded-lg border border-gray-300 cursor-pointer"
              value="{{ old('primary_color', $account->primary_color) }}"
            />
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import {
  show,
  submitFormCreate,
  updateField,
} from "@/utils/requests/httpUtils";
import { BACKEND_URL, ACCOUNT_URL, IMAGES_PATH } from "@/config/apiConfig";
import AddMessage from "@/components/forms/messages/AddMessage.vue";
import axios from "axios";
import TextEditableField from "@/components/fields/text/TextEditableField.vue";

export default {
  data() {
    return {
      account: {
        cnpj: "",
        email: "",
        inscricao_municipal: "",
        logo: null,
        name: "",
        phone: "",
        address: "",
        address_city: "",
      },
      messageStatus: "",
      messageText: "",
      newLogo: null,
    };
  },
  components: {
    AddMessage,
    TextEditableField,
  },
  methods: {
    submitFormCreate,
    copyToClipboard(text) {
      navigator.clipboard
        .writeText(text)
        .then(() => {
          this.setMessageStatus("success");
        })
        .catch(() => {
          this.setMessageStatus("error");
        });
    },
    copyCnpjWithSymbols() {
      this.copyToClipboard(this.account.cnpj);
    },
    copyCnpjWithoutSymbols() {
      const cnpjWithoutSymbols = this.account.cnpj.replace(/\D/g, "");
      this.copyToClipboard(cnpjWithoutSymbols);
    },
    handleLogoUpload() {
      this.newLogo = this.$refs.logo.files[0];
    },
    async getAccount() {
      let accountId = this.$route.params.id;
      this.account = await show("accounts", accountId);
      console.log(this.account);
    },
    setMessageStatus(status) {
      this.messageStatus = status;

      if (status === "error") {
        this.messageText = "CNPJ não cadastrado";
      } else if (status === "success") {
        this.messageText = "CNPJ copiado com sucesso!";
      }
    },
    async submitForm() {
      const { data, error } = await this.submitFormCreate(
        "accounts",
        this.account
      );

      if (data) {
        this.account = data;
        // this.$emit("new-proposal-event", data);
      }
      if (error) {
        this.errors = error;
      }
    },
    async submitFormLogo() {
      let formData = new FormData();
      if (this.newLogo) {
        formData.append("logo", this.newLogo);
      }

      let accountId = this.$route.params.id;

      try {
        const response = await axios.post(
          `${BACKEND_URL}${ACCOUNT_URL}/${accountId}/logo`,
          formData
        );
        this.account.logo = response.data.data;
      } catch (error) {
        console.error("There was an error uploading the logo:", error);
      }
    },
    async updateAccount(fieldName, editedValue) {
      this.account = await updateField(
        "accounts",
        this.account.id,
        fieldName,
        editedValue
      );
    },
  },
  computed: {
    urlImageLogo() {
      return `${IMAGES_PATH}${this.account.logo}`;
    },
  },
  mounted() {
    this.getAccount();
  },
};
</script>