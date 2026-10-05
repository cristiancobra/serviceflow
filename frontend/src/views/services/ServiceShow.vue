<template>
  <div class="page-container">
    <PageHeader title="SERVIÇO" icon="fa-solid fa-tools" />

    <section class="section-container">
      <div class="section-header">
        <div class="section-title">
          <h1>
            <TextEditableField
              name="name"
              class=""
              v-model="service.name"
              placeholder="descrição detalhada do serviço"
              @save="updateService('name', $event)"
            />
          </h1>
        </div>
      </div>
    </section>

    <SectionCard title="Nota fiscal (NFS-e)" class="mt-6">
      <error-message v-if="validationErrors" :formResponse="validationErrors" />
      <TextEditableField
        name="nfse_service_code"
        :modelValue="service.nfse_service_code"
        label="Código de tributação nacional:"
        placeholder="6 dígitos"
        empty-text="não informado (usa o código padrão da conta)"
        @save="updateService('nfse_service_code', $event)"
      />
      <p class="text-xs text-base-content/60 mt-1">
        Código do serviço na NFS-e Nacional. Confira o seu no Emissor Nacional (nfse.gov.br).
      </p>
    </SectionCard>

    <service-profit-margin-section
      :service="service"
      @update-service="updateService"
    />

    <service-costs-section
      :service="service"
      @update-service-from-cost="updateServiceFromCost"
    />

    <div class="table-row">
      <div class="flex justify-end">
        <button
          class="myButton delete"
          @click="deleteService()"
        >
          excluir
        </button>
      </div>
    </div>
  </div>
</template>


<script>
import { destroy, show, updateField } from "@/utils/requests/httpUtils";
import TextEditableField from "@/components/fields/text/TextEditableField.vue";
import ServiceCostsSection from "../../components/show/ServiceCostsSection.vue";
import ServiceProfitMarginSection from "@/components/show/ServiceProfitMarginSection.vue";
import PageHeader from "@/components/layout/PageHeader.vue";
import SectionCard from "@/components/common/SectionCard.vue";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";

export default {
  data() {
    return {
      service: [],
      serviceId: "",
      validationErrors: null,
    };
  },
  components: {
    PageHeader,
    SectionCard,
    ErrorMessage,
    TextEditableField,
    ServiceCostsSection,
    ServiceProfitMarginSection,
  },
  methods: {
    destroy,
    show,
    updateField,
    async deleteService() {
      this.response = await destroy("services", this.serviceId);
      this.$router.push({ name: "serviceIndex" });
    },
    formatDateBr(date) {
      // Verifica se a data é válida
      if (!date) return "";

      const dateObj = new Date(date);
      const day = dateObj.getDate();
      const month = dateObj.getMonth() + 1; // Os meses em JavaScript começam em 0, então adicionamos 1
      const year = dateObj.getFullYear();

      // Formate a data no formato desejado (exemplo: dd/mm/aaaa)
      const dateBr = `${day}/${month}/${year}`;

      return dateBr;
    },
    async getService() {
      this.service = await show("services", this.serviceId);
    },
    setServiceId(serviceId) {
      this.serviceId = serviceId;
    },
    async updateService(fieldName, editedValue) {
      try {
        this.validationErrors = null;
        this.service = await updateField(
          "services",
          this.serviceId,
          fieldName,
          editedValue
        );
      } catch (error) {
        console.error(`Erro ao atualizar ${fieldName}:`, error);
        this.validationErrors = {
          errors: error.response?.data?.errors || { [fieldName]: ["Erro ao salvar. Tente novamente."] },
        };
      }
    },
    updateServiceFromCost(updatedService) {
      this.service = updatedService;
    },
  },
  async mounted() {
    this.setServiceId(this.$route.params.id);
    this.getService();
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.icon-column {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  margin: 1rem;
  flex-basis: 0%;
}

.integer-column {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-basis: 5%;
}

.price-column {
  display: flex;
  align-items: center;
  justify-content: right;
  flex-basis: 15%;
}

.total-price-column {
  display: flex;
  align-items: center;
  justify-content: right;
  flex-basis: 15%;
  font-weight: 800;
}

.container-card {
  margin-left: 180px;
  margin-right: 180px;
  margin-bottom: 60px;
  margin-top: 60px;
}

.myButton {
  border-width: 2px;
  border-style: solid;
  border-color: white;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  /* margin: 0 4px 0 4px; */
  color: white;
  font-weight: 800;
  /* width: 120px; */
}

.delete {
  background-color: color-mix(in oklab, var(--color-error) 15%, var(--color-base-100));
  border-color: var(--color-error);
  color: var(--color-error);
}

.delete:hover {
  background-color: var(--color-error);
  border-color: var(--color-error);
  color: white;
}
</style>
