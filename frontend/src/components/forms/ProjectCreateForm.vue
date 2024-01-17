<template>

    <div>
        <div class="mt-5 mb-5 success">
            {{ message }}     
        </div>

        <div id ='form' class='container'>
            <form @submit.prevent="submitForm">

                <div class='form-group'>
                    <div class='row'>
                        <div class='col-2'>
                            <label class='labels' for='name'>
                                Nome
                            </label>
                        </div>
                        <div class='col-10'>
                            <input class="form-control" type='text' id='name' v-model='form.name' placeholder='Digite um nome para seu projeto'>
                        </div>
                    </div>
                </div>

                <div class='form-group'>
                    <div class='row'>
                        <div class='col-2'>
                            <label class='labels' for='description'>
                                Descrição
                            </label>
                        </div>
                        <div class='col-10'>
                            <input class="form-control" type='text' id='description' v-model='form.description' placeholder='Digite o nome do responsável por garantir a execução do projeto'>
                        </div>
                    </div>
                </div>

                <div class='form-group'>
                    <div class='row'>
                        <div class='col-2'>
                            <label class='labels' for='company_id'>
                                Empresa
                            </label>
                        </div>
                        <div class='col-10'>
                            <input class="form-control" type='text' id='company_id' v-model='form.company_id' placeholder='Digite o nome da empresa'>
                        </div>
                    </div>
                </div>

                <div class='form-group'>
                    <div class='row'>
                        <div class='col-2'>
                            <label class='labels' for='contact_id'>
                                Contato
                            </label>
                        </div>
                        <div class='col-10'>
                            <input class="form-control" type='text' id='contact_id' v-model='form.contact_id' placeholder='Digite o nome da pessoa que solicitou o projeto'>
                        </div>
                    </div>
                </div>

                <div class='form-group'>
                    <div class='row'>
                        <div class='col-2'>
                            <label class='labels' for='user_id'>
                                Responsável
                            </label>
                        </div>
                        <div class='col-10'>
                            <input class="form-control" type='text' id='user_id' v-model='form.user_id' placeholder='Digite o nome do responsável por garantir a execução do projeto'>
                        </div>
                    </div>
                </div>
                    
                <div class='form-group'>
                    <div class='row pt-4'>
                        <div class='col-2'>
                            <label class='labels' for='date_start'>
                                Início
                            </label>
                        </div>
                        <div class='col-2'>
                            <input class="form-control" type='date' id='date_start' v-model='form.date_start' placeholder='Digite o nome do responsável por garantir a execução do projeto'>
                        </div>
                        <div class='col-1'>
                            <label class='labels' for='date_due'>
                                Prazo final
                            </label>
                        </div>
                        <div class='col-2'>
                            <input class="form-control" type='date' id='date_due' v-model='form.date_due' placeholder='Digite o nome do responsável por garantir a execução do projeto'>
                        </div>
                        <div class='col-1'>
                            <label class='labels' for='status'>
                                Situação
                            </label>
                        </div>
                        <div class='col-2'>
                            <select class='form-select' id='status' name='status' v-model='form.status' aria-label="Selecione a situação do projeto">
                                <option v-for='(status,index) in allStatus' v-bind:key='index' :value='status'>
                                    {{ status }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row ms-5 me-5 mt-4 mb-2">
                    <button type="submit" class="btn new">
                        Criar
                    </button>
                </div>

            </form>
        </div>
    
    </div>

</template>

<script>
import { BACKEND_URL, PROJECT_URL, PROJECT_STATUS_URL } from "@/config/apiConfig";
import axios from 'axios'

export default {
    name: 'ProjectCreateForm',
    emits: ["new-project-event"],
    data() {
        return {
            allStatus: [],
            message: null,
            data: [],
            form : {
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
                .get(`${BACKEND_URL}${PROJECT_STATUS_URL}`)
                .then((response) => {
                    this.allStatus = response.data;
                })
                .catch((error) => console.log(error));
        },
        async submitForm() {
            axios
                .post(`${BACKEND_URL}${PROJECT_URL}`, this.form)
                .then((response) => {
                    this.data = response.data;
                    this.newProjectEvent(this.data);
                })        
        },
        newProjectEvent (data){
            this.$emit('new-project-event', data)
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