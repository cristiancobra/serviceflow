<template>
  <div>
    <button type="button" class="button button-new" @click="openModal">
      <font-awesome-icon icon="fa-solid fa-plus" class="" />
    </button>

    <div v-if="isModalVisible" class="myModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1>Nova proposta</h1>
            <button
              type="button"
              class="btn-close"
              @click="closeModal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm">
              <div class="table-row">
                <TextAreaInput
                  class="text-start"
                  label="Detalhamento:"
                  name="description"
                  v-model="form.description"
                  placeholder="Detalhamento da oportunidade"
                  :rows="4"
                />
              </div>
              <div class="table-row">
                <div class="column-50">
                  <UsersSelectInput
                    class="text-start"
                    label="Responsável"
                    v-model="form.user_id"
                    fieldsToDisplay="name"
                    autoSelect="true"
                  />
                </div>
                <div class="column-50">
                  <label for="installment_quantity" class="form-label">
                    Quantidade de Parcelas
                  </label>
                  <input
                    type="number"
                    id="installment_quantity"
                    name="installment_quantity"
                    v-model="form.installment_quantity"
                    min="1"
                    max="99"
                    step="1"
                    class="form-control"
                  />
                </div>
              </div>
              <div class="table-row">
                <div class="column-50">
                  <div
                    v-if="currentProject"
                    class="d-flex justify-content-start"
                  >
                    <label for="project" class="form-label">Projeto</label>
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
              <div class="table-row">
                <div class="column-50">
                  <DateInput
                    class="text-start"
                    v-model="form.date"
                    label="Início"
                    name="date"
                    placeholder="início do prazo"
                    :autoFillNow="true"
                    @update="updateForm"
                  />
                </div>
                <div class="column-50">
                  <label for="duration" class="form-label"
                    >Validade da proposta</label
                  >
                  <input
                    type="number"
                    class="form-control"
                    v-model="form.validity_days"
                    name="duration"
                    placeholder="validade da proposta em dias"
                  />
                </div>
              </div>
              <div v-if="services.length === 0" class="table-row">
                <p>Você ainda não possui serviços cadastrados.</p>
              </div>
              <div v-else class="section-container">
                <div class="section-header">
                  <div class="section-title">
                    <font-awesome-icon icon="fa-solid fa-tools" class="icon" />
                    <h2>Serviços</h2>
                  </div>
                </div>
                <div class="list-container">
                  <div
                    class="table-row"
                    v-for="service in services"
                    :key="service.id"
                  >
                    <div class="column-10">
                      <input
                        type="number"
                        min="0"
                        :id="service.id"
                        v-model.number="service.quantity"
                        placeholder="0"
                        class="input-number"
                      />
                    </div>
                    <div class="column-60">
                      <label :for="service.id">
                        {{ service.name }}
                      </label>
                    </div>
                    <div class="column-20">
                      <money-editable-field
                        :name="service.id"
                        v-model="service.price"
                      />
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="costs.length === 0" class="table-row">
                <p>Você ainda não possui custos cadastrados.</p>
              </div>
              <div v-else class="section-container">
                <div class="section-header">
                  <div class="section-title">
                    <font-awesome-icon icon="fa-solid fa-tools" class="icon" />
                    <h2>Custos de produção</h2>
                  </div>
                </div>
                <div class="list-container">
                  <div class="table-row" v-for="cost in costs" :key="cost.id">
                    <div class="column-10">
                      <input
                        type="number"
                        min="0"
                        :id="cost.id"
                        v-model.number="cost.quantity"
                        placeholder="0"
                        class="input-number"
                      />
                    </div>
                    <div class="column-60">
                      <label :for="cost.id">
                        {{ cost.name }}
                      </label>
                    </div>
                    <div class="column-20">R$ {{ cost.price }}</div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-secondary"
                  data-bs-dismiss="modal"
                  @click="closeModal"
                >
                  Fechar
                </button>
                <button
                  type="submit"
                  class="button-new"
                  data-bs-dismiss="modal"
                >
                  criar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { index } from "@/utils/requests/httpUtils";
import { submitFormCreate } from "@/utils/requests/httpUtils";
import DateInput from "@/components/forms/inputs/date/DateInput.vue";
import TextAreaInput from "./inputs/textarea/TextAreaInput";
import UsersSelectInput from "./selects/UsersSelectInput.vue";
import MoneyEditableField from "../fields/number/MoneyEditableField.vue";

export default {
  components: {
    DateInput,
    MoneyEditableField,
    TextAreaInput,
    UsersSelectInput,
  },
  props: {
    opportunityId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      costs: [],
      form: {
        name: null,
        description: null,
        user_id: null,
        date: null,
        opportunity_id: this.opportunityId,
        validity_days: 30,
        installment_quantity: 1,
      },
      isModalVisible: false,
      modal: false,
      services: [],
      selectedServices: [],
    };
  },
  methods: {
    index,
    submitFormCreate,
    closeModal() {
      this.isModalVisible = false;
    },
    openModal() {
      this.isModalVisible = true;
    },
    async getCosts() {
      this.costs = await this.index("costs");
    },
    async getServices() {
      this.services = await this.index("services");
    },
    async submitForm() {
      this.form.services = this.services
        .filter((service) => service.quantity > 0)
        .map((service) => ({
          id: service.id,
          quantity: service.quantity,
          price: service.price,
        }));

      this.form.costs = this.costs
        .filter((cost) => cost.quantity > 0)
        .map((cost) => ({
          id: cost.id,
          quantity: cost.quantity,
          price: cost.price,
        }));

      const { data, error } = await this.submitFormCreate(
        "proposals",
        this.form
      );

      if (data) {
        this.isModalVisible = false;
        this.$emit("new-proposal-event", data);
      }
      if (error) {
        this.errors = error;
      }
    },
  },
  mounted() {
    this.getCosts();
    this.getServices();
  },
};
</script>