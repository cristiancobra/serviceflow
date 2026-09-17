<template>
  <div
    v-if="!transactions || transactions.length === 0"
    class="w-full rounded-xl border border-dashed border-indigo-200 bg-gradient-to-r from-indigo-50 to-sky-50 py-8 text-center text-indigo-700 shadow-sm"
  >
    <p class="text-sm font-medium">{{ isDebit ? 'Nenhum pagamento feito' : 'Nenhum pagamento recebido' }}</p>
  </div>

  <div
    v-else
    class="mt-4 space-y-2 rounded-xl border border-gray-200 bg-white p-2 border-t-4 border-t-indigo-500 shadow-sm"
  >
    <div
      v-for="transaction in transactions"
      :key="transaction.id"
      class="group flex items-center justify-between px-4 py-3 rounded-md bg-white even:bg-sky-50/40 hover:bg-sky-100/60 border-l-4 border-transparent hover:border-sky-400 transition-colors"
    >
      <div class="min-w-[160px]">
        <div
          class="inline-flex items-center gap-2 rounded-full bg-indigo-100 px-3 py-1"
        >
          <span class="h-2.5 w-2.5 rounded-full bg-sky-500"></span>
          <date-editable-input
            name="transaction_date"
            :modelValue="transaction.transaction_date"
            @save="emitUpdateTransaction('transaction_date', transaction.id, $event)"
            class-text="text-sm font-semibold text-indigo-700"
          />
        </div>
      </div>
      <div class="flex-1"></div>
      <div
        class="text-right inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 ring-1 ring-emerald-200 text-emerald-700"
      >
        <money-editable-field
          name="amount"
          :modelValue="transaction.amount"
          @save="emitUpdateTransaction('amount', transaction.id, $event)"
        />
      </div>
      <delete-icon-button
        class="ml-2"
        title="Excluir transação"
        confirm-message="Tem certeza que deseja excluir esta transação? Esta ação não pode ser desfeita."
        @confirm="$emit('delete-transaction', transaction.id)"
      />
    </div>
  </div>
</template>

<script>
import DateEditableInput from "@/components/fields/date/DateEditableInput.vue";
import MoneyEditableField from "@/components/fields/number/MoneyEditableField.vue";
import DeleteIconButton from "@/components/buttons/DeleteIconButton.vue";

export default {
  name: "TransactionsListSection",
  components: {
    DateEditableInput,
    MoneyEditableField,
    DeleteIconButton,
  },
  props: {
    transactions: {
      type: Array,
      required: false,
      default: () => [],
    },
    isDebit: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  emits: ["update-transaction", "delete-transaction"],
  methods: {
    emitUpdateTransaction(fieldName, transactionId, editedValue) {
      this.$emit("update-transaction", fieldName, transactionId, editedValue);
    },
  },
};
</script>
