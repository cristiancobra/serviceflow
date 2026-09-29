<template>
  <div>
    <form @submit.prevent="submitForm">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 mb-6">
        <!-- Descrição -->
        <div class="fieldset md:col-span-2">
          <label for="description" class="fieldset-legend justify-start">Descrição <span class="text-error">*</span></label>
          <input
            id="description"
            v-model="form.description"
            type="text"
            class="input w-full"
            placeholder="Ex: Notebook"
            required
          />
          <span v-if="errors.description" class="text-error text-xs">{{ errors.description[0] }}</span>
        </div>

        <!-- Valor -->
        <div class="fieldset">
          <label for="amount" class="fieldset-legend justify-start">Valor Total <span class="text-error">*</span></label>
          <money-input
            name="amount"
            v-model="form.amount"
            class="input w-full"
          />
          <span v-if="errors.amount" class="text-error text-xs">{{ errors.amount[0] }}</span>
        </div>

        <!-- Parcelas -->
        <div class="fieldset">
          <label for="installment_total" class="fieldset-legend">Parcelas</label>
          <input
            id="installment_total"
            v-model.number="form.installment_total"
            type="number"
            min="1"
            max="60"
            class="input w-full"
          />
          <span v-if="errors.installment_total" class="text-error text-xs">{{ errors.installment_total[0] }}</span>
        </div>

        <!-- Data da Compra -->
        <div class="fieldset">
          <label for="purchase_date" class="fieldset-legend justify-start">Data da Compra <span class="text-error">*</span></label>
          <input
            id="purchase_date"
            v-model="form.purchase_date"
            type="date"
            class="input w-full"
            required
          />
          <span v-if="errors.purchase_date" class="text-error text-xs">{{ errors.purchase_date[0] }}</span>
        </div>

        <!-- Categoria -->
        <div class="fieldset">
          <label for="category" class="fieldset-legend">Categoria</label>
          <input
            id="category"
            v-model="form.category"
            type="text"
            class="input w-full"
            placeholder="Ex: Equipamentos"
          />
          <span v-if="errors.category" class="text-error text-xs">{{ errors.category[0] }}</span>
        </div>
      </div>

      <!-- Botões de Ação -->
      <div class="flex justify-end gap-3 pt-4 border-t border-base-300">
        <button type="button" @click="cancel" class="btn btn-ghost">
          Cancelar
        </button>
        <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
          <span v-if="isSubmitting">Salvando...</span>
          <span v-else>Lançar Compra</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { store } from "@/utils/requests/httpUtils";
import MoneyInput from "./inputs/money/MoneyInput.vue";

export default {
  name: "CreditCardChargeForm",
  components: {
    MoneyInput,
  },
  props: {
    creditCardId: {
      type: [Number, String],
      required: true,
    },
  },
  data() {
    return {
      form: this.emptyForm(),
      errors: {},
      isSubmitting: false,
    };
  },
  methods: {
    emptyForm() {
      return {
        credit_card_id: this.creditCardId,
        description: "",
        amount: 0,
        installment_total: 1,
        purchase_date: new Date().toISOString().slice(0, 10),
        category: "",
      };
    },

    async submitForm() {
      this.isSubmitting = true;
      this.errors = {};

      try {
        const response = await store("credit_card_charges", {
          ...this.form,
          credit_card_id: this.creditCardId,
        });

        this.$emit("saved", response);
        this.form = this.emptyForm();
      } catch (error) {
        console.error("Erro ao lançar compra:", error);

        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors = {
            general: [error.response?.data?.message || "Erro ao lançar compra"]
          };
        }
      } finally {
        this.isSubmitting = false;
      }
    },

    cancel() {
      this.form = this.emptyForm();
      this.errors = {};
      this.$emit("cancel");
    },
  },
};
</script>
