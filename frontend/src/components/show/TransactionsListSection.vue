<template>
  <EmptyState
    v-if="!transactions || transactions.length === 0"
    :text="isDebit ? 'Nenhum pagamento feito' : 'Nenhum pagamento recebido'"
    icon="fa-solid fa-coins"
  />

  <!-- Mesmo formato das linhas de pagamento das faturas na oportunidade -->
  <div v-else class="space-y-1 rounded-lg bg-base-200 p-2">
    <div
      v-for="transaction in transactions"
      :key="transaction.id"
      class="flex items-center gap-3 px-2 py-1 rounded-md hover:bg-info/10 border-l-4 border-transparent hover:border-info transition-colors"
    >
      <font-awesome-icon icon="fa-solid fa-coins" class="text-primary text-sm" />
      <span class="text-sm text-base-content/70 whitespace-nowrap">
        {{ displayDate(transaction.transaction_date) }}
        <font-awesome-icon icon="fa-solid fa-clock" class="ms-2 me-1 text-base-content/50" />
        {{ displayTime(transaction.transaction_date) }}
      </span>
      <div class="flex-1"></div>
      <span class="min-w-28 text-right font-semibold tabular-nums text-base-content">
        {{ formatCurrencySymbol(transaction.amount || 0) }}
      </span>
      <edit-icon-button
        title="Editar pagamento"
        @click="$emit('edit-transaction', transaction.id)"
      />
    </div>
  </div>
</template>

<script>
import { displayDate, displayTime } from "@/utils/date/dateUtils";
import { formatCurrencySymbol } from "@/utils/number/moneyUtils";
import EmptyState from "@/components/layout/EmptyState.vue";
import EditIconButton from "@/components/buttons/EditIconButton.vue";

export default {
  name: "TransactionsListSection",
  components: {
    EmptyState,
    EditIconButton,
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
  emits: ["edit-transaction"],
  methods: {
    displayDate,
    displayTime,
    formatCurrencySymbol,
  },
};
</script>
