<template>
  <ModalCard
    :title="cloneFrom ? 'Clonar Tarefa' : 'Nova Tarefa'"
    icon="fa-solid fa-tasks"
    :compact="compact"
    @close="closeModal"
  >
          <error-message v-if="formResponse" :formResponse="formResponse" />
          <form id="taskForm" @submit.prevent="submitForm">
            <div class="mb-4">
              <div class="form-control w-full">
                <label class="text-base-content" :for="name"> Nome da tarefa </label>
                <input
                  class="input input-bordered w-full"
                  type="text"
                  name="name"
                  v-model="form.name"
                />
              </div>
            </div>

            <div class="mb-4">
              <label class="text-base-content" :for="description"> Detalhamento </label>
              <textarea
                class="input input-bordered w-full"
                name="description"
                v-model="form.description"
                placeholder="Detalhamento da tarefa"
                rows=5
              ></textarea>
            </div>
            <div class="mb-4">
            </div>
            <div class="mb-4">
              <label class="text-base-content" for="department_id">Departamento</label>
              <DepartmentsSelectInput
                v-model="form.department_id"
                name="department_id"
                fieldsToDisplay="name"
                fieldNull="Sem departamento"
              />
            </div>
            <div class="mb-4">
              <div class="flex flex-wrap -mx-2">
                <div class="w-full md:w-1/2 px-2">
                  <div v-if="opportunity">
                    <label
                      for="opportunity"
                      class="block text-sm font-medium text-base-content mb-1"
                      >Oportunidade</label
                    >
                    <input
                      type="hidden"
                      id="opportunity"
                      name="opportunity_id"
                      :value="opportunity.id"
                    />
                    <TextValue
                      :modelValue="opportunity.name"
                      class="selected"
                      :readonly="true"
                    />
                  </div>
                  <div v-else>
                    <OpportunitiesSelectInput
                      label="Oportunidade"
                      v-model="form.opportunity_id"
                      fieldsToDisplay="name"
                      :autoSelect="false"
                      fieldNull="Nenhum"
                    />
                  </div>
                </div>
                <div class="w-full md:w-1/2 px-2">
                  <div v-if="project">
                    <label
                      for="project"
                      class="block text-sm font-medium text-base-content mb-1"
                      >Projeto</label
                    >
                    <input
                      type="hidden"
                      id="project"
                      name="project_id"
                      :value="project.id"
                    />
                    <TextValue 
                      :modelValue="project.name" 
                      class="selected" 
                      :readonly="true" 
                    />
                  </div>
                  <div v-else>
                    <ProjectsSelectInput
                      label="Projeto"
                      v-model="form.project_id"
                      fieldsToDisplay="name"
                      :autoSelect="false"
                      fieldNull="Nenhum"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-4">
              <div class="w-full">
                <UsersSelectInput
                  label="Responsável"
                  v-model="form.user_id"
                  fieldsToDisplay="name"
                  autoSelect="true"
                />
              </div>
            </div>

            <div class="mb-4">
              <div class="flex flex-wrap -mx-2">
                <div class="w-full md:w-1/2 px-2">
                  <DateInput
                    v-model="form.date_start"
                    label="Início"
                    name="date_start"
                    placeholder="início do prazo"
                    :autoFillNow="true"
                    @update="updateForm"
                  />
                </div>
                <div class="w-full md:w-1/2 px-2">
                  <DateInput
                    v-model="form.date_due"
                    label="Prazo final"
                    name="date_due"
                    placeholder="prazo final"
                    @update="updateForm"
                  />
                </div>
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
        form="taskForm"
        class="px-6 py-2 bg-primary hover:opacity-90 text-white rounded-lg font-semibold transition-colors flex items-center gap-2"
      >
        <font-awesome-icon icon="fa-solid fa-plus" />
        {{ cloneFrom ? 'Clonar Tarefa' : 'Criar Tarefa' }}
      </button>
    </template>
  </ModalCard>
</template>

<script>
import { submitFormCreate } from "@/utils/requests/httpUtils";
// import { inject } from "vue";
// import AddMessage from "@/components/forms/messages/AddMessage.vue";
import DateInput from "./inputs/date/DateInput.vue";
// import ErrorMessage from "./messages/ErrorMesssage.vue";
import OpportunitiesSelectInput from "./selects/OpportunitiesSelectInput.vue";
import ProjectsSelectInput from "./selects/ProjectsSelectInput.vue";
import DepartmentsSelectInput from "./selects/DepartmentsSelectInput.vue";
// import SuccessMessage from "./messages/SuccessMessage.vue";
import TextValue from "../fields/text/TextValue.vue";
import UsersSelectInput from "./selects/UsersSelectInput.vue";
import ErrorMessage from "../forms/messages/ErrorMessage.vue";
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
  name: "TaskCreateForm",
  emits: ["new-task-event", "close"],
  components: {
    // AddMessage,
    DateInput,
    // ErrorMessage,
    OpportunitiesSelectInput,
    ProjectsSelectInput,
    DepartmentsSelectInput,
    // SuccessMessage,
    TextValue,
    UsersSelectInput,
    ErrorMessage,
    ModalCard,
  },
  props: {
    opportunity: {
      type: Object,
      default: null,
    },
    project: {
      type: Object,
      default: null,
    },
    cloneFrom: {
      type: Object,
      default: null,
    },
    compact: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      allStatus: [],
      companies: [],
      data: [],
      form: {
        company_id: null,
        department_id: null,
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
      leads: [],
      message: null,
      messageStatus: "",
      messageText: "",
      newTask: null,
      // selectedProject: inject('currentProject'),
      users: [],
      formResponse: null,
    };
  },
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
      this.$emit("close");
    },
    async submitForm() {
      const { data, error } = await this.submitFormCreate("tasks", this.form);
      console.log("form", this.form);

      if (data) {
        this.messageStatus = "success";
        this.messageText = "Tarefa criada com sucesso!";
        this.isError = false;
        this.closeModal();
        this.clearForm();
        this.$emit("new-task-event", data);
        this.formResponse = null;
      }
      if (error) {
        this.formResponse = error.response?.data || { errors: { geral: ['Erro ao criar tarefa'] } };
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
  mounted() {
    // Preenche o formulário com os dados da tarefa clonada, exceto o prazo final
    if (this.cloneFrom) {
      this.form.name = this.cloneFrom.name;
      this.form.description = this.cloneFrom.description;
      this.form.company_id = this.cloneFrom.company_id;
      this.form.department_id = this.cloneFrom.department_id;
      this.form.contact_id = this.cloneFrom.contact_id;
      this.form.user_id = this.cloneFrom.user_id;
      this.form.priority = this.cloneFrom.priority;
      this.form.status = this.cloneFrom.status;
      this.form.date_start = this.cloneFrom.date_start;
      this.form.opportunity_id = this.cloneFrom.opportunity_id;
      this.form.project_id = this.cloneFrom.project_id;
    }

    // Inicializar IDs se as props existirem
    if (this.opportunity) {
      this.form.opportunity_id = this.opportunity.id;
    }
    if (this.project) {
      this.form.project_id = this.project.id;
    }
  },
  watch: {
    opportunity(newValue) {
      if (newValue) {
        this.form.opportunity_id = newValue.id;
      }
    },
    project(newValue) {
      if (newValue) {
        this.form.project_id = newValue.id;
      }
    },
  },
};
</script>