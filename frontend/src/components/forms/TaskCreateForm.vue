<template>
  <div class="form-container">
    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <form @submit.prevent="submitForm">
      <div class="row">
        <div class="col-12">
          <TextInput
            label="Nome"
            type="text"
            name="name"
            v-model="form.name"
            placeholder="nome da tarefa"
          />
        </div>
      </div>

      <div class="row">
        <TextAreaInput
          label="Descrição:"
          name="description"
          v-model="form.description"
          placeholder="Detalhamento da tarefa"
          :rows="5"
        />
      </div>

      <div class="row">
        <div class="col-md-4">
          <SelectInput
            label="Empresa cliente"
            name="company_id"
            v-model="form.company_id"
            :items="companies"
            :fieldsToDisplay="['business_name', 'legal_name']"
            fieldNull="Não possui / minha empresa"
          />
        </div>
        <div class="col-2 d-flex align-items-center justify-content-start">
          <button type="button" class="button-new" @click="toggleCompany()">
            + criar
          </button>
        </div>

        <div class="col-md-4">
          <SelectInput
            label="Contato"
            name="contact_id"
            v-model="form.contact_id"
            :items="leads"
            fieldsToDisplay="name"
            fieldNull="Não possui"
          />
        </div>
        <div class="col-2 d-flex align-items-center justify-content-start">
          <button type="button" class="button-new" @click="toggleLead()">
            + criar
          </button>
        </div>
      </div>

      <div :class="{ hidden: !isActiveCompany }">
        <CompanyCreateForm @new-company-event="addCompanyCreated()" />
      </div>
      <div :class="{ hidden: !isActiveLead }">
        <LeadCreateForm @new-lead-event="addLeadCreated($event)" />
      </div>

      <div class="row">
        <div class="col-md-4">
          <SelectInput
            label="Responsável"
            name="user_id"
            v-model="form.user_id"
            :items="users"
            fieldsToDisplay="name"
          />
        </div>

        <div class="offset-2 col-md-6">
          <PrioritySelectRadioInput
            :form="form"
            @priority-change="updateFormPriority"
          />
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <TextInput
            label="Início"
            type="date"
            name="date_start"
            v-model="form.date_start"
            placeholder="início do prazo"
          />
        </div>

        <div class="col-md-4">
          <TextInput
            label="Prazo final"
            type="date"
            name="date_due"
            v-model="form.date_due"
            placeholder="final do prazo"
          />
        </div>

        <div class="col-md-4">
          <TextInput
            label="Data de conclusão"
            type="date"
            name="date_conclusion"
            v-model="form.date_conclusion"
            placeholder="data quando a tarefa foi finalizada"
          />
        </div>
      </div>

      <div class="col-md-12">
        <StatusLinearRadioInput
          :form="form"
          @status-change="updateFormStatus"
        />
      </div>

      <div class="row ms-auto me-auto mt-5 mb-5">
        <button type="submit" class="button-new">criar</button>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import LeadCreateForm from "./LeadCreateForm.vue";
import CompanyCreateForm from "./CompanyCreateForm.vue";
import PrioritySelectRadioInput from "./inputs/PrioritySelectRadioInput.vue";
import StatusLinearRadioInput from "./inputs/StatusLinearRadioInput.vue";
import SelectInput from "./inputs/SelectInput";
import TextInput from "./inputs/TextInput";
import TextAreaInput from "./inputs/TextAreaInput";
import ErrorMessage from "./messages/ErrorMesssage.vue";
import SuccessMessage from "./messages/SuccessMessage.vue";

export default {
  name: "TaskCreateForm",
  emits: ["new-task-event"],
  components: {
    LeadCreateForm,
    CompanyCreateForm,
    PrioritySelectRadioInput,
    StatusLinearRadioInput,
    ErrorMessage,
    SuccessMessage,
    SelectInput,
    TextInput,
    TextAreaInput,
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
        date_start: null,
        date_due: null,
        date_conclusion: null,
        status: "to-do",
        priority: "medium",
      },
    };
  },
  methods: {
    addLeadCreated(newLead) {
      this.leads.push(newLead.lead);
      !this.toggleLead();
      this.form.contact_id = newLead.lead.id;
    },
    addCompanyCreated() {
      this.getCompanies();
    },
    clearForm() {
      this.form.name = null;
      this.form.description = null;
      this.form.company_id = null;
      this.form.contact_id = null;
      this.form.user_id = null;
      this.form.date_start = null;
      this.form.date_due = null;
      this.form.date_conclusion = null;
      this.status = "to-do";
      this.priority = "medium";
    },
    getLeads() {
      axios
        .get("http://localhost:8191/api/leads")
        .then((response) => {
          this.leads = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    getCompanies() {
      axios
        .get("http://localhost:8191/api/companies")
        .then((response) => {
          this.companies = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    getTasksStatus() {
      axios
        .get("http://localhost:8191/api/tasks/status")
        .then((response) => {
          this.allStatus = response.data;
        })
        .catch((error) => console.log(error));
    },
    getUsers() {
      axios
        .get("http://localhost:8191/api/users")
        .then((response) => {
          this.users = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    newTaskEvent(data) {
      this.$emit("new-task-event", data);
    },
    async submitForm() {
      try {
        const response = await axios.post(
          "http://localhost:8191/api/tasks",
          this.form
        );
        this.data = response.data.data;
        // console.log(this.data);
        this.newTaskEvent(this.data);
        this.isSuccess = true;
        this.isError = false;
        // this.newCompanyEvent(this.data);
        // this.successMessage(this.data);
        this.toggleTaskForm();
        // this.clearForm();
      } catch (error) {
        console.error(error);
        if (error.response && error.response.status === 422) {
          this.isError = true;
          this.isSuccess = false;
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
    this.getLeads();
    this.getCompanies();
    this.getUsers();
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