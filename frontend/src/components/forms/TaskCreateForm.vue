<template>
  <div class="container">
    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <!-- <div id="form" class="container"> -->
    <form class="row" @submit.prevent="submitForm">
      <div class="col-12">
        <TextInput
          size="full"
          label="Nome"
          type="text"
          name="name"
          v-model="form.name"
          placeholder="nome da tarefa"
        />
      </div>

      <div class="col-12">
        <TextInput
          size="full"
          label="Descrição"
          type="text"
          name="description"
          v-model="form.description"
          placeholder="descrição detalhada da tarefa"
        />
      </div>

      <div class="col-md-4">
        <SelectInput
          label="Empresa cliente"
          name="company_id"
          v-model="form.company_id"
          placeholder="Selecione a empresa"
          :items="companies"
          :fieldToDisplay="['business_name', 'legal_name']"
        />
      </div>
      <div class="col-2 d-flex align-items-center justify-content-start">
        <button class="button-new" @click="toggleCompany()">+ criar</button>
      </div>

      <div class="col-md-4">
        <SelectInput
          label="Contato"
          name="contact_id"
          v-model="form.contact_id"
          placeholder="Selecione o contato"
          :items="leads"
          fieldToDisplay="name"
        />
      </div>
      <div class="col-2 d-flex align-items-center justify-content-start">
        <button class="button-new" @click="toggleLead()">+ criar</button>
      </div>

      <div :class="{ hidden: isActiveCompany }">
        <CompanyCreateForm @new-company-event="addCompanyCreated($event)" />
      </div>
      <div :class="{ hidden: isActiveLead }">
        <LeadCreateForm @new-lead-event="addLeadCreated($event)" />
      </div>

      <div class="col-md-4">
        <SelectInput
          label="Responsável"
          name="user_id"
          v-model="form.user_id"
          placeholder="Digite o nome do responsável pela execução da tarefa"
          :items="users"
          fieldToDisplay="name"
        />
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2 label-col">
            <label class="labels" for="user_id"> Responsável </label>
          </div>
          <div class="col-10">
            <input
              class="form-control"
              type="text"
              id="user_id"
              v-model="form.user_id"
              placeholder="Digite o nome do responsável pela execução da tarefa"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row">
          <div class="col-2 label-col">
            <label class="labels" for="date_start"> Início </label>
          </div>
          <div class="col-2">
            <input
              class="form-control"
              type="date"
              id="date_start"
              v-model="form.date_start"
            />
          </div>

          <div class="col-2 label-col">
            <label class="labels" for="date_due"> Prazo final </label>
          </div>
          <div class="col-2">
            <input
              class="form-control"
              type="date"
              id="date_due"
              v-model="form.date_due"
            />
          </div>

          <div class="col-2 label-col">
            <label class="labels" for="date_conclusion">
              Data de conclusão
            </label>
          </div>
          <div class="col-2">
            <input
              class="form-control"
              type="date"
              id="date_conclusion"
              v-model="form.date_conclusion"
            />
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="row mt-4 mb-4">
          <div class="col-2 label-col">
            <label class="labels" for="status"> Prioridade </label>
          </div>
          <div class="col-4">
            <PrioritySelectRadio
              :form="form"
              @priority-change="updateFormPriority"
            />
          </div>
          <div class="col-2 label-col">
            <label class="labels" for="status"> Situação </label>
          </div>
          <div class="col-4">
            <StatusLinearRadios
              :form="form"
              @status-change="updateFormStatus"
            />
          </div>
        </div>
      </div>

      <div class="row ms-5 me-5 mt-5 mb-2">
        <button type="submit" class="button-new">criar</button>
      </div>
    </form>
    <!-- </div> -->
  </div>
</template>

<script>
import axios from "axios";
import LeadCreateForm from "./LeadCreateForm.vue";
import CompanyCreateForm from "./CompanyCreateForm.vue";
import PrioritySelectRadio from "../buttons/PrioritySelectRadio.vue";
import StatusLinearRadios from "../buttons/StatusLinearRadios.vue";
import SelectInput from "./inputs/SelectInput";
import TextInput from "./inputs/TextInput";
import ErrorMessage from "./messages/ErrorMesssage.vue";
import SuccessMessage from "./messages/SuccessMessage.vue";

export default {
  name: "TaskCreateForm",
  emits: ["new-task-event"],
  components: {
    LeadCreateForm,
    CompanyCreateForm,
    PrioritySelectRadio,
    StatusLinearRadios,
    ErrorMessage,
    SuccessMessage,
    SelectInput,
    TextInput,
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
      isActiveCompany: true,
      isActiveLead: true,
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
    clearForm() {
      this.form.name = null;
      this.form.description = null;
      this.form.company_id = null;
      this.form.contact_id = null;
      this.form.user_id = null;
      this.form.date_start = null;
      this.form.date_due = null;
      this.form.date_conclusion = null;
      this.form.priority = "medium";
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
    async submitForm() {
      axios
        .post("http://localhost:8191/api/tasks", this.form)
        .then((response) => {
          this.newTask = response.data.task;
          this.newTaskEvent(this.newTask);
          this.clearForm();
        });
    },
    newTaskEvent(data) {
      this.$emit("new-task-event", data);
    },
    toggleCompany() {
      this.isActiveCompany = !this.isActiveCompany;
    },
    toggleLead() {
      this.isActiveLead = !this.isActiveLead;
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
.container {
  border-style: solid;
  border-color: #ff3eb5;
  border-width: 2px;
  margin-left: 180px;
  margin-right: 180px;
  margin-bottom: 60px;
  margin-top: 60px;
  padding: 20px;
  border-radius: 16px;
  transition: all 0.5s;
  text-align: left;
  font-weight: 800;
}
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

.row {
  margin-top: 1vh;
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