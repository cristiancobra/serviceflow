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
                            <label class='labels' for='comments'>
                                Observações
                            </label>
                        </div>
                        <div class='col-10'>
                            <input class="form-control" type='text' id='comments' v-model='form.comments' placeholder='Digite o nome do responsável por garantir a execução do projeto'>
                        </div>
                    </div>
                </div>

                <div class='form-group'>
                    <div class='row'>
                        <div class='col-2'>
                            <label class='labels' for='email'>
                                Email
                            </label>
                        </div>
                        <div class='col-10'>
                            <input class="form-control" type='text' id='email' v-model='form.email' placeholder='Digite o nome da empresa'>
                        </div>
                    </div>
                </div>

                <div class='form-group'>
                    <div class='row'>
                        <div class='col-2'>
                            <label class='labels' for='cel_phone'>
                                Celular
                            </label>
                        </div>
                        <div class='col-10'>
                            <input class="form-control" type='text' id='cel_phone' v-model='form.cel_phone' placeholder='Digite o nome da pessoa que solicitou o projeto'>
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
import axios from 'axios'

export default {
    name: 'LeadCreateForm',
    emits: ["new-lead-event"],
    data() {
        return {
            allStatus: [],
            message: null,
            data: [],
            form : {
                name: null,
                comments: null,
                email: null,
                cel_phone: null,
                user_id: null,
            }
        }
    },
    methods: {
        // getLeadsStatus() {
        //     axios
        //         .get("http://localhost:8191/api/leads/status")
        //         .then((response) => {
        //             this.allStatus = response.data;
        //         })
        //         .catch((error) => console.log(error));
        // },
        async submitForm() {
            axios
                .post('http://localhost:8191/api/leads', this.form)
                .then((response) => {
                    this.data = response.data;
                    this.newLeadEvent(this.data);
                })        
        },
        newLeadEvent (data){
            this.$emit('new-lead-event', data)
        },
    },
    // mounted() {
    //     this.getLeadsStatus();
    // },
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
</style>