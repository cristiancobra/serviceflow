<template>
  <ModalCard
    title="Novo Projeto"
    subtitle="Adicione um novo projeto ao sistema"
    icon="fa-solid fa-folder-open"
    size="lg"
    :compact="compact"
    @close="closeModal"
  >
    <add-message v-if="messageStatus" :messageStatus="messageStatus" :messageText="messageText" />

    <form id="projectCreateForm" @submit.prevent="submitForm" class="space-y-6">
      <!-- Nome -->
      <div>
        <label class="block text-sm font-semibold text-base-content mb-2" for="name">
          Nome do Projeto
        </label>
        <input
          class="w-full px-4 py-2 border border-base-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 outline-none"
          type="text"
          id="name"
          v-model="form.name"
          placeholder="Digite um nome para seu projeto"
        >
      </div>

      <!-- Descrição -->
      <div>
        <label class="block text-sm font-semibold text-base-content mb-2" for="description">
          Descrição
        </label>
        <input
          class="w-full px-4 py-2 border border-base-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 outline-none"
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

    <template #footer>
      <button
        type="button"
        class="px-6 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg font-semibold hover:bg-base-200 transition-colors"
        @click="closeModal"
      >
        Cancelar
      </button>
      <button
        type="submit"
        form="projectCreateForm"
        class="px-6 py-2 bg-primary hover:opacity-90 text-white rounded-lg font-semibold transition-colors flex items-center gap-2"
      >
        <font-awesome-icon icon="fa-solid fa-plus" />
        Criar Projeto
      </button>
    </template>
  </ModalCard>
</template>

<script>
import { BACKEND_URL, PROJECT_URL, TASK_STATUS_URL } from "@/config/apiConfig";
import AddMessage from "@/components/forms/messages/AddMessage.vue";
import axios from 'axios'
import CompaniesSelectInput from "@/components/forms/selects/CompaniesSelectInput.vue";
import TimeInput from "@/components/forms/inputs/time/TimeInput.vue";
import LeadsSelectInput from "@/components/forms/selects/LeadsSelectInput.vue";
import UsersSelectInput from "./selects/UsersSelectInput.vue";
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
    name: 'ProjectCreateForm',
    emits: ["new-project-event", "close"],
    props: {
        // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
        compact: {
            type: Boolean,
            default: false,
        },
    },
    components: {
        AddMessage,
        CompaniesSelectInput,
        TimeInput,
        LeadsSelectInput,
        UsersSelectInput,
        ModalCard,
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
            this.$emit("close");
        },
        async submitForm() {
            try {
                axios
                    .post(`${BACKEND_URL}${PROJECT_URL}`, this.form)
                    .then((response) => {
                        this.data = response.data.data;
                        this.newProjectEvent(this.data);
                        this.closeModal();
                    })
            } catch (error) {
                console.error(error);
                if (error.response && error.response.status === 422) {
                    this.messageStatus = "error";
                    this.messageText = "Erro ao criar projeto. Verifique os campos.";
                }
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
