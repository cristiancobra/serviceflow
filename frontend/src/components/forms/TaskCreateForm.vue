<template>
  <div class="form-container">
    <AddMessage v-if="messageStatus" :messageStatus="messageStatus" :messageText="messageText">
    </AddMessage>

    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <form @submit.prevent="submitForm">
      <div class="row">
        <div class="col-12">
          <TextInput label="Nome" type="text" name="name" v-model="form.name" placeholder="nome da tarefa" />
        </div>
      </div>

      <div class="row">
        <TextAreaInput label="Descrição:" name="description" v-model="form.description"
          placeholder="Detalhamento da tarefa" :rows="5" />
      </div>

      <div class="row mb-5 mt-5">
        <div class="col-md-4">
          <CompaniesSelectInput label="Empresa cliente" name="company_id" v-model="form.company_id"
            :fieldsToDisplay="['business_name', 'legal_name']" fieldNull="Não possui / minha empresa" />
        </div>
        <div class="col-2 d-flex align-items-center justify-content-start">
          <button type="button" class="button-new" @click="toggleCompany()">
            + criar
          </button>
        </div>

        <div class="col-md-4">
          <LeadsSelectInput label="Contato" name="contact_id" v-model="form.contact_id" fieldsToDisplay="name"
            fieldNull="Não possui" />
        </div>
        <div class="col-2 d-flex align-items-center justify-content-start">
          <button type="button" class="button-new" @click="toggleLead()">
            + criar
          </button>
        </div>
      </div>

      <div v-if="isActiveCompany">
        <CompanyCreateForm @new-company-event="addCompanyCreated" />
      </div>
      <div v-if="isActiveLead">
        <LeadCreateForm @new-lead-event="addLeadCreated" />
      </div>

      <div class="row mb-5 mt-5">
        <div class="col">
          <UsersSelectInput label="Responsável" v-model="form.user_id" fieldsToDisplay="name" autoSelect=true />
        </div>
        <div class="col">
          <ProjectsSelectInput label="Projeto" v-model="form.project_id" fieldsToDisplay="name" autoSelect=false />
        </div>
      </div>

      <div class="row mb-5 mt-5">
        <div class="col-md-4">
          <DateInput v-model="form.date_start" label="Início" name="date_start" placeholder="início do prazo"
            :autoFillNow="true" @update="updateForm" />
        </div>

        <div class="col-md-4">
          <DateInput v-model="form.date_due" label="Prazo final" name="date_due" placeholder="prazo final" 
          @update="updateForm" />
        </div>

        <div class="col-md-4">
          <DateInput v-model="form.date_conclusion" label="Data de conclusão" name="date_conclusion"
            placeholder="data quando a tarefa foi finalizada"  @update="updateForm" />
        </div>
      </div>

      <div class="row mb-5 mt-5">
        <div class="col">
          <PrioritySelectRadioInput :priority="form.priority" @priority-change="updateFormPriority" />
        </div>
        <div class="col">
          <StatusLinearRadioInput :status="form.status" @status-change="updateFormStatus" />
        </div>
      </div>

      <div class="row ms-auto me-auto mt-5 mb-5">
        <button type="submit" class="button-new">criar</button>
      </div>
    </form>
  </div>
</template>

<script>
import {
  BACKEND_URL,
  TASK_URL,
  TASK_STATUS_URL,
} from "@/config/apiConfig";
import AddMessage from "@/components/forms/messages/AddMessage.vue";
import axios from "axios";
import DateInput from "./inputs/date/DateInput";
import CompanyCreateForm from "./CompanyCreateForm.vue";
import CompaniesSelectInput from "./selects/CompaniesSelectInput.vue";
import LeadCreateForm from "./LeadCreateForm.vue";
import LeadsSelectInput from "./selects/LeadsSelectInput.vue";
import PrioritySelectRadioInput from "./inputs/PrioritySelectRadioInput.vue";
import StatusLinearRadioInput from "./inputs/StatusLinearRadioInput.vue";
import TextInput from "./inputs/text/TextInput";
import TextAreaInput from "./inputs/TextAreaInput";
import UsersSelectInput from "./selects/UsersSelectInput.vue";
import ErrorMessage from "./messages/ErrorMesssage.vue";
import SuccessMessage from "./messages/SuccessMessage.vue";
import ProjectsSelectInput from "./selects/ProjectsSelectInput.vue";

export default {
  name: "TaskCreateForm",
  emits: ["new-task-event"],
  components: {
    AddMessage,
    CompanyCreateForm,
    CompaniesSelectInput,
    DateInput,
    LeadsSelectInput,
    LeadCreateForm,
    PrioritySelectRadioInput,
    ProjectsSelectInput,
    StatusLinearRadioInput,
    ErrorMessage,
    SuccessMessage,
    TextInput,
    TextAreaInput,
    UsersSelectInput,
  },
  data() {
    return {
      allStatus: [],
      message: null,
      newTask: null,
      companies: [],
      leads: [],
      users: [],
      data: [],
      isActiveCompany: false,
      isActiveLead: false,
      form: {
        name: null,
        description: null,
        company_id: null,
        contact_id: null,
        user_id: null,
        project_id: null,
        date_start: null,
        date_due: null,
        date_conclusion: null,
        status: "to-do",
        priority: "medium",
      },
      messageStatus: "",
      messageText: "",
    };
  },
  methods: {
    addLeadCreated(newLead) {
      this.leads.push(newLead.lead);
      !this.toggleLead();
      this.form.contact_id = newLead.lead.id;
    },
    // addCompanyCreated(newCompany) {
    //   this.companies.push(newCompany.company);
    //   this.getCompanies();
    // },
    updateForm(field, value) {
      this.form[field] = value;
    },
    
    clearForm() {
      this.form.name = "";
      this.form.description = "";
      this.form.company_id = null;
      this.form.contact_id = null;
      this.form.user_id = "";
      this.form.date_start = null;
      this.form.date_due = "";
      this.form.date_conclusion = "";
      this.status = "to-do";
      this.priority = "medium";
    },
    getTasksStatus() {
      axios
        .get(`${BACKEND_URL}${TASK_STATUS_URL}`)
        .then((response) => {
          this.allStatus = response.data;
        })
        .catch((error) => console.log(error));
    },
    async submitForm() {
      try {
        const response = await axios.post(
          `${BACKEND_URL}${TASK_URL}`,
          this.form
        );
        this.newTask = response.data.data;
        this.$emit("new-task-event", this.newTask);
        // this.isSuccess = true;
        this.messageStatus = "success";
        this.messageText = "Tarefa criada com sucesso!";
        this.isError = false;
        // this.newCompanyEvent(this.data);
        // this.successMessage(this.data);
        // this.toggleTaskForm();
        this.clearForm();
      } catch (error) {
        console.error(error);
        if (error.response && error.response.status === 422) {
          this.isError = true;
          // this.isSuccess = false;
          this.messageStatus = "error";
          this.messageText = "Erro ao criar tarefa. Verifique os campos.";
          this.formResponse = error.response.data;
          console.log(error.response.data);
        }
        if (!error.response) {
          this.formResponse =
            "Ocorreu um erro ao enviar o formulário. Tente novamente.";
        }
      }
    },
    toggleCompany() {
      this.isActiveCompany = !this.isActiveCompany;

      if (this.isActiveCompany) {
        this.isActiveLead = false;
      }
    },
    toggleLead() {
      this.isActiveLead = !this.isActiveLead;

      if (this.isActiveLead) {
        this.isActiveCompany = false;
      }
    },
    toggleTaskForm() {
      this.$emit("toggle-task-form"); // Emitir evento para o componente pai
    },
    updateFormPriority(newPriority) {
      this.form.priority = newPriority;
    },
    updateFormStatus(newStatus) {
      this.form.status = newStatus;
    },
  },
  mounted() {
    this.getTasksStatus();
  },
};
</script>

<style scoped>
.labels {
  text-align: left;
  margin-left: 0;
}

.label-col {
  display: flex;
  justify-content: center;
  align-items: center;
}

.radio-group {
  display: flex;
  gap: 10px;
  align-items: center;
}

/* status */
.to-do {
  border-radius: 16px 0px 0px 16px;
  border-color: var(--gray);
  color: var(--red);
}

.label-col {
  display: flex;
  justify-content: left;
  align-items: center;
}

.status {
  display: inline-block;
  padding: 8px 16px;
  font-size: 16px;
  background-color: #f0f0f0;
  border: 1px solid #ccc;
  cursor: pointer;
  user-select: none;
}
</style>