<template>
  <div class="mt-8 mb-20 px-8">
    <div class="flex flex-wrap items-center justify-end gap-2 mb-4">
      <div class="flex flex-wrap items-center gap-2">
        <button-new-form
          target="debit-invoice"
          title="Nova fatura de débito"
          @open-modal="openDebitInvoiceModal"
        />
        <button-new-form
          v-if="canCreateOperationalCostInvoice"
          target="operational-cost-invoice"
          extra-icon="fa-solid fa-briefcase"
          title="Gerar fatura de custo operacional"
          @open-modal="openOperationalCostInvoiceModal"
        />
        <span
          v-else-if="proposal?.total_operational_cost > 0"
          class="badge badge-lg badge-soft gap-2 font-medium"
          :title="`Já faturado: ${formatCurrency(totalOperationalCostInvoices)} de ${formatCurrency(proposal.total_operational_cost)}`"
        >
          <font-awesome-icon icon="fa-solid fa-lock" />
          Custo operacional faturado
        </span>
        <span
          v-else
          class="badge badge-lg badge-soft gap-2 font-medium text-base-content/60"
          title="Adicione serviços com custo operacional à proposta primeiro"
        >
          <font-awesome-icon icon="fa-solid fa-info-circle" />
          Sem custo operacional definido
        </span>
      </div>
    </div>

    <!-- Mensagem quando não há faturas de débito -->
    <div
      v-if="debitInvoices.length === 0"
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
        Nenhuma fatura de custo
      </h3>
      <p class="text-sm text-base-content/60 max-w-sm">
        Use os botões acima para lançar uma despesa ou gerar a fatura de custo
        operacional desta proposta.
      </p>
    </div>

    <!-- Lista de faturas de débito -->
    <div v-else class="space-y-2">
      <div
        v-for="invoice in debitInvoices"
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
              :title="invoice.category === 'operational' ? 'Custo operacional' : 'Despesa'"
            >
              <font-awesome-icon
                :icon="invoice.category === 'operational' ? 'fa-solid fa-briefcase' : 'fa-solid fa-receipt'"
              />
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
                  @save="updateInvoice('date_due', $event, invoice)"
                  class-text="text-base font-semibold"
                />
              </div>
            </div>
            <div class="flex flex-col">
              <span class="text-sm font-medium text-base-content/70"
                >Fornecedor</span
              >
              <span class="text-base font-semibold">
                {{ supplierName(invoice) }}
              </span>
            </div>
          </div>

          <div class="flex items-center gap-6">
            <div class="flex items-center">
              <font-awesome-icon
                icon="fas fa-dollar-sign"
                class="text-base-content/50 mr-2 w-4"
              />
              <span class="font-medium mr-1 text-sm">Valor:</span>
              <span class="text-error font-bold">
                {{ formatCurrency(invoice.price) }}
              </span>
            </div>

            <div
              v-if="getInvoiceTotalPaid(invoice) > 0 && getInvoiceBalance(invoice) > 0"
              class="flex items-center"
            >
              <font-awesome-icon
                icon="fas fa-check-circle"
                class="text-base-content/50 mr-2 w-4"
              />
              <span class="font-medium mr-1 text-sm">Pago:</span>
              <span class="text-success font-bold">
                {{ formatCurrency(getInvoiceTotalPaid(invoice)) }}
              </span>
            </div>

            <div
              v-if="getInvoiceTotalPaid(invoice) > 0 && getInvoiceBalance(invoice) > 0"
              class="flex items-center"
            >
              <font-awesome-icon
                icon="fas fa-balance-scale"
                class="text-base-content/50 mr-2 w-4"
              />
              <span class="font-medium mr-1 text-sm">Saldo:</span>
              <span class="text-warning font-bold">
                {{ formatCurrency(getInvoiceBalance(invoice)) }}
              </span>
            </div>

            <div class="w-28 flex justify-end">
              <button
                v-if="getInvoiceBalance(invoice) > 0"
                type="button"
                class="btn btn-primary btn-sm"
                title="Registrar pagamento feito"
                @click.prevent.stop="openTransactionModal(invoice)"
              >
                <font-awesome-icon icon="fa-solid fa-coins" />
                Pagar
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

        <!-- Pagamentos Realizados -->
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

      <!-- Totais das faturas de débito -->
      <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-xl border border-base-300 bg-base-300 p-4 shadow-sm">
          <div class="text-xs font-semibold text-base-content/80">
            Total das Faturas
          </div>
          <div class="mt-1 text-1xl font-bold text-base-content">
            <money-field name="total" :modelValue="totalDebits" readonly />
          </div>
        </div>

        <div
          class="rounded-xl border p-4 shadow-sm"
          :class="balanceDebits > 0 ? 'border-error/30 bg-error/10' : 'border-base-300 bg-base-300'"
        >
          <div
            class="text-xs font-semibold"
            :class="balanceDebits > 0 ? 'text-error' : 'text-base-content/80'"
          >
            Saldo
          </div>
          <div
            class="mt-1 text-1xl font-bold"
            :class="balanceDebits > 0 ? 'text-error' : 'text-base-content'"
          >
            <money-field name="balance" :modelValue="balanceDebits" readonly />
          </div>
        </div>

        <div class="rounded-xl border border-info/30 bg-info/10 p-4 shadow-sm">
          <div class="text-xs font-semibold text-info">Total Pago</div>
          <div class="mt-1 text-1xl font-bold text-info">
            <money-field name="paid" :modelValue="totalPaidDebits" readonly />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

  <script>
import { mapMutations } from "vuex";
import { updateField } from "@/utils/requests/httpUtils";
import ButtonNewForm from "../buttons/ButtonNewForm.vue";
import MoneyField from "../fields/number/MoneyField.vue";
import DateEditableInput from "../fields/date/DateEditableInput.vue";
import DateTimeEditableInput from "../fields/datetime/DateTimeEditableInput.vue";

export default {
  props: {
    proposal: {
      type: Object,
      required: false,
    },
  },
  components: {
    ButtonNewForm,
    MoneyField,
    DateEditableInput,
    DateTimeEditableInput,
  },
  computed: {
    debitInvoices() {
      return (this.proposal?.invoices || [])
        .filter((invoice) => invoice.type === "debit");
    },
    // Calcula o total das faturas de custo operacional já criadas
    totalOperationalCostInvoices() {
      return this.debitInvoices.reduce((acc, inv) => {
        // Verifica se é uma fatura de custo operacional pela categoria
        const isOperationalCost = inv.category === 'operational';
        
        if (isOperationalCost) {
          return acc + (Number(inv.price) || 0);
        }
        return acc;
      }, 0);
    },
    // Verifica se pode criar nova fatura de custo operacional
    canCreateOperationalCostInvoice() {
      const totalOperationalCost = Number(this.proposal?.total_operational_cost) || 0;
      const totalInvoiced = this.totalOperationalCostInvoices;
      
      // Se não houver custo operacional definido, não permite criar
      if (totalOperationalCost === 0) {
        return false;
      }
      
      // Permite criar se o total já faturado for menor que o custo operacional total
      return totalInvoiced < totalOperationalCost;
    },
    totalDebits() {
      return this.debitInvoices.reduce((acc, inv) => {
        const price = Number(inv?.price) || 0;
        return acc + price;
      }, 0);
    },
    totalPaidDebits() {
      return this.debitInvoices.reduce((acc, inv) => {
        const transactions = inv?.transactions || [];
        const transactionsTotal = transactions.reduce((tAcc, t) => {
          return tAcc + (Number(t?.amount) || 0);
        }, 0);
        return acc + transactionsTotal;
      }, 0);
    },
    balanceDebits() {
      return this.totalDebits - this.totalPaidDebits;
    },
  },
  methods: {
    ...mapMutations(["openModal"]),
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
    openDebitInvoiceModal() {
      this.openModal({
        component: "DebitInvoiceCreateForm",
        props: { proposal: this.proposal },
        listeners: {
          "invoice-created": this.addInvoiceCreated,
        },
      });
    },
    openOperationalCostInvoiceModal() {
      this.openModal({
        component: "OperationalCostInvoiceCreateForm",
        props: { proposal: this.proposal },
        listeners: {
          "new-invoice-event": this.addInvoiceCreated,
        },
      });
    },
    addInvoiceCreated() {
      // Emite evento para o pai recarregar a proposta
      this.$emit("reload-proposal");
    },
    formatCurrency(value) {
      return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
      }).format(value);
    },
    supplierName(invoice) {
      return invoice.company?.business_name || invoice.company?.legal_name || invoice.lead?.name || "Sem fornecedor";
    },
    getInvoiceTotalPaid(invoice) {
      const transactions = invoice.transactions || [];
      return transactions.reduce((acc, t) => acc + (Number(t?.amount) || 0), 0);
    },
    getInvoiceBalance(invoice) {
      return invoice.price - this.getInvoiceTotalPaid(invoice);
    },
    // Mesmas cores da fatura de receita: paga e parcial saem das transações;
    // vencida e cancelada vêm do status calculado pela API.
    invoiceIconClass(invoice) {
      if (invoice.status === "cancelled") return "bg-base-300 text-base-content/40";
      if (this.getInvoiceBalance(invoice) <= 0) return "bg-success text-success-content";
      if (invoice.status === "overdue") return "bg-error text-error-content";
      if (this.getInvoiceTotalPaid(invoice) > 0) return "bg-warning text-warning-content";
      return "bg-info text-info-content";
    },
    async updateInvoice(fieldName, editedValue, invoice) {
      const updatedInvoice = await updateField("invoices", invoice.id, fieldName, editedValue);
      invoice[fieldName] = updatedInvoice[fieldName];
      // Mudar o vencimento pode mudar o status (vencida ou não)
      if (updatedInvoice.status) invoice.status = updatedInvoice.status;
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
      const invoiceIndex = this.debitInvoices.findIndex(
        (inv) => inv.id === newTransaction.invoice_id
      );
      if (invoiceIndex !== -1) {
        const invoice = this.debitInvoices[invoiceIndex];
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
    async updateTransaction(fieldName, editedValue, transactionId) {
      const updatedTransaction = await updateField(
        "transactions",
        transactionId,
        fieldName,
        editedValue
      );

      // Atualizar transação localmente
      for (let invoice of this.debitInvoices) {
        if (invoice.transactions) {
          const index = invoice.transactions.findIndex(
            (t) => t.id === transactionId
          );
          if (index !== -1) {
            invoice.transactions[index] = updatedTransaction;
            break;
          }
        }
      }
    },
  },
};
</script>
  
  <style scoped>
/* Estilos específicos se necessário */
</style>
