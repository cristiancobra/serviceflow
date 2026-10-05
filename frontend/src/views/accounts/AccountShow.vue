<template>
  <div class="page-container">
    <AddMessage
      :messageStatus="messageStatus"
      :messageText="messageText"
      @update:messageStatus="messageStatus = $event"
    />
    <PageHeader title="Configurações" icon="fa-solid fa-cogs" />

    <section class="mt-8 mb-20 px-8">
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
              class="px-4 py-2 bg-info hover:bg-info text-white rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md"
              title="Copiar com símbolos"
            >
              <font-awesome-icon icon="fa-solid fa-copy" />
            </button>
            <button
              @click="copyCnpjWithoutSymbols"
              class="px-4 py-2 bg-success hover:bg-success text-white rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md"
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
              name="pix_key"
              v-model="account.pix_key"
              placeholder="CNPJ, e-mail, +55DDDNUMERO ou chave aleatória"
              label="Chave Pix (QR Code das faturas):"
              @save="updateAccount('pix_key', $event)"
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
          <div class="mb-4">
            <TextEditableField
              name="zip_code"
              v-model="account.zip_code"
              label="CEP:"
              placeholder="00000-000"
              @save="updateZipCode"
            />
          </div>
          <div class="mb-4">
            <label for="theme_preference" class="mb-2 text-sm font-heavy block">Tema</label>
            <select
              id="theme_preference"
              v-model="account.theme_preference"
              @change="updateAccount('theme_preference', account.theme_preference)"
              class="w-full rounded-lg border border-base-300 px-3 py-2 bg-base-100 text-base-content text-sm"
            >
              <option value="light">Diurno</option>
              <option value="dark">Noturno</option>
              <option value="auto">Automático (18h às 6h)</option>
            </select>
          </div>
          <div class="mb-4 p-4 bg-base-200 rounded-lg">
            <label class="flex items-center gap-2 cursor-pointer mb-3">
              <input
                type="checkbox"
                v-model="account.is_mei"
                @change="updateAccount('is_mei', account.is_mei)"
                class="w-4 h-4"
              />
              <span class="text-sm font-medium text-base-content"
                >Empresa enquadrada como MEI</span
              >
            </label>
            <MoneyEditableField
              v-if="account.is_mei"
              name="mei_annual_limit"
              v-model="account.mei_annual_limit"
              label="Limite anual de faturamento do MEI:"
              @save="updateAccount('mei_annual_limit', $event)"
            />
          </div>
        </div>
        <div class="space-y-6">
          <div class="flex justify-center items-center p-6 bg-base-200 rounded-lg border-2 border-dashed border-base-300">
            <img 
              :src="urlImageLogo" 
              alt="Logo" 
              class="max-w-full h-auto max-h-64 object-contain"
            />
          </div>
          <form @submit.prevent="submitFormLogo" class="space-y-4">
            <div class="flex flex-col">
              <label for="logo" class="mb-2 text-sm font-medium">Logo:</label>
              <input
                type="file"
                id="logo"
                ref="logo"
                @change="handleLogoUpload"
                class="block w-full text-sm text-base-content/70 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-base-200 file:text-base-content hover:file:bg-base-300"
              />
            </div>
            <div class="flex justify-start">
              <button 
                class="px-6 py-2 bg-info hover:bg-info text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md" 
                type="submit"
              >
                Enviar Logo
              </button>
            </div>
          </form>
          <div class="flex flex-col">
            <label for="primary_color" class="mb-2 text-sm font-medium">Cor Principal</label>
            <input
              type="color"
              name="primary_color"
              id="primary_color"
              class="h-12 w-full rounded-lg border border-base-300 cursor-pointer"
              value="{{ old('primary_color', $account->primary_color) }}"
            />
          </div>
        </div>
      </div>
    </section>

    <section class="mt-8">
      <error-message v-if="validationErrors" :formResponse="validationErrors" />
      <AccountNfseSection
        v-if="account.id"
        :account="account"
        @update-field="updateAccount"
        @account-updated="account = $event"
      />
    </section>

    <!-- Seção de Departamentos -->
    <section class="mt-8 mb-20 px-8">
      <departments-manager />
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
import MoneyEditableField from "@/components/fields/number/MoneyEditableField.vue";
import DepartmentsManager from "@/components/lists/DepartmentsManager.vue";
import { mapMutations } from "vuex";
import PageHeader from "@/components/layout/PageHeader.vue";
import AccountNfseSection from "@/components/show/AccountNfseSection.vue";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";
import { fetchAddressByCep } from "@/utils/address/viaCepUtils";

export default {
  data() {
    return {
      account: {
        cnpj: "",
        email: "",
        inscricao_municipal: "",
        pix_key: "",
        logo: null,
        name: "",
        phone: "",
        address: "",
        address_city: "",
        zip_code: "",
        is_mei: false,
        mei_annual_limit: 0,
        theme_preference: "auto",
      },
      messageStatus: "",
      messageText: "",
      newLogo: null,
      validationErrors: null,
    };
  },
  components: {
    PageHeader,
    AccountNfseSection,
    ErrorMessage,
    AddMessage,
    TextEditableField,
    MoneyEditableField,
    DepartmentsManager,
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
      try {
        this.validationErrors = null;
        this.account = await updateField(
          "accounts",
          this.account.id,
          fieldName,
          editedValue
        );
      } catch (error) {
        console.error(`Erro ao atualizar ${fieldName}:`, error);
        this.validationErrors = {
          errors: error.response?.data?.errors || { [fieldName]: ["Erro ao salvar. Tente novamente."] },
        };
        return false;
      }

      if (fieldName === "theme_preference") {
        this.setThemePreference(this.account.theme_preference);
      }
      return true;
    },
    // O CEP preenche o código IBGE do município, exigido na NFS-e
    async updateZipCode(zipCode) {
      const saved = await this.updateAccount("zip_code", zipCode);
      if (!saved) return;

      const address = await fetchAddressByCep(zipCode);
      if (address?.ibgeCityCode && address.ibgeCityCode !== this.account.ibge_city_code) {
        await this.updateAccount("ibge_city_code", address.ibgeCityCode);
      }
    },
    ...mapMutations(["setThemePreference"]),
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