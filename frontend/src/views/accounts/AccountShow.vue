<template>
    <div class="m-5">
        <form @submit.prevent="submitForm">
            <div class="row mt-5">
                <div class="col-6">
                    <label for="name">Name:</label>
                    <input type="text" id="name" v-model="account.name">
                </div>
                <div class="col-6">
                    <label for="email">Email:</label>
                    <input type="email" id="email" v-model="account.email">
                </div>
            </div>
            <div class="row mt-4 mb-5">
                <div class="col">
                    <button type="submit">Submit</button>
                </div>
            </div>
        </form>
        <div class="mt-5">
            <img :src="urlImageLogo" alt="Logo">
        </div>
        <form @submit.prevent="submitFormLogo">
            <div class="row mt-4">
                <label for="logo">Logo:</label>
                <input type="file" id="logo" ref="logo" @change="handleLogoUpload">
            </div>
            <div class="row mt-5">
                <div class="col">
                    <button type="submit">Submit</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import { show, submitFormCreate } from "@/utils/requests/httpUtils";
import { BACKEND_URL, ACCOUNT_URL, IMAGES_PATH } from "@/config/apiConfig";
import axios from "axios";

export default {
    data() {
        return {
            account: {
                logo: null,
                name: '',
                email: '',
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
            this.account = await show('accounts', accountId);
        },
        // submitForm() {
        //     let formData = new FormData();
        //     formData.append('name', this.name);
        //     formData.append('email', this.email);
        //     if (this.logo) {
        //         formData.append('logo', this.logo);
        //     }

        //     let accountId = this.$route.params.id;

        //     axios.patch(`${BACKEND_URL}${ACCOUNT_URL}/${accountId}`, formData)
        //         .then(response => {
        //             console.log(response);
        //         }).catch(error => {
        //             console.log(error);
        //         });
        // },
        async submitFormLogo() {
            let formData = new FormData();
            if (this.newLogo) {
                formData.append('logo', this.newLogo);
            }

            let accountId = this.$route.params.id;

            try {
                const response = await axios.post(`${BACKEND_URL}${ACCOUNT_URL}/${accountId}/logo`, formData);
                this.account = response.data.data;
            } catch (error) {
                console.error('There was an error uploading the logo:', error);
            }
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