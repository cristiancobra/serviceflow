<template>
    <div>
        <AddMessage v-if="messageStatus" :messageStatus="messageStatus" :messageText="messageText">
        </AddMessage>

        <button 
            type="button" 
            class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg"
            @click="openModal"
        >
            <font-awesome-icon icon="fa-solid fa-plus" class="mr-2" />
            Novo Projeto
        </button>

        <!-- Modal -->
        <div v-if="isModalVisible" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.25)">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="sticky top-0 bg-gradient-to-r from-purple-50 to-purple-25 border-b border-gray-200 px-8 py-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">Novo Projeto</h3>
                        <p class="text-gray-600 text-sm mt-1">Adicione um novo projeto ao sistema</p>
                    </div>
                    <button
                        type="button"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                        @click="closeModal"
                    >
                        <font-awesome-icon icon="fa-solid fa-xmark" class="text-2xl" />
                    </button>
                </div>

                <!-- Body -->
                <div class="px-8 py-6">
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <!-- Nome -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2" for="name">
                                Nome do Projeto
                            </label>
                            <input 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 outline-none"
                                type="text" 
                                id="name" 
                                v-model="form.name"
                                placeholder="Digite um nome para seu projeto"
                            >
                        </div>

                        <!-- Descrição -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2" for="description">
                                Descrição
                            </label>
                            <input 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 outline-none"
                                type="text" 
                                id="description" 
                                v-model="form.description"
                                placeholder="Descreva o objetivo do projeto"
                            >
                        </div>

                        <!-- Empresa e Responsável -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <CompaniesSelectInput 
                                    label="Empresa cliente" 
                                    v-model="form.company_id"
                                    name="company_id" 
                                    :fieldsToDisplay="['business_name', 'legal_name']"
                                    fieldNull="Não possui / minha empresa" 
                                />
                            </div>
                            <div>
                                <UsersSelectInput 
                                    label="Responsável" 
                                    v-model="form.user_id"
                                    fieldsToDisplay="name" 
                                    autoSelect="true" 
                                />
                            </div>
                        </div>

                        <!-- Contato -->
                        <div>
                            <LeadsSelectInput 
                                label="Contato" 
                                name="contact_id" 
                                v-model="form.contact_id"
                                fieldsToDisplay="name" 
                                fieldNull="Não possui" 
                            />
                        </div>

                        <!-- Datas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <TimeInput 
                                    v-model="form.date_start" 
                                    label="Data de Início" 
                                    name="date_start"
                                    placeholder="Início do prazo" 
                                    :autoFillNow="true" 
                                />
                            </div>
                            <div>
                                <TimeInput 
                                    v-model="form.date_due" 
                                    label="Prazo Final" 
                                    name="date_due"
                                    placeholder="Prazo final" 
                                />
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-8 py-4 flex justify-end gap-3">
                    <button 
                        type="button" 
                        class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                        @click="closeModal"
                    >
                        Cancelar
                    </button>
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-purple-600 to-purple-800 text-white rounded-lg font-semibold hover:shadow-lg transition-all hover:-translate-y-0.5"
                        @click="submitForm"
                    >
                        <font-awesome-icon icon="fa-solid fa-plus" class="me-2" />
                        Criar Projeto
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { BACKEND_URL, PROJECT_URL, TASK_STATUS_URL } from "@/config/apiConfig";
import AddMessage from "@/components/forms/messages/AddMessage.vue";
import axios from 'axios'
import CompaniesSelectInput from "@/components/forms/selects/CompaniesSelectInput.vue";
import TimeInput from "@/components/forms/inputs/time/TimeInput.vue";
import LeadsSelectInput from "@/components/forms/selects/LeadsSelectInput.vue";
import UsersSelectInput from "./selects/UsersSelectInput.vue";

export default {
    name: 'ProjectCreateForm',
    emits: ["new-project-event"],
    components: {
        AddMessage,
        CompaniesSelectInput,
        TimeInput,
        LeadsSelectInput,
        UsersSelectInput
    },
    data() {
        return {
            allStatus: [],
            messageStatus: "",
            messageText: "",
            data: [],
            form: {
                name: null,
                description: null,
                company_id: null,
                contact_id: null,
                user_id: null,
                date_start: null,
                date_due: null,
            },
            isModalVisible: false,
        }
    },
    methods: {
        getProjectsStatus() {
            axios
                .get(`${BACKEND_URL}${TASK_STATUS_URL}`)
                .then((response) => {
                    this.allStatus = response.data;
                })
                .catch((error) => console.log(error));
        },
        closeModal() {
            this.isModalVisible = false;
        },
        openModal() {
            this.isModalVisible = true;
        },
        async submitForm() {
            try {
                axios
                    .post(`${BACKEND_URL}${PROJECT_URL}`, this.form)
                    .then((response) => {
                        this.data = response.data.data;
                        this.newProjectEvent(this.data);
                        this.messageStatus = "success";
                        this.messageText = "Projeto criado com sucesso!";
                        this.closeModal();
                    })
            } catch (error) {
                console.error(error);
                if (error.response && error.response.status === 422) {
                    this.isError = true;
                    // this.isSuccess = false;
                    this.messageStatus = "error";
                    this.messageText = "Erro ao criar tarefa. Verifique os campos.";
                    // this.formResponse = error.response.data;
                }
                // if (!error.response) {
                //     this.formResponse =
                //         "Ocorreu um erro ao enviar o formulário. Tente novamente.";
                // }
            }
        },
        newProjectEvent(data) {
            this.$emit('new-project-event', data)
        },
        updateFormStatus(newStatus) {
            this.form.status = newStatus;
        },
    },
    mounted() {
        this.getProjectsStatus();
    },
};
</script>

<style scoped>
/* Estilos adicionais se necessário */
</style>