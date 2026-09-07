<template>
  <div>
    <button
      type="button"
      @click="openModal"
      class="flex items-center justify-center w-10 h-10 rounded-full bg-primary hover:bg-primary-dark text-white transition-all duration-200 hover:shadow-lg"
    >
      <font-awesome-icon icon="fa-solid fa-cogs" class="text-lg" />
    </button>

    <div v-if="isModalVisible" class="myModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <div class="flex items-center">
              <font-awesome-icon
                icon="fa-solid fa-cogs"
                class="text-primary text-2xl mr-3"
              />
              <h2 class="text-2xl font-bold text-gray-900">Novo Custo</h2>
            </div>
            <button
              type="button"
              class="btn-close"
              @click="closeModal"
              aria-label="Close"
            ></button>
          </div>

          <div class="modal-body">
            <form @submit.prevent="submitForm">
              <!-- Nome do Custo -->
              <div class="mb-6">
                <TextInput
                  label="Nome"
                  name="name"
                  v-model="form.name"
                  placeholder="Nome do custo"
                />
                <div v-if="errors.name" class="mt-2">
                  <span class="text-sm text-red-600 font-medium">
                    * {{ errors.name[0] }}
                  </span>
                </div>
              </div>

              <!-- Descrição -->
              <div class="mb-6">
                <label
                  for="observations"
                  class="block text-sm font-semibold text-gray-900 mb-2"
                >
                  Descrição
                </label>
                <textarea
                  id="observations"
                  v-model="form.observations"
                  placeholder="Descreva o custo..."
                  rows="3"
                  class="w-full px-3 py-2 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 ease-in-out hover:border-gray-400 resize-none"
                ></textarea>
              </div>

              <!-- Preço -->
              <div class="mb-6">
                <label
                  for="price"
                  class="block text-sm font-semibold text-gray-900 mb-2"
                >
                  Preço
                </label>
                <money-input
                  name="price"
                  v-model="form.price"
                  show-currency-symbol
                  class="w-full pr-3 py-2 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 ease-in-out hover:border-gray-400"
                />
                <div v-if="errors.price" class="mt-2">
                  <span class="text-sm text-red-600 font-medium">
                    * {{ errors.price[0] }}
                  </span>
                </div>
              </div>

              <!-- Footer com botões -->
              <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <button
                  type="button"
                  class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors"
                  @click="closeModal"
                >
                  Fechar
                </button>
                <button type="submit" class="button-new">Criar</button>
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
import TextInput from "./inputs/text/TextInput.vue";
import MoneyInput from "./inputs/money/MoneyInput.vue";

export default {
  emits: ["new-cost-event"],
  data() {
    return {
      allStatus: [],
      formattedLaborHours: "",
      formattedLaborHourlyRate: "",
      laborHourlyRateMinutes: "",
      message: null,
      data: [],
      errors: [],
      form: {
        name: null,
        observations: null,
        price: null,
      },
      isModalVisible: false,
    };
  },
  components: {
    TextInput,
    MoneyInput,
  },
  methods: {
    submitFormCreate,
    closeModal() {
      this.isModalVisible = false;
    },
    async submitForm() {
      const { data, error } = await this.submitFormCreate("costs", this.form);

      if (data) {
        this.isModalVisible = false;
        this.$emit("new-cost-event", data);
      }
      if (error) {
        this.errors = error;
      }
    },
    openModal() {
      this.isModalVisible = true;
    },
    validateTimeInput() {
      const input = this.form.labor_hours;
      const timeRegex = /^(2[0-3]|[01]?[0-9]):([0-5]?[0-9])$/;

      if (!timeRegex.test(input)) {
        // O valor não está no formato de hora válido, você pode tratar isso aqui.
        // Por exemplo, definir um valor padrão ou exibir uma mensagem de erro.
        this.form.labor_hours = ""; // Defina um valor padrão vazio por enquanto.
      }
    },
  },
};
</script>

<style scoped>
/* Removido - tudo em Tailwind agora! */
</style>