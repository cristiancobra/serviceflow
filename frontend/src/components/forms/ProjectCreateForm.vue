<template>
    <div class="form-container">
        <AddMessage v-if="messageStatus" :messageStatus="messageStatus" :messageText="messageText">
        </AddMessage>


        <form @submit.prevent="submitForm">

            <div class='row'>
                <div class='col-2'>
                    <label class='labels' for='name'>
                        Nome
                    </label>
                </div>
                <div class='col-10'>
                    <input class="form-control" type='text' id='name' v-model='form.name'
                        placeholder='Digite um nome para seu projeto'>
                </div>
            </div>

            <div class='row'>
                <div class='col-2'>
                    <label class='labels' for='description'>
                        Descrição
                    </label>
                </div>
                <div class='col-10'>
                    <input class="form-control" type='text' id='description' v-model='form.description'
                        placeholder='Digite o nome do responsável por garantir a execução do projeto'>
                </div>
            </div>

            <div class='row mt-5 mb-5'>
                <div class="col">
                    <CompaniesSelectInput label="Empresa cliente" v-model="form.company_id" name="company_id"
                        :fieldsToDisplay="['business_name', 'legal_name']" fieldNull="Não possui / minha empresa" />
                </div>
                <div class="col">
                    <div class="col">
                        <UsersSelectInput label="Responsável" v-model="form.user_id" fieldsToDisplay="name"
                            autoSelect=true />
                    </div>
                </div>
            </div>

            <div class='row'>
                <div class='col'>
                    <LeadsSelectInput label="Contato" name="contact_id" v-model="form.contact_id" fieldsToDisplay="name"
                        fieldNull="Não possui" />
                </div>
                <div class="col">
                    <StatusLinearRadioInput :status="form.status" @status-change="updateFormStatus" />
                </div>
            </div>

            <div class="row mb-5 mt-5">
                <div class="col-md-4">
                    <TimeInput v-model="form.date_start" label="Início" name="date_start"
                        placeholder="início do prazo" :autoFillNow="true" />
                </div>

                <div class="col-md-4">
                    <TimeInput v-model="form.date_due" label="Prazo final" name="date_due"
                        placeholder="prazo final" />
                </div>

                <div class="col-md-4">
                    <TimeInput v-model="form.date_conclusion" label="Data de conclusão" name="date_conclusion"
                        placeholder="data quando a projeto foi finalizada" />
                </div>
            </div>

            <div class="row ms-5 me-5 mt-4 mb-2">
                <button type="submit" class="btn new">
                    Criar
                </button>
            </div>

        </form>

    </div>

</template>

<script>
import { BACKEND_URL, PROJECT_URL, TASK_STATUS_URL } from "@/config/apiConfig";
import AddMessage from "@/components/forms/messages/AddMessage.vue";
import axios from 'axios'
import CompaniesSelectInput from "@/components/forms/selects/CompaniesSelectInput.vue";
import TimeInput from "@/components/forms/inputs/time/TimeInput.vue";
import LeadsSelectInput from "@/components/forms/selects/LeadsSelectInput.vue";
import StatusLinearRadioInput from "@/components/forms/inputs/StatusLinearRadioInput.vue";
import UsersSelectInput from "./selects/UsersSelectInput.vue";

export default {
    name: 'ProjectCreateForm',
    emits: ["new-project-event"],
    components: {
        AddMessage,
        CompaniesSelectInput,
        TimeInput,
        LeadsSelectInput,
        StatusLinearRadioInput,
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
            }
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
        async submitForm() {
            try {
                axios
                    .post(`${BACKEND_URL}${PROJECT_URL}`, this.form)
                    .then((response) => {
                        this.data = response.data.data;
                        this.newProjectEvent(this.data);
                        this.messageStatus = "success";
                        this.messageText = "Projeto criado com sucesso!";
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
.container {
    border-style: solid;
    border-color: #FF3EB5;
    border-width: 2px;
    margin-left: 180px;
    margin-right: 180px;
    margin-bottom: 60px;
    margin-top: 60px;
    padding: 20px;
    border-radius: 16px;
    transition: all .5s;
    text-align: left;
    font-weight: 800;
}

.labels {
    text-align: left;
    margin-left: 0;
}

.new {
    background-color: #FF3EB5;
    color: white;
    font-weight: 800;
    padding: 10px 20px 10px 20px;
}

.new:hover {
    background-color: #ffbf00;
}
</style>