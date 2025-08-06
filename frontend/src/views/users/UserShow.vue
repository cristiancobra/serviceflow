<template>
    <div class="row m-5">
        <div class="col-8">
            <div class="row me-5 w-50">
                <div class="col">
                    <div class="row mt-4">
                        <faIcon icon="user" />
                        <TextEditableField name="name" v-model="user.name" placeholder="nome do usuário"
                            label="Nome do usuário:" @save="updateUser('name', $event)" />
                    </div>
                    <div class="row mt-4">
                        <TextEditableField name="email" v-model="user.email" placeholder="email do usuário"
                            label="Email:" @save="updateUser('email', $event)" />
                    </div>
                    <div class="row mt-4">
                        <TextEditableField name="phone" v-model="user.phone" placeholder="telefone do usuário"
                            label="Telefone:" @save="updateUser('phone', $event)" />
                    </div>
                    <div class="row mt-4">
                        <TextEditableField name="address" v-model="user.address" placeholder="Endereço do usuário"
                            label="Endereço:" @save="updateUser('address', $event)" />
                    </div>
                    <div class="row mt-4">
                        <TextEditableField name="address_city" v-model="user.address_city" label="Cidade:"
                            placeholder="Cidade:" @save="updateUser('address_city', $event)" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="photo-container">
                <img class="photo" :src="urlImagePhoto" alt="Foto">
            </div>
            <form @submit.prevent="submitFormPhoto">
                <div class="row mt-4">
                    <label for="photo">Foto:</label>
                    <input type="file" id="photo" ref="photo" @change="handlePhotoUpload">
                </div>
                <div class="row mt-4">
                    <div class="col">
                        <button type="submit">Enviar foto</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { show, submitFormCreate, updateField } from "@/utils/requests/httpUtils";
import { BACKEND_URL, USER_URL, IMAGES_PATH } from "@/config/apiConfig";
import axios from "axios";
import TextEditableField from "@/components/fields/text/TextEditableField.vue";


export default {
    data() {
        return {
            user: {
                email: '',
                name: '',
                phone: '',
                address: '',
                address_city: '',
                photo: null,
            },
            newPhoto: null,
        }
    },
    components: {
        TextEditableField,
    },
    methods: {
        submitFormCreate,
        async handlePhotoUpload() {
            this.newPhoto = this.$refs.photo.files[0];
            if (this.newPhoto) {
                await this.submitFormPhoto();
            }
        },
        async getUser() {
            let userId = this.$route.params.id;
            this.user = await show('users', userId);
            console.log(this.user);
        },
        async submitForm() {
            const { data, error } = await this.submitFormCreate("users", this.user);

            if (data) {
                this.user = data;
            }
            if (error) {
                this.errors = error;
            }
        },
        async submitFormPhoto() {
            let formData = new FormData();
            if (this.newPhoto) {
                formData.append('photo', this.newPhoto);
            }

            let userId = this.$route.params.id;

            try {
                const response = await axios.post(`${BACKEND_URL}${USER_URL}/${userId}/photo`, formData);
                this.$store.commit('setUserData', response.data.data); 
                this.user.photo = response.data.data.photo;
                this.newPhoto = null; 
            } catch (error) {
                console.error('There was an error uploading the photo:', error);
            }
        },
        async updateUser(fieldName, editedValue) {
            this.user = await updateField("users", this.user.id, fieldName, editedValue);
        },
    },
    computed: {
        urlImagePhoto() {
            return `${IMAGES_PATH}${this.user.photo}`;
        }
    },
    mounted() {
        this.getUser();
    }
}
</script>

<style scoped>
.photo {
    width: 100%;
    height: 100%;
}

.photo-container {
    width: 200px;
    height: 200px;
    border: 1px solid black;
    border-radius: 50%;
    overflow: hidden;
    margin: 0 auto;
}
</style>