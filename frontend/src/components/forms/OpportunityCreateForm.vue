<template>
  <div>
    <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.25)">
      <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-gradient-to-r from-purple-50 to-purple-25 border-b border-gray-200 px-8 py-6 flex justify-between items-center">
          <div>
            <h3 class="text-2xl font-bold text-gray-800">Nova Oportunidade</h3>
            <p class="text-gray-600 text-sm mt-1">Adicione uma nova oportunidade de negócio</p>
          </div>
          <button
            type="button"
            class="text-gray-400 hover:text-gray-600 transition-colors"
            @click="closeModal"
          >
            <font-awesome-icon icon="fa-solid fa-xmark" class="text-2xl" />
          </button>
        </div>

        <!-- Body -->
        <div class="px-8 py-6">
          <ErrorMessage v-if="formResponse" :formResponse="formResponse" />
          
          <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Título -->
            <div>
              <TextInput
                label="Título"
                name="name"
                v-model="form.name"
                placeholder="Título da oportunidade"
              />
            </div>

            <!-- Descrição -->
            <div>
              <TextAreaInput
                label="Descrição"
                name="description"
                v-model="form.description"
                placeholder="Detalhamento da oportunidade"
                :rows="4"
              />
            </div>

            <!-- Empresa e Contato -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <CompaniesSelectInput
                  ref="companiesSelect"
                  label="Empresa Cliente"
                  name="company_id"
                  v-model="form.company_id"
                  :fieldsToDisplay="['business_name', 'legal_name']"
                  fieldNull="Não possui / minha empresa"
                />
                <button
                  type="button"
                  class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-semibold"
                  @click="toggleCompany()"
                >
                  + Adicionar nova empresa
                </button>
              </div>

              <div>
                <LeadsSelectInput
                  ref="leadsSelect"
                  label="Contato"
                  name="lead_id"
                  v-model="form.lead_id"
                  fieldsToDisplay="name"
                  fieldNull="Não possui"
                />
                <button
                  type="button"
                  class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-semibold"
                  @click="toggleLead()"
                >
                  + Adicionar novo contato
                </button>
              </div>
            </div>

            <!-- Responsável e Projeto -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <UsersSelectInput
                  label="Responsável"
                  v-model="form.user_id"
                  fieldsToDisplay="name"
                  autoSelect="true"
                />
              </div>
              <div>
                <div v-if="currentProject">
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Projeto</label>
                  <div class="px-4 py-2 bg-gray-100 rounded-lg text-gray-800 font-medium">
                    {{ currentProject.name }}
                  </div>
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

            <!-- Datas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <DateInput
                  v-model="form.date_start"
                  label="Início"
                  name="date_start"
                  placeholder="Data de início"
                  :autoFillNow="true"
                />
              </div>
              <div>
                <DateInput
                  v-model="form.date_due"
                  label="Prazo Final"
                  name="date_due"
                  placeholder="Data limite"
                />
              </div>
            </div>
          </form>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-8 py-4 flex justify-end gap-3">
          <button
            type="button"
            class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
            @click="closeModal"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-6 py-2 bg-gradient-to-r from-purple-600 to-purple-800 text-white rounded-lg font-semibold hover:shadow-lg transition-all hover:-translate-y-0.5"
            @click="submitForm"
          >
            <font-awesome-icon icon="fa-solid fa-plus" class="me-2" />
            Criar Oportunidade
          </button>
        </div>
      </div>
    </div>

    <!-- Modais em sobreposição -->
    <company-create-form 
      v-model="isActiveFormCompany"
      @new-company-event="addCompanyCreated" 
    />
    
    <lead-create-form 
      v-model="isActiveFormLead"
      @new-lead-event="addLeadCreated" 
    />
  </div>
</template>

<script>
import { submitFormCreate } from "@/utils/requests/httpUtils";
import CompaniesSelectInput from "@/components/forms/selects/CompaniesSelectInput";
import DateInput from "@/components/forms/inputs/date/DateInput.vue";
import CompanyCreateForm from "@/components/forms/CompanyCreateForm.vue";
import LeadCreateForm from "@/components/forms/LeadCreateForm.vue";
import LeadsSelectInput from "@/components/forms/selects/LeadsSelectInput.vue";
import ProjectsSelectInput from "@/components/forms/selects/ProjectsSelectInput.vue";
import TextAreaInput from "./inputs/textarea/TextAreaInput";
import TextInput from "./inputs/text/TextInput";
import UsersSelectInput from "./selects/UsersSelectInput.vue";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";

export default {
  name: "OpportunityCreateForm",
  components: {
    CompanyCreateForm,
    CompaniesSelectInput,
    DateInput,
    LeadCreateForm,
    LeadsSelectInput,
    ProjectsSelectInput,
    TextAreaInput,
    TextInput,
    UsersSelectInput,
    ErrorMessage,
  },
  emits: ["new-opportunity-event", "update:modelValue"],
  props: {
    modelValue: {
      type: Boolean,
      default: false,
    },
    currentProject: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      form: {
        name: null,
        description: null,
        company_id: null,
        lead_id: null,
        user_id: null,
        project_id: null,
        date_start: null,
        date_due: null,
      },
      formResponse: null,
      isActiveFormCompany: false,
      isActiveFormLead: false,
    };
  },
  watch: {
    currentProject: {
      handler(newProject) {
        if (newProject) {
          this.form.project_id = newProject.id;
        }
      },
      immediate: true,
    },
  },
  methods: {
    submitFormCreate,
    closeModal() {
      this.$emit("update:modelValue", false);
      this.formResponse = null;
      this.clearForm();
    },
    async submitForm() {
      const { data, error } = await this.submitFormCreate(
        "opportunities",
        this.form
      );

      if (data) {
        this.$emit("update:modelValue", false);
        this.$emit("new-opportunity-event", data);
        this.clearForm();
        this.formResponse = null;
      }
      if (error) {
        this.formResponse = error.response?.data || { errors: { geral: ['Erro ao criar oportunidade'] } };
        console.error("Erro ao criar oportunidade:", error);
      }
    },
    clearForm() {
      this.form.name = null;
      this.form.description = null;
      this.form.company_id = null;
      this.form.lead_id = null;
      this.form.user_id = null;
      this.form.project_id = null;
      this.form.date_start = null;
      this.form.date_due = null;
      this.isActiveFormCompany = false;
      this.isActiveFormLead = false;
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
    addCompanyCreated(newCompany) {
      this.form.company_id = newCompany.id;
      this.isActiveFormCompany = false;
      // Recarregar a lista de empresas
      this.$refs.companiesSelect?.reload();
    },
    addLeadCreated(newLead) {
      this.form.lead_id = newLead.id;
      this.isActiveFormLead = false;
      // Recarregar a lista de leads
      this.$refs.leadsSelect?.reload();
    },
  },
};
</script>