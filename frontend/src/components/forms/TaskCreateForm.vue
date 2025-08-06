<template>
  <div>
    <button type="button" class="button button-new" @click="openModal">
      <font-awesome-icon icon="fa-solid fa-plus" class="" />
    </button>

    <div v-if="isModalVisible" class="myModal">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <font-awesome-icon icon="fa-solid fa-tasks" class="icon primary" />
            <h5 class="modal-title" id="taskModalLabel">Nova tarefa</h5>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm">

              <div class="row">
                <div class="col-12">
                  <TextInput label="Nome" name="name" v-model="form.name" placeholder="nome da tarefa" />
                </div>
              </div>

              <div class="row mt-4">
                <TextAreaInput label="Descrição:" name="description" v-model="form.description"
                  placeholder="Detalhamento da tarefa" :rows="5" />
              </div>
              <div class="row mb-4 mt-4">
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

              <div class="row mb-4 mt-4">
                <div class="col">
                  <UsersSelectInput label="Responsável" v-model="form.user_id" fieldsToDisplay="name" autoSelect=true />
                </div>
              </div>

              <div class="row mb-4 mt-4">
                <div class="col-md-6">
                  <DateInput v-model="form.date_start" label="Início" name="date_start" placeholder="início do prazo"
                    :autoFillNow="true" @update="updateForm" />
                </div>

                <div class="col-md-6">
                  <DateInput v-model="form.date_due" label="Prazo final" name="date_due" placeholder="prazo final"
                    @update="updateForm" />
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click=closeModal>Fechar</button>
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
// import { inject } from "vue";
// import AddMessage from "@/components/forms/messages/AddMessage.vue";
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
      isActiveFormCompany: false,
      isActiveFormLead: false,
      isModalVisible: false,
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
    submitFormCreate,
    addLeadCreated(newLead) {
      this.leads.push(newLead.lead);
      !this.toggleLead();
      this.form.contact_id = newLead.lead.id;
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
    closeModal() {
      this.isModalVisible = false;
    },
    openModal() {
      this.isModalVisible = true;
    },
    async submitForm() {
      const { data, error } = await this.submitFormCreate("tasks", this.form);
      console.log('form', this.form);

      if (data) {
        this.messageStatus = "success";
        this.messageText = "Tarefa criada com sucesso!";
        this.isError = false;
        this.closeModal();
        this.clearForm();
        this.$emit("new-task-event", data);
      }
      if (error) {
        this.errors = error;
      }

    },
    toggleCompany() {
      this.isActiveFormCompany = !this.isActiveFormCompany;

      if (this.isActiveFormCompany) {
        this.isActiveFormLead = false;
      }
    },
    toggleLead() {
      this.isActiveFormLead = !this.isActiveFormLead;

      if (this.isActiveFormLead) {
        this.isActiveFormCompany = false;
      }
    },
    updateForm(field, value) {
      this.form[field] = value;
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
};
</script>