<template>
    <div class="row m-5">
        <div class="col-8">
            <form @submit.prevent="submitForm">
                <div class="row me-5 w-50">
                    <div class="col">
                        <div class="row mt-4">
                            <label for="name">Name:</label>
                            <br>
                            <input type="text" id="name" v-model="form.name">
                        </div>
                        <div class="row mt-4">
                            <label for="email">Email:</label>
                            <br>
                            <input type="email" id="email" v-model="form.email">
                        </div>
                        <div class="row mt-4">
                            <label for="cnpj">CNPJ:</label>
                            <br>
                            <input type="text" id="cnpj" v-model="form.cnpj">
                        </div>
                        <div class="row mt-4">
                            <label for="inscricao_municipal">Inscrição municipal:</label>
                            <br>
                            <input type="text" id="inscricao_municipal" v-model="form.inscricao_municipal">
                        </div>
                        <div class="row mt-4">
                            <label for="phone">Telefone:</label>
                            <br>
                            <input type="text" id="phone" v-model="form.phone">
                        </div>
                        <div class="row mt-4">
                            <label for="address">Endereço:</label>
                            <br>
                            <textarea id="address" v-model="form.address"></textarea>
                        </div>
                        <div class="row mt-4">
                            <label for="address_city">Cidade:</label>
                            <br>
                            <input type="text" id="address_city" v-model="form.address_city">
                        </div>
                    </div>
                </div>
                <div class="row mt-4 mb-5">
                    <div class="col">
                        <button type="submit">Submit</button>
                    </div>
                </div>
            </form>
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
import { show, submitFormCreate } from "@/utils/requests/httpUtils";
import { BACKEND_URL, ACCOUNT_URL, IMAGES_PATH } from "@/config/apiConfig";
import axios from "axios";

export default {
    data() {
        return {
            form: {
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
    methods: {
        submitFormCreate,
        handleLogoUpload() {
            this.newLogo = this.$refs.logo.files[0];
        },
        async getAccount() {
            let accountId = this.$route.params.id;
            this.form = await show('accounts', accountId);
            console.log(this.form);
        },
        async submitForm() {
            const { data, error } = await this.submitFormCreate("accounts", this.form);

            if (data) {
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
                this.form.logo = response.data.data;
            } catch (error) {
                console.error('There was an error uploading the logo:', error);
            }
        },
    },
    computed: {
        urlImageLogo() {
            return `${IMAGES_PATH}${this.form.logo}`;
        }
    },
    mounted() {
        this.getAccount();
    }
}
</script>