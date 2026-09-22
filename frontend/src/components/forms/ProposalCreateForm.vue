<template>
  <ModalCard
    title="Nova Proposta"
    icon="fa-solid fa-money-bill"
    size="xl"
    :compact="compact"
    @close="closeModal"
  >
            <form id="proposalCreateForm" @submit.prevent="submitForm">
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
                  <label for="installment_quantity" class="block text-sm font-medium text-base-content mb-2">
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
                    class="w-full px-3 py-2 text-base-content border border-base-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
              </div>
              <div class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div v-if="currentProject" class="flex flex-col">
                    <label for="project" class="block text-sm font-medium text-base-content mb-2">Projeto</label>
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
                    label="Data de criação"
                    name="date"
                    placeholder="início do prazo"
                    :autoFillNow="true"
                    @update="updateForm"
                  />
                </div>
                <div>
                  <label for="duration" class="block text-sm font-medium text-base-content mb-2">
                    Validade da proposta
                  </label>
                  <input
                    type="number"
                    class="w-full px-3 py-2 text-base-content border border-base-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    v-model="form.validity_days"
                    name="duration"
                    placeholder="validade da proposta em dias"
                  />
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                  <label for="discount_type" class="block text-sm font-medium text-base-content mb-2">
                    Tipo de Desconto
                  </label>
                  <select
                    id="discount_type"
                    v-model="discountType"
                    class="w-full px-3 py-2 text-base-content border border-base-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="percentage">Percentual (%)</option>
                    <option value="value">Valor (R$)</option>
                  </select>
                </div>
                <div>
                  <label for="discount" class="block text-sm font-medium text-base-content mb-2">
                    {{ discountType === 'percentage' ? 'Desconto (%)' : 'Desconto (R$)' }}
                  </label>
                  <input
                    v-if="discountType === 'percentage'"
                    type="number"
                    id="discount"
                    v-model.number="discountInput"
                    :min="0"
                    :max="100"
                    step="0.01"
                    class="w-full px-3 py-2 text-base-content border border-base-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="0"
                  />
                  <money-input
                    v-else
                    name="discount"
                    v-model="discountInput"
                    class="w-full px-3 py-2 text-base-content border border-base-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
              </div>
              
              <div v-if="services.length === 0" class="mb-6">
                <p class="text-base-content/70">Você ainda não possui serviços cadastrados.</p>
              </div>
              <div v-else class="bg-base-200 rounded-lg border border-base-300 p-4 mb-6">
                <div class="mb-4">
                  <div class="flex items-center mb-2">
                    <font-awesome-icon icon="fa-solid fa-tools" class="text-blue-600 mr-2" />
                    <h2 class="text-lg font-medium text-base-content">Serviços</h2>
                  </div>
                </div>
                <div class="space-y-2">
                  <div
                    class="flex items-center space-x-4 p-2 bg-base-100 rounded border"
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
                        class="w-full px-2 py-1 border border-base-300 rounded text-right text-base-content focus:outline-none focus:ring-1 focus:ring-blue-500"
                      />
                    </div>
                    <div class="flex-1">
                      <label :for="service.id" class="text-base-content">
                        {{ service.name }}
                      </label>
                    </div>
                    <div class="w-32">
                      <money-editable-field
                        :name="service.id"
                        v-model="service.price"
                      />
                    </div>
                    <div class="w-32 text-right text-base-content font-medium">
                      R$ {{ formatCurrency((service.quantity || 0) * (service.price || 0)) }}
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="costs.length === 0" class="mb-6">
                <p class="text-base-content/70">Você ainda não possui custos cadastrados.</p>
              </div>
              <div v-else class="bg-base-200 rounded-lg border border-base-300 p-4 mb-6">
                <div class="mb-4">
                  <div class="flex items-center mb-2">
                    <font-awesome-icon icon="fa-solid fa-tools" class="text-orange-600 mr-2" />
                    <h2 class="text-lg font-medium text-base-content">Custos de produção</h2>
                  </div>
                </div>
                <div class="space-y-2">
                  <div class="flex items-center space-x-4 p-2 bg-base-100 rounded border" v-for="cost in costs" :key="cost.id">
                    <div class="w-20">
                      <input
                        type="number"
                        min="0"
                        :id="cost.id"
                        v-model.number="cost.quantity"
                        placeholder="0"
                        class="w-full px-2 py-1 border border-base-300 rounded text-right text-base-content focus:outline-none focus:ring-1 focus:ring-blue-500"
                      />
                    </div>
                    <div class="flex-1">
                      <label :for="cost.id" class="text-base-content">
                        {{ cost.name }}
                      </label>
                    </div>
                    <div class="w-32">
                      <money-editable-field
                        :name="cost.id"
                        v-model="cost.price"
                      />
                    </div>
                    <div class="w-32 text-right text-base-content font-medium">
                      R$ {{ formatCurrency((cost.quantity || 0) * (cost.price || 0)) }}
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Resumo de Valores -->
              <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 mb-6">
                <h3 class="text-lg font-semibold text-base-content mb-3">Resumo</h3>
                <div class="space-y-2">
                  <div class="flex justify-between text-base-content">
                    <span>Subtotal:</span>
                    <span class="font-medium">{{ formatCurrency(subtotal) }}</span>
                  </div>
                  <div v-if="totalDiscount > 0" class="flex justify-between text-orange-600">
                    <span>Desconto:</span>
                    <span class="font-medium">- {{ formatCurrency(totalDiscount) }}</span>
                  </div>
                  <div class="flex justify-between text-lg font-bold text-base-content pt-2 border-t border-blue-200">
                    <span>Total:</span>
                    <span>{{ formatCurrency(total) }}</span>
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
        form="proposalCreateForm"
        class="px-6 py-2 bg-primary hover:opacity-90 text-white rounded-lg font-semibold transition-colors flex items-center gap-2"
      >
        <font-awesome-icon icon="fa-solid fa-plus" />
        Criar Proposta
      </button>
    </template>
  </ModalCard>
</template>

<script>
import { index } from "@/utils/requests/httpUtils";
import { submitFormCreate } from "@/utils/requests/httpUtils";
import DateInput from "@/components/forms/inputs/date/DateInput.vue";
import TextAreaInput from "./inputs/textarea/TextAreaInput.vue";
import UsersSelectInput from "./selects/UsersSelectInput.vue";
import MoneyEditableField from "../fields/number/MoneyEditableField.vue";
import MoneyInput from "./inputs/money/MoneyInput.vue";
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
  components: {
    DateInput,
    MoneyEditableField,
    MoneyInput,
    TextAreaInput,
    UsersSelectInput,
    ModalCard,
  },
  emits: ["new-proposal-event", "close"],
  props: {
    opportunityId: {
      type: Number,
      required: true,
    },
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
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
      modal: false,
      services: [],
      selectedServices: [],
      discountType: 'percentage',
      discountInput: 0,
    };
  },
  computed: {
    subtotal() {
      const servicesTotal = this.services.reduce((sum, service) => {
        return sum + ((service.quantity || 0) * (service.price || 0));
      }, 0);
      
      const costsTotal = this.costs.reduce((sum, cost) => {
        return sum + ((cost.quantity || 0) * (cost.price || 0));
      }, 0);
      
      return servicesTotal + costsTotal;
    },
    totalDiscount() {
      if (this.discountType === 'percentage') {
        return (this.subtotal * (this.discountInput || 0)) / 100;
      }
      return this.discountInput || 0;
    },
    total() {
      return Math.max(0, this.subtotal - this.totalDiscount);
    }
  },
  methods: {
    index,
    submitFormCreate,
    closeModal() {
      this.$emit("close");
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

      this.form.total_discount = this.totalDiscount;
      
      console.log('Dados enviados:', {
        subtotal: this.subtotal,
        discountType: this.discountType,
        discountInput: this.discountInput,
        totalDiscount: this.totalDiscount,
        total_discount: this.form.total_discount,
        form: this.form
      });

      const { data, error } = await this.submitFormCreate(
        "proposals",
        this.form
      );

      if (data) {
        this.$emit("close");
        this.$emit("new-proposal-event", data);
      }
      if (error) {
        this.errors = error;
      }
    },
    formatCurrency(value) {
      if (typeof value === 'string') {
        value = parseFloat(value) || 0;
      }
      return (value || 0).toLocaleString("pt-BR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
  },
  mounted() {
    this.getCosts();
    this.getServices();
  },
};
</script>