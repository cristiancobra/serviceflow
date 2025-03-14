<template>
    <div>
        <button type="button" class="button button-new" @click="openModal">
            <font-awesome-icon icon="fa-solid fa-plus" class="" />
        </button>

        <div v-if="isModalVisible" class="myModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="taskModalLabel">Nova oportunidade</h5>
                        <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
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
                                <div class="col-md-4">
                                    <CompaniesSelectInput class="text-start" label="Empresa cliente" name="company_id"
                                        v-model="form.company_id" :fieldsToDisplay="['business_name', 'legal_name']"
                                        fieldNull="Não possui / minha empresa" />
                                </div>
                                <div class="col-2 d-flex align-items-center justify-content-start">
                                    <button type="button" class="button-new" @click="toggleCompany()">
                                        +
                                    </button>
                                </div>

                                <div class="col-md-4">
                                    <LeadsSelectInput class="text-start" label="Contato" name="lead_id"
                                        v-model="form.lead_id" fieldsToDisplay="name" fieldNull="Não possui" />
                                </div>
                                <div class="col-2 d-flex align-items-center justify-content-start">
                                    <button type="button" class="button-new" @click="toggleLead()">
                                        +
                                    </button>
                                </div>
                            </div>

                            <div v-if="isActiveCompany">
                                <CompanyCreateForm @new-company-event="addCompanyCreated" />
                            </div>
                            <div v-if="isActiveLead">
                                <LeadCreateForm @new-lead-event="addLeadCreated" />
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
                                    <DateInput class="text-start" v-model="form.date_start" label="Início"
                                        name="date_start" placeholder="início do prazo" :autoFillNow="true"
                                        @update="updateForm" />
                                </div>
                                <div class="col-md-6">
                                    <DateInput class="text-start" v-model="form.date_due" label="Prazo final"
                                        name="date_due" placeholder="prazo final" @update="updateForm" />
                                </div>
                            </div>


                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="closeModal">Fechar</button>
                                <button type="submit" class="button-new">criar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { submitFormCreate } from "@/utils/requests/httpUtils";
import CompaniesSelectInput from "@/components/forms/selects/CompaniesSelectInput";
import DateInput from "@/components/forms/inputs/date/DateInput.vue";
import LeadsSelectInput from "@/components/forms/selects/LeadsSelectInput.vue";
import TextAreaInput from "./inputs/textarea/TextAreaInput";
import TextInput from "./inputs/text/TextInput";
import UsersSelectInput from "./selects/UsersSelectInput.vue";

export default {
    components: {
        CompaniesSelectInput,
        DateInput,
        LeadsSelectInput,
        TextAreaInput,
        TextInput,
        UsersSelectInput,
    },
    data() {
        return {
            form: {
                name: null,
                description: null,
                company_id: null,
                lead_id: null,
                user_id: null,
                project_id: null,
                date_start: null,
                date_due: null,
                date_conclusion: null,
                priority: null,
                status: null,
            },
            isModalVisible: false,
            modal: true,
        };
    },
    methods: {
        submitFormCreate,
        closeModal() {
            this.isModalVisible = false;
        },
        openModal() {
            this.isModalVisible = true;
        },
        async submitForm() {            
            const { data, error } = await this.submitFormCreate("opportunities", this.form);

            if (data) {
                this.isModalVisible = false;
                this.$emit("new-opportunity-event", data);
            }
            if (error) {
                this.errors = error;
            }
        }
    },
};
</script>