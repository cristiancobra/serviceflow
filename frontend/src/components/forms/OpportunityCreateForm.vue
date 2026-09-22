<template>
  <ModalCard
    title="Nova Oportunidade"
    subtitle="Adicione uma nova oportunidade de negócio"
    icon="fa-solid fa-bullseye"
    size="lg"
    :compact="compact"
    @close="closeModal"
  >
    <ErrorMessage v-if="formResponse" :formResponse="formResponse" />

    <form id="opportunityCreateForm" @submit.prevent="submitForm" class="space-y-6">
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
                  :disabled="!!currentLead"
                />
                <p v-if="currentLead" class="mt-1 text-sm text-base-content/70">
                  <font-awesome-icon icon="fas fa-info-circle" class="mr-1" />
                  Oportunidade vinculada a este contato
                </p>
                <button
                  v-else
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
                  <label class="block text-sm font-semibold text-base-content mb-2">Projeto</label>
                  <div class="px-4 py-2 bg-base-300 rounded-lg text-base-content font-medium">
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
        form="opportunityCreateForm"
        class="px-6 py-2 bg-primary hover:opacity-90 text-white rounded-lg font-semibold transition-colors"
      >
        <font-awesome-icon icon="fa-solid fa-plus" class="me-2" />
        Criar Oportunidade
      </button>
    </template>
  </ModalCard>
</template>

<script>
import { mapMutations } from "vuex";
import { submitFormCreate } from "@/utils/requests/httpUtils";
import CompaniesSelectInput from "@/components/forms/selects/CompaniesSelectInput.vue";
import DateInput from "@/components/forms/inputs/date/DateInput.vue";
import LeadsSelectInput from "@/components/forms/selects/LeadsSelectInput.vue";
import ProjectsSelectInput from "@/components/forms/selects/ProjectsSelectInput.vue";
import TextAreaInput from "./inputs/textarea/TextAreaInput.vue";
import TextInput from "./inputs/text/TextInput.vue";
import UsersSelectInput from "./selects/UsersSelectInput.vue";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
  name: "OpportunityCreateForm",
  components: {
    CompaniesSelectInput,
    DateInput,
    LeadsSelectInput,
    ProjectsSelectInput,
    TextAreaInput,
    TextInput,
    UsersSelectInput,
    ErrorMessage,
    ModalCard,
  },
  emits: ["new-opportunity-event", "close"],
  props: {
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
    currentProject: {
      type: Object,
      default: null,
    },
    currentLead: {
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
    currentLead: {
      handler(newLead) {
        if (newLead) {
          this.form.lead_id = newLead.id;
        }
      },
      immediate: true,
    },
  },
  methods: {
    ...mapMutations(["openModal"]),
    submitFormCreate,
    closeModal() {
      this.$emit("close");
    },
    async submitForm() {
      const { data, error } = await this.submitFormCreate(
        "opportunities",
        this.form
      );

      if (data) {
        this.$emit("close");
        this.$emit("new-opportunity-event", data);
      }
      if (error) {
        this.formResponse = error.response?.data || { errors: { geral: ['Erro ao criar oportunidade'] } };
        console.error("Erro ao criar oportunidade:", error);
      }
    },
    toggleCompany() {
      this.openModal({
        component: "CompanyCreateForm",
        listeners: {
          "new-company-event": this.addCompanyCreated,
        },
      });
    },
    toggleLead() {
      this.openModal({
        component: "LeadCreateForm",
        listeners: {
          "new-lead-event": this.addLeadCreated,
        },
      });
    },
    addCompanyCreated(newCompany) {
      this.form.company_id = newCompany.id;
      // Recarregar a lista de empresas
      this.$refs.companiesSelect?.reload();
    },
    addLeadCreated(newLead) {
      this.form.lead_id = newLead.id;
      // Recarregar a lista de leads
      this.$refs.leadsSelect?.reload();
    },
  },
};
</script>