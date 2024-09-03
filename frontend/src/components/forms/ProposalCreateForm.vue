<template>
    <div>
        <button type="button" class="button button-new d-flex justify-content-center" @click="showModal"
            data-bs-toggle="modal" data-bs-target="#taskModal">
            <font-awesome-icon icon="fa-solid fa-plus" class="" />
        </button>

        <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="taskModalLabel">Nova proposta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" @click="closeModal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitForm">
                            <div class="row">
                                <div class="col-12">
                                    <TextInput class="text-start" label="Título" name="name" v-model="form.name"
                                        placeholder="título da oportunidade" />
                                </div>
                            </div>

                            <div class="row mb-4 mt-4">
                                <TextAreaInput class="text-start" label="Descrição:" name="description"
                                    v-model="form.description" placeholder="Detalhamento da oportunidade" :rows="4" />
                            </div>
                            <div class="row mb-4 mt-4">
                                <div class="col">
                                    <UsersSelectInput class="text-start" label="Responsável" v-model="form.user_id"
                                        fieldsToDisplay="name" autoSelect=true />
                                </div>
                                <div class="col">
                                    <div v-if="currentProject" class="d-flex justify-content-start">
                                        <label for="project" class="form-label">Projeto</label>
                                        <input type="hidden" id="project" name="project_id"
                                            v-model="currentProject.id" />
                                        <TextValue v-model="currentProject.name" class="selected" />
                                    </div>
                                    <div v-else>
                                        <ProjectsSelectInput label="Projeto" v-model="form.project_id"
                                            fieldsToDisplay="name" :autoSelect="false" fieldNull="Nenhum" />
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4 mt-4">
                                <div class="col-md-6">
                                    <DateInput class="text-start" v-model="form.date" label="Início"
                                        name="date_start" placeholder="início do prazo" :autoFillNow="true"
                                        @update="updateForm" />
                                </div>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" v-model="form.validity_days" name="duration"
                                        placeholder="validade da proposta em dias" />
                                </div>
                            </div>
                            <div v-if="services.length === 0" class="row mb-4 mt-4">
                                <p>
                                    Você ainda não possui serviços cadastrados.
                                </p>
                            </div>
                            <div v-else class="row mb-4 mt-4">
                                <div class="row mb-3">
                                    <p class="label">
                                        Serviços:
                                    </p>
                                </div>
                                <div class="row ps-5" v-for="service in services" :key="service.id">
                                    <div class="col-2">
                                        <input type="number" min="0" :id="service.id"
                                            v-model.number="service.quantity" placeholder="0"
                                            class="form-control text-end" />
                                    </div>
                                    <div class="col-7">
                                        <label :for="service.id">
                                            {{ service.name }}
                                        </label>
                                    </div>
                                    <div class="col-3 text-end">
                                        R$ {{ service.price }}
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                    @click="closeModal">Fechar</button>
                                <button type="submit" class="button-new" data-bs-dismiss="modal">criar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { index } from "@/utils/requests/httpUtils";
import { submitFormCreate } from "@/utils/requests/httpUtils";
import DateInput from "@/components/forms/inputs/date/DateInput.vue";
import TextAreaInput from "./inputs/textarea/TextAreaInput";
import TextInput from "./inputs/text/TextInput";
import UsersSelectInput from "./selects/UsersSelectInput.vue";

export default {
    components: {
        DateInput,
        TextAreaInput,
        TextInput,
        UsersSelectInput,
    },
    props: {
        opportunityId: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            form: {
                name: null,
                description: null,
                user_id: null,
                date_start: null,
                opportunity_id: this.opportunityId,
                validity_days: 30,
            },
            modal: true,
            services: [],
            selectedServices: [],
        };
    },
    methods: {
        index,
        submitFormCreate,
        closeModal() {
            this.modal = false;
        },
        async getServices() {
            this.services = await this.index("services");
        },
        showModal() {
            this.modal = true;
        },
        async submitForm() {
            this.form.services = this.services
                .filter(service => service.quantity > 0)
                .map(service => ({
                    id: service.id,
                    quantity: service.quantity,
                    price: service.price,
                }));
            const newProposal = await this.submitFormCreate("proposals", this.form);
            this.modal = false;
            this.$emit("new-proposal-event", newProposal);
            // this.isSuccess = true;
            // this.messageStatus = "success";
            // this.messageText = "Tarefa criada com sucesso!";
            // this.isError = false;
            // this.newCompanyEvent(this.data);
            // this.successMessage(this.data);
            // this.toggleTaskForm();
            // this.clearForm();
        }
    },
    mounted() {
        this.getServices();
    },
};
</script>