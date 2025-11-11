<template>
  <div>
    <ButtonNew 
      icon="fa-solid fa-plus"
      @click="openModal"
    />

    <div v-if="isModalVisible" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-auto">
        <div class="bg-gray-50 rounded-t-lg">
          <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-800">Nova proposta</h1>
            <button
              type="button"
              class="text-gray-400 hover:text-gray-600 focus:outline-none"
              @click="closeModal"
              aria-label="Close"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div class="p-6">
            <form @submit.prevent="submitForm">
              <div class="mb-6">
                <TextAreaInput
                  class="text-left"
                  label="Detalhamento:"
                  name="description"
                  v-model="form.description"
                  placeholder="Detalhamento da oportunidade"
                  :rows="4"
                />
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                  <UsersSelectInput
                    class="text-left"
                    label="Responsável"
                    v-model="form.user_id"
                    fieldsToDisplay="name"
                    autoSelect="true"
                  />
                </div>
                <div>
                  <label for="installment_quantity" class="block text-sm font-medium text-gray-700 mb-2">
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
                    class="w-full px-3 py-2 text-black border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
              </div>
              <div class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div v-if="currentProject" class="flex flex-col">
                    <label for="project" class="block text-sm font-medium text-gray-700 mb-2">Projeto</label>
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
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                  <DateInput
                    class="text-left"
                    v-model="form.date"
                    label="Início"
                    name="date"
                    placeholder="início do prazo"
                    :autoFillNow="true"
                    @update="updateForm"
                  />
                </div>
                <div>
                  <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                    Validade da proposta
                  </label>
                  <input
                    type="number"
                    class="w-full px-3 py-2 text-black border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    v-model="form.validity_days"
                    name="duration"
                    placeholder="validade da proposta em dias"
                  />
                </div>
              </div>
              <div v-if="services.length === 0" class="mb-6">
                <p class="text-gray-600">Você ainda não possui serviços cadastrados.</p>
              </div>
              <div v-else class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-6">
                <div class="mb-4">
                  <div class="flex items-center mb-2">
                    <font-awesome-icon icon="fa-solid fa-tools" class="text-blue-600 mr-2" />
                    <h2 class="text-lg font-medium text-gray-800">Serviços</h2>
                  </div>
                </div>
                <div class="space-y-2">
                  <div
                    class="flex items-center space-x-4 p-2 bg-white rounded border"
                    v-for="service in services"
                    :key="service.id"
                  >
                    <div class="w-20">
                      <input
                        type="number"
                        min="0"
                        :id="service.id"
                        v-model.number="service.quantity"
                        placeholder="0"
                        class="w-full px-2 py-1 border border-gray-300 rounded text-right text-black focus:outline-none focus:ring-1 focus:ring-blue-500"
                      />
                    </div>
                    <div class="flex-1">
                      <label :for="service.id" class="text-gray-700">
                        {{ service.name }}
                      </label>
                    </div>
                    <div class="w-32">
                      <money-editable-field
                        :name="service.id"
                        v-model="service.price"
                      />
                    </div>
                    <div class="w-32 text-right text-gray-700 font-medium">
                      R$ {{ formatCurrency((service.quantity || 0) * (service.price || 0)) }}
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="costs.length === 0" class="mb-6">
                <p class="text-gray-600">Você ainda não possui custos cadastrados.</p>
              </div>
              <div v-else class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-6">
                <div class="mb-4">
                  <div class="flex items-center mb-2">
                    <font-awesome-icon icon="fa-solid fa-tools" class="text-orange-600 mr-2" />
                    <h2 class="text-lg font-medium text-gray-800">Custos de produção</h2>
                  </div>
                </div>
                <div class="space-y-2">
                  <div class="flex items-center space-x-4 p-2 bg-white rounded border" v-for="cost in costs" :key="cost.id">
                    <div class="w-20">
                      <input
                        type="number"
                        min="0"
                        :id="cost.id"
                        v-model.number="cost.quantity"
                        placeholder="0"
                        class="w-full px-2 py-1 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500"
                      />
                    </div>
                    <div class="flex-1">
                      <label :for="cost.id" class="text-gray-700">
                        {{ cost.name }}
                      </label>
                    </div>
                    <div class="w-32 text-gray-600">R$ {{ cost.price }}</div>
                  </div>
                </div>
              </div>
              <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                <button
                  type="button"
                  class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition-colors duration-200"
                  @click="closeModal"
                >
                  Fechar
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors duration-200"
                >
                  Criar
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
import ButtonNew from "@/components/ui/ButtonNew.vue";

export default {
  components: {
    ButtonNew,
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
    formatCurrency(value) {
      return value.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL",
      });
    },
  },
  mounted() {
    this.getCosts();
    this.getServices();
  },
};
</script>