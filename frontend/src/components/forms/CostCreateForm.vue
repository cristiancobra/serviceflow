<template>
  <ModalCard
    title="Novo Custo"
    icon="fa-solid fa-cogs"
    :compact="compact"
    @close="closeModal"
  >
    <form id="costCreateForm" @submit.prevent="submitForm">
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
          class="block text-sm font-semibold text-base-content mb-2"
        >
          Descrição
        </label>
        <textarea
          id="observations"
          v-model="form.observations"
          placeholder="Descreva o custo..."
          rows="3"
          class="w-full px-3 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 ease-in-out resize-none"
        ></textarea>
      </div>

      <!-- Preço -->
      <div class="mb-6">
        <label
          for="price"
          class="block text-sm font-semibold text-base-content mb-2"
        >
          Preço
        </label>
        <money-input
          name="price"
          v-model="form.price"
          show-currency-symbol
          class="w-full pr-3 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 ease-in-out"
        />
        <div v-if="errors.price" class="mt-2">
          <span class="text-sm text-red-600 font-medium">
            * {{ errors.price[0] }}
          </span>
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
        form="costCreateForm"
        class="px-6 py-2 bg-primary hover:opacity-90 text-white rounded-lg font-semibold transition-colors flex items-center gap-2"
      >
        <font-awesome-icon icon="fa-solid fa-plus" />
        Criar Custo
      </button>
    </template>
  </ModalCard>
</template>

<script>
import { submitFormCreate } from "@/utils/requests/httpUtils";
import TextInput from "./inputs/text/TextInput.vue";
import MoneyInput from "./inputs/money/MoneyInput.vue";
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
  name: "CostCreateForm",
  emits: ["new-cost-event", "close"],
  props: {
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      errors: [],
      form: {
        name: null,
        observations: null,
        price: null,
      },
    };
  },
  components: {
    TextInput,
    MoneyInput,
    ModalCard,
  },
  methods: {
    submitFormCreate,
    closeModal() {
      this.$emit("close");
    },
    async submitForm() {
      const { data, error } = await this.submitFormCreate("costs", this.form);

      if (data) {
        this.$emit("close");
        this.$emit("new-cost-event", data);
      }
      if (error) {
        this.errors = error;
      }
    },
  },
};
</script>
