<template>
  <div>
    <div class="mt-8 mb-20 px-8">
      <div class="flex flex-wrap items-center justify-end gap-2 mb-4">
        <div>
          <credit-invoice-create-form
            @new-invoice-event="addInvoiceCreated"
            :proposal="proposal"
          />
        </div>
      </div>

      <!-- Mensagem quando não há faturas -->
      <div
        v-if="localInvoices.length === 0"
        class="flex flex-col items-center justify-center py-8 px-4 text-center bg-base-200 rounded-lg border-2 border-dashed border-base-300"
      >
        <div
          class="w-16 h-16 bg-base-300 rounded-full flex items-center justify-center mb-4"
        >
          <font-awesome-icon
            icon="fa-solid fa-file-invoice"
            class="text-2xl text-base-content/50"
          />
        </div>
        <h3 class="text-lg font-medium text-base-content/70 mb-2">
          Nenhuma fatura criada
        </h3>
        <p class="text-sm text-base-content/60 max-w-sm">
          Use o botão acima para gerar as faturas desta proposta
          automaticamente.
        </p>
      </div>

      <!-- Lista de faturas existentes -->
      <div v-else class="space-y-2">
        <div
          v-for="invoice in localInvoices"
          :key="invoice.id"
          class="bg-base-200 rounded-lg border border-base-300 hover:border-primary/40 hover:shadow-md transition-all duration-200 cursor-pointer"
          title="Ver detalhes da fatura"
          @click="openInvoiceModal(invoice)"
        >
          <div class="flex items-center justify-between p-2 text-base-content">
            <div class="flex items-center gap-4">
              <div
                :class="invoiceIconClass(invoice)"
                class="flex items-center justify-center w-10 h-10 rounded-full"
              >
                <font-awesome-icon icon="fa fa-receipt" />
              </div>
              <div class="flex flex-col">
                <span class="text-sm font-medium text-base-content/70"
                  >Vencimento</span
                >
                <!-- @click.stop: editar a data não abre o modal da fatura -->
                <div @click.stop>
                  <date-editable-input
                    name="date_due"
                    :modelValue="invoice.date_due"
                    @save="updateInvoice('date_due', $event, invoice.id)"
                    class-text="text-base font-semibold"
                  />
                </div>
              </div>
            </div>

            <div class="flex items-center gap-6">
              <div class="flex items-center">
                <font-awesome-icon
                  icon="fas fa-dollar-sign"
                  class="text-base-content/50 mr-2 w-4"
                />
                <span class="font-medium mr-1 text-sm">Valor:</span>
                <span class="text-info font-bold">
                  {{ formatCurrency(invoice.price) }}
                </span>
              </div>

              <div
                v-if="invoice.total_paid > 0 && calculateInvoiceBalance(invoice) > 0"
                class="flex items-center"
              >
                <font-awesome-icon
                  icon="fas fa-check-circle"
                  class="text-base-content/50 mr-2 w-4"
                />
                <span class="font-medium mr-1 text-sm">Pago:</span>
                <span class="text-success font-bold">
                  {{ formatCurrency(invoice.total_paid || 0) }}
                </span>
              </div>

              <div
                v-if="calculateInvoiceBalance(invoice) > 0 && calculateInvoiceBalance(invoice) !== Number(invoice.price)"
                class="flex items-center"
              >
                <font-awesome-icon
                  icon="fas fa-balance-scale"
                  class="text-base-content/50 mr-2 w-4"
                />
                <span class="font-medium mr-1 text-sm">Saldo:</span>
                <span class="text-warning font-bold">
                  {{ formatCurrency(calculateInvoiceBalance(invoice)) }}
                </span>
              </div>

              <div class="w-28 flex justify-end">
                <button
                  v-if="invoice.balance > 0"
                  type="button"
                  class="btn btn-primary btn-sm"
                  title="Registrar pagamento recebido"
                  @click.prevent.stop="openTransactionModal(invoice)"
                >
                  <font-awesome-icon icon="fa-solid fa-coins" />
                  Receber
                </button>
                <span
                  v-else
                  class="badge badge-lg badge-success badge-soft gap-2 font-semibold"
                  title="Fatura quitada"
                >
                  <font-awesome-icon icon="fa-solid fa-circle-check" />
                  Pago
                </span>
              </div>
            </div>
          </div>

          <!-- Pagamentos Recebidos -->
          <div
            v-if="invoice.transactions && invoice.transactions.length > 0"
            class="mx-3 mb-3 space-y-1 rounded-lg bg-base-100 p-2"
          >
            <div
              v-for="transaction in invoice.transactions"
              :key="transaction.id"
              class="flex items-center justify-end gap-3 px-2 py-1 rounded-md hover:bg-info/10 border-l-4 border-transparent hover:border-info transition-colors"
            >
              <font-awesome-icon
                icon="fa-solid fa-coins"
                class="text-primary text-sm"
              />
              <date-time-editable-input
                @click.stop
                name="transaction_date"
                :modelValue="transaction.transaction_date"
                @save="
                  updateTransaction(
                    'transaction_date',
                    $event,
                    transaction.id
                  )
                "
                class-text="text-sm text-base-content/70"
              />
              <span
                class="min-w-28 text-right font-semibold tabular-nums text-base-content"
              >
                {{ formatCurrency(transaction.amount) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Totais das faturas -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Total das Faturas - Preto/Cinza -->
          <div
            class="rounded-xl border border-base-300 bg-base-300 p-4 shadow-sm"
          >
            <div class="text-xs font-semibold text-base-content/80 ">
              Total das Faturas
            </div>
            <div class="mt-1 text-1xl font-bold text-base-content">
              <money-field name="total" :modelValue="totalInvoices" readonly />
            </div>
          </div>

          <!-- Saldo - Vermelho se positivo, Cinza se zero -->
          <div
            class="rounded-xl border p-4 shadow-sm"
            :class="balance > 0 ? 'border-error/30 bg-error/10' : 'border-base-300 bg-base-300'"
          >
            <div
              class="text-xs font-semibold"
              :class="balance > 0 ? 'text-error' : 'text-base-content/80'"
            >
              Saldo
            </div>
            <div
              class="mt-1 text-1xl font-bold"
              :class="balance > 0 ? 'text-error' : 'text-base-content'"
            >
              <money-field
                name="balance"
                :modelValue="balance"
                readonly />
            </div>
          </div>

                    
          <!-- Total Recebido - Azul -->
          <div
            class="rounded-xl border border-info/30 bg-info/10 p-4 shadow-sm"
          >
            <div class="text-xs font-semibold text-info">Total Recebido</div>
            <div class="mt-1 text-1xl font-bold text-info">
              <money-field name="paid" :modelValue="totalPaid" readonly />
            </div>
          </div>
          

          
        </div>
      </div>
    </div>

  </div>
</template>

  <script>
import { mapMutations } from "vuex";
import { updateField } from "@/utils/requests/httpUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import DateEditableInput from "../fields/date/DateEditableInput.vue";
import DateTimeEditableInput from "../fields/datetime/DateTimeEditableInput.vue";
import CreditInvoiceCreateForm from "@/components/forms/CreditInvoiceCreateForm.vue";
import MoneyField from "../fields/number/MoneyField.vue";

export default {
  props: {
    proposal: {
      type: Object,
      required: false,
    },
  },
  data() {
    return {
      localInvoices: [],
    };
  },
  components: {
    DateEditableInput,
    DateTimeEditableInput,
    CreditInvoiceCreateForm,
    MoneyField,
  },
  watch: {
    "proposal.invoices": {
      handler(newInvoices) {
        // Filtra apenas faturas de CRÉDITO (recebimento)
        this.localInvoices = (newInvoices || []).filter(inv => inv.type === 'credit');
      },
      deep: true,
      immediate: true,
    },
  },
  computed: {
    totalInvoices() {
      return this.localInvoices.reduce(
        (acc, inv) => acc + Number(inv?.price ?? 0),
        0
      );
    },
    totalPaid() {
      return this.localInvoices.reduce(
        (acc, inv) => acc + Number(inv?.total_paid ?? 0),
        0
      );
    },
    balance() {
      return this.totalInvoices - this.totalPaid;
    },
  },
  methods: {
    ...mapMutations(["openModal"]),
    formatDateBr,
    addInvoiceCreated(newInvoices) {
      // Adiciona as novas faturas aos dados locais
      if (Array.isArray(newInvoices)) {
        this.localInvoices.push(...newInvoices);
      } else {
        this.localInvoices.push(newInvoices);
      }

      // Emite evento para o componente pai atualizar a proposta original
      this.$emit("invoices-updated", this.localInvoices);
    },
    openInvoiceModal(invoice) {
      this.openModal({
        component: "InvoiceDetailModal",
        props: { invoiceId: invoice.id },
        listeners: {
          "invoice-updated": () => this.$emit("reload-proposal"),
          "invoice-deleted": () => this.$emit("reload-proposal"),
        },
        id: `invoice-${invoice.id}`,
      });
    },
    openTransactionModal(invoice) {
      this.openModal({
        component: "TransactionCreateForm",
        props: { invoice },
        listeners: {
          "new-transaction-event": this.handleNewTransaction,
        },
      });
    },
    handleNewTransaction(newTransaction) {
      // Encontrar a invoice correspondente e adicionar a transação
      const invoice = this.localInvoices.find(
        (inv) => inv.id === newTransaction.invoice_id
      );
      if (invoice) {
        if (!invoice.transactions) {
          invoice.transactions = [];
        }
        invoice.transactions.push(newTransaction);
        // Atualizar total_paid E balance
        invoice.total_paid = (invoice.total_paid || 0) + Number(newTransaction.amount);
        invoice.balance = invoice.price - invoice.total_paid;
      }
      // Emite evento para o pai recarregar a proposta
      this.$emit("reload-proposal");
    },
    formatCurrency(value) {
      return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
      }).format(value);
    },
    calculateInvoiceBalance(invoice) {
      return invoice.price - (invoice.total_paid || 0);
    },
    // Paga e parcial saem do saldo, que a tela recalcula ao registrar pagamento;
    // vencida e cancelada vêm do status calculado pela API.
    invoiceIconClass(invoice) {
      if (invoice.status === "cancelled") return "bg-base-300 text-base-content/40";
      if (this.calculateInvoiceBalance(invoice) <= 0) return "bg-success text-success-content";
      if (invoice.status === "overdue") return "bg-error text-error-content";
      if (invoice.total_paid > 0) return "bg-warning text-warning-content";
      return "bg-info text-info-content";
    },
    async updateTransaction(fieldName, editedValue, transactionId) {
      const updatedTransaction = await updateField(
        "transactions",
        transactionId,
        fieldName,
        editedValue
      );

      // Encontrar a invoice que contém esta transação
      for (const invoice of this.localInvoices) {
        if (!invoice.transactions) continue;

        const transactionIndex = invoice.transactions.findIndex(
          (t) => t.id === transactionId
        );

        if (transactionIndex !== -1) {
          // Atualizar a transação
          invoice.transactions[transactionIndex] = updatedTransaction;

          // Recalcular o total_paid da invoice
          invoice.total_paid = invoice.transactions.reduce(
            (sum, t) => sum + Number(t.amount || 0),
            0
          );
          break;
        }
      }

      // Emitir evento para atualizar o componente pai
      this.$emit("invoices-updated", this.localInvoices);
    },
    async updateInvoice(fieldName, editedValue, invoiceId) {
      const updatedInvoice = await updateField(
        "invoices",
        invoiceId,
        fieldName,
        editedValue
      );

      const index = this.localInvoices.findIndex((inv) => inv.id === invoiceId);

      if (index !== -1) {
        this.localInvoices[index] = updatedInvoice;
      }

      this.$emit("invoices-updated", this.localInvoices);
    },
  },
};
</script>
  
  <style scoped>
/* Removi todas as classes CSS personalizadas que foram substituídas por Tailwind */
</style>