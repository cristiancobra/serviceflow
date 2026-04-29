<template>
    <div class="page-container">
        <div class="page-header">
            <div class="page-title">
                <font-awesome-icon icon="fa-solid fa-user" class="page-icon" />
                <h1>Perfil do Usuário</h1>
            </div>
        </div>

        <section class="section-container">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div class="mb-4">
                        <TextEditableField 
                            name="name" 
                            v-model="user.name" 
                            placeholder="nome do usuário"
                            label="Nome do usuário:" 
                            @save="updateUser('name', $event)" 
                        />
                    </div>
                    <div class="mb-4">
                        <TextEditableField 
                            name="email" 
                            v-model="user.email" 
                            placeholder="email do usuário"
                            label="Email:" 
                            @save="updateUser('email', $event)" 
                        />
                    </div>
                    <div class="mb-4">
                        <TextEditableField 
                            name="phone" 
                            v-model="user.phone" 
                            placeholder="telefone do usuário"
                            label="Telefone:" 
                            @save="updateUser('phone', $event)" 
                        />
                    </div>
                    <div class="mb-4">
                        <TextEditableField 
                            name="address" 
                            v-model="user.address" 
                            placeholder="Endereço do usuário"
                            label="Endereço:" 
                            @save="updateUser('address', $event)" 
                        />
                    </div>
                    <div class="mb-4">
                        <TextEditableField 
                            name="address_city" 
                            v-model="user.address_city" 
                            label="Cidade:"
                            placeholder="Cidade:" 
                            @save="updateUser('address_city', $event)" 
                        />
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="flex justify-center items-center p-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <div class="photo-container">
                            <img class="photo" :src="urlImagePhoto" alt="Foto">
                        </div>
                    </div>
                    <form @submit.prevent="submitFormPhoto" class="space-y-4">
                        <div class="flex flex-col">
                            <label for="photo" class="mb-2 text-sm font-medium text-gray-700">Foto:</label>
                            <input 
                                type="file" 
                                id="photo" 
                                ref="photo" 
                                @change="handlePhotoUpload"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            >
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
        </section>
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
}
</style>