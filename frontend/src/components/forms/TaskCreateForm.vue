<template>
  <div>
    <div v-show="modelValue" class="myModal">
      <div class="bg-white rounded-lg shadow-lg mt-25">
        <div class="flex items-center justify-between p-4 border-b">
          <div class="flex items-center">
            <font-awesome-icon
              icon="fa-solid fa-tasks"
              class="text-primary text-xl mr-2"
            />
            <h5 class="text-primary text-lg font-semibold">Nova tarefa</h5>
          </div>
          <button
            type="button"
            class="text-gray-400 hover:text-gray-600"
            @click="closeModal"
            aria-label="Close"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              ></path>
            </svg>
          </button>
        </div>
        <div class="p-4">
          <form id="taskForm" @submit.prevent="submitForm">
            <div class="mb-4">
              <div class="form-control w-full">
                <label class="text-black" :for="name"> Nome da tarefa </label>
                <input
                  class="input input-bordered w-full"
                  type="text"
                  name="name"
                  v-model="form.name"
                />
              </div>
            </div>

            <div class="mb-4">
              <label class="text-black" :for="description"> Detalhamento </label>
              <textarea
                class="input input-bordered w-full"
                name="description"
                v-model="form.description"
                placeholder="Detalhamento da tarefa"
                rows=5
                @input="$emit('update:modelValue', $event.target.value)"
              ></textarea>
            </div>
            <div class="mb-4">
              <div class="flex flex-wrap -mx-2">
                <div class="w-full md:w-1/2 px-2">
                  <div v-if="currentOpportunity">
                    <label
                      for="opportunity"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >Oportunidade</label
                    >
                    <input
                      type="hidden"
                      id="opportunity"
                      name="opportunity_id"
                      v-model="currentOpportunity.id"
                    />
                    <TextValue
                      v-model="currentOpportunity.name"
                      class="selected"
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
                  <div v-if="currentProject">
                    <label
                      for="project"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >Projeto</label
                    >
                    <input
                      type="hidden"
                      id="project"
                      name="project_id"
                      v-model="currentProject.id"
                    />
                    <TextValue v-model="currentProject.name" class="selected" />
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
        </div>
        <div class="flex justify-end p-4 border-t bg-gray-50">
          <button type="button" class="btn" @click="closeModal">Fechar</button>
          <button type="submit" class="btn btn-primary" form="taskForm">
            criar
          </button>
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
import TextValue from "../fields/text/TextValue.vue";
import UsersSelectInput from "./selects/UsersSelectInput.vue";

export default {
  name: "TaskCreateForm",
  emits: ["new-task-event", "update:modelValue"],
  components: {
    // AddMessage,
    DateInput,
    // ErrorMessage,
    OpportunitiesSelectInput,
    ProjectsSelectInput,
    // SuccessMessage,
    TextValue,
    UsersSelectInput,
  },
  props: {
    modelValue: {
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
    };
  },
  inject: ["currentProject", "currentOpportunity"],
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
      this.$emit("update:modelValue", false);
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