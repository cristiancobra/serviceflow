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
            <font-awesome-icon icon="fa-solid fa-tasks" class="icon pe-3 primary" />
            <h5 class="modal-title" id="taskModalLabel">Nova tarefa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" @click="closeModal"
              aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm">

              <div class="row">
                <div class="col-12">
                  <TextInput label="Nome" name="name" v-model="form.name" placeholder="nome da tarefa" />
                </div>
              </div>

              <div class="row mt-5">
                <TextAreaInput label="Descrição:" name="description" v-model="form.description"
                  placeholder="Detalhamento da tarefa" :rows="5" />
              </div>
              <div class="row mb-5 mt-5">
                <div class="col">
                  <div v-if="currentOpportunity">
                    <label for="opportunity" class="form-label">Oportunidade</label>
                    <input type="hidden" id="opportunity" name="opportunity_id" v-model="currentOpportunity.id" />
                    <TextValue v-model="currentOpportunity.name" class="selected" />
                  </div>
                  <div v-else>
                    <OpportunitiesSelectInput label="Oportunidade" v-model="form.opportunity_id" fieldsToDisplay="name"
                      :autoSelect="false" fieldNull="Nenhum" />
                  </div>
                </div>
                <div class="col">
                  <div v-if="currentProject">
                    <label for="project" class="form-label">Projeto</label>
                    <input type="hidden" id="project" name="project_id" v-model="currentProject.id" />
                    <TextValue v-model="currentProject.name" class="selected" />
                  </div>
                  <div v-else>
                    <ProjectsSelectInput label="Projeto" v-model="form.project_id" fieldsToDisplay="name"
                      :autoSelect="false" fieldNull="Nenhum" />
                  </div>
                </div>
              </div>

              <div class="row mb-5 mt-5">
                <div class="col">
                  <UsersSelectInput label="Responsável" v-model="form.user_id" fieldsToDisplay="name" autoSelect=true />
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
                    placeholder="data quando a tarefa foi finalizada" @update="updateForm" />
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
import {
  BACKEND_URL,
  TASK_URL,
  TASK_STATUS_URL,
} from "@/config/apiConfig";
// import { inject } from "vue";
// import AddMessage from "@/components/forms/messages/AddMessage.vue";
import axios from "axios";
import DateInput from "./inputs/date/DateInput";
// import ErrorMessage from "./messages/ErrorMesssage.vue";
import OpportunitiesSelectInput from "./selects/OpportunitiesSelectInput.vue";
import ProjectsSelectInput from "./selects/ProjectsSelectInput.vue";
// import SuccessMessage from "./messages/SuccessMessage.vue";
import TextAreaInput from "./inputs/textarea/TextAreaInput";
import TextInput from "./inputs/text/TextInput";
import TextValue from "../fields/text/TextValue";
import UsersSelectInput from "./selects/UsersSelectInput.vue";

export default {
  name: "TaskCreateForm",
  emits: ["new-task-event"],
  components: {
    // AddMessage,
    DateInput,
    // ErrorMessage,
    OpportunitiesSelectInput,
    ProjectsSelectInput,
    // SuccessMessage,
    TextAreaInput,
    TextInput,
    TextValue,
    UsersSelectInput,
  },
  data() {
    return {
      allStatus: [],
      companies: [],
      data: [],
      form: {
        company_id: null,
        contact_id: null,
        date_conclusion: null,
        date_due: null,
        date_start: null,
        description: null,
        name: null,
        priority: "medium",
        opportunity_id: null,
        project_id: null,
        status: "to-do",
        user_id: null,
      },
      isActiveCompany: false,
      isActiveLead: false,
      leads: [],
      message: null,
      messageStatus: "",
      messageText: "",
      newTask: null,
      // selectedProject: inject('currentProject'),
      users: [],
    };
  },
  inject: [
    'currentProject',
    'currentOpportunity',
  ],
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
        this.toggleTaskForm();
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
  watch: {
    currentOpportunity(newValue) {
      this.form.opportunity_id = newValue.id;
    },
    currentProject(newValue) {
      this.form.project_id = newValue.id;
    },
  },
  mounted() {
    this.getTasksStatus();
  },
};
</script>

<style scoped>
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

.selected {
  width: 100%;
  padding: 0.4rem;
  font-size: 1rem;
  border: 1px dashed var(--primary);
  border-radius: 4px;
  font-weight: 400;
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