<template>
    <div class="row m-5">
        <div class="col-8">
            <div class="row me-5 w-50">
                <div class="col">
                    <div class="row mt-4">
                        <faIcon icon="user" />
                        <TextEditableField name="name" v-model="account.name" placeholder="nome da conta"
                            label="Nome da empresa:" @save="updateAccount('name', $event)" />
                    </div>
                    <div class="row mt-4">
                        <TextEditableField name="email" v-model="account.email" placeholder="email da conta"
                            label="Email:" @save="updateAccount('email', $event)" />
                    </div>
                    <div class="row mt-4">
                        <TextEditableField name="cnpj" v-model="account.cnpj" placeholder="cnpj da empresa"
                            label="CNPJ:" @save="updateAccount('cnpj', $event)" />
                    </div>
                    <div class="row mt-4">
                        <TextEditableField name="inscricao_municipal" v-model="account.inscricao_municipal"
                            placeholder="inscricao_municipal da empresa" label="Insc. Municipal:"
                            @save="updateAccount('inscricao_municipal', $event)" />
                    </div>
                    <div class="row mt-4">
                        <TextEditableField name="phone" v-model="account.phone" placeholder="telefone da empresa"
                            label="Telefone:" @save="updateAccount('phone', $event)" />
                    </div>
                    <div class="row mt-4">
                        <TextEditableField name="address" v-model="account.address" placeholder="Endereço da empresa"
                            label="Endereço:" @save="updateAccount('address', $event)" />
                    </div>
                    <div class="row mt-4">
                        <TextEditableField name="address_city" v-model="account.address_city" label="Cidade:"
                            placeholder="Cidade:" @save="updateAccount('address_city', $event)" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="mt-5">
                <img :src="urlImageLogo" alt="Logo">
            </div>
            <form @submit.prevent="submitFormLogo">
                <div class="row mt-4">
                    <label for="logo">Logo:</label>
                    <input type="file" id="logo" ref="logo" @change="handleLogoUpload">
                </div>
                <div class="row mt-4">
                    <div class="col">
                        <button type="submit">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { show, submitFormCreate, updateField } from "@/utils/requests/httpUtils";
import { BACKEND_URL, ACCOUNT_URL, IMAGES_PATH } from "@/config/apiConfig";
import axios from "axios";
import TextEditableField from "@/components/fields/text/TextEditableField.vue";

export default {
    data() {
        return {
            account: {
                cnpj: '',
                email: '',
                inscricao_municipal: '',
                logo: null,
                name: '',
                phone: '',
                address: '',
                address_city: '',
            },
            newLogo: null,
        }
    },
    components: {
        TextEditableField,
    },
    methods: {
        submitFormCreate,
        handleLogoUpload() {
            this.newLogo = this.$refs.logo.files[0];
        },
        async getAccount() {
            let accountId = this.$route.params.id;
            this.account = await show('accounts', accountId);
            console.log(this.account);
        },
        async submitForm() {
            const { data, error } = await this.submitFormCreate("accounts", this.account);

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
                formData.append('logo', this.newLogo);
            }

            let accountId = this.$route.params.id;

            try {
                const response = await axios.post(`${BACKEND_URL}${ACCOUNT_URL}/${accountId}/logo`, formData);
                this.account.logo = response.data.data;
            } catch (error) {
                console.error('There was an error uploading the logo:', error);
            }
        },
        async updateAccount(fieldName, editedValue) {
            this.account = await updateField("accounts", this.account.id, fieldName, editedValue);
        },
    },
    computed: {
        urlImageLogo() {
            return `${IMAGES_PATH}${this.account.logo}`;
        }
    },
    mounted() {
        this.getAccount();
    }
}
</script>