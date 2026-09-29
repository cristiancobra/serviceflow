<template>
  <div class="rounded-lg border border-teal-200 bg-teal-50 p-4">
    <h3 class="text-lg font-bold text-teal-800 mb-3 flex items-center gap-2">
      <font-awesome-icon icon="fa-brands fa-pix" class="text-teal-600" />
      {{ title }}
    </h3>

    <p v-if="loading" class="text-sm text-gray-500">Gerando QR Code...</p>

    <p v-else-if="error" class="text-sm text-teal-900">
      {{ error }}
      <!-- Link para corrigir o cadastro (Configurações, fornecedor...) -->
      <slot name="error-action" />
    </p>

    <div v-else-if="pix" class="flex flex-col sm:flex-row items-center gap-4">
      <img :src="pix.qr_code" alt="QR Code Pix" class="w-44 h-44 rounded-lg bg-white p-1 shadow-sm" />
      <div class="flex-1 min-w-0 w-full">
        <p class="text-sm text-teal-900 mb-1">
          Valor: <strong><money-field name="pix_amount" :modelValue="pix.amount" readonly /></strong>
        </p>
        <p v-if="isPayable" class="text-sm text-teal-900 mb-1">
          Para: <strong>{{ pix.recipient_name }}</strong>
          <span class="text-xs text-gray-600">(chave {{ pix.pix_key }})</span>
        </p>
        <p class="text-xs text-gray-600 mb-2">
          <template v-if="isPayable">
            Escaneie com o app do banco no celular (Pix &rarr; ler QR Code) ou copie o código abaixo.
            Confira o nome do recebedor no app antes de confirmar.
          </template>
          <template v-else>
            O cliente abre o app do banco &rarr; Pix &rarr; ler QR Code, ou cola o código abaixo.
          </template>
        </p>
        <div class="flex items-stretch gap-2">
          <input
            type="text"
            readonly
            :value="pix.payload"
            class="flex-1 min-w-0 rounded-lg border border-gray-300 bg-white px-2 py-1 text-xs font-mono text-gray-700"
            @focus="$event.target.select()"
          />
          <button type="button" class="btn btn-sm btn-primary" title="Copiar Pix copia e cola" @click="copyPayload">
            <font-awesome-icon :icon="copied ? 'fa-solid fa-check' : 'fa-solid fa-copy'" class="text-white" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import MoneyField from "@/components/fields/number/MoneyField.vue";

/**
 * Exibe um Pix gerado pelo backend (endpoints invoices/{id}/pix e invoices/pix-batch):
 * QR Code, valor, recebedor e o copia e cola. Quem usa cuida de buscar o Pix.
 */
export default {
  name: "PixPaymentCard",
  components: { MoneyField },
  props: {
    title: { type: String, required: true },
    // { payload, qr_code, amount, pix_key, recipient_name }
    pix: { type: Object, default: null },
    loading: { type: Boolean, default: false },
    error: { type: String, default: null },
    // Conta a pagar: mostra o recebedor e a orientação para quem vai pagar
    isPayable: { type: Boolean, default: false },
  },
  data() {
    return { copied: false };
  },
  methods: {
    async copyPayload() {
      await navigator.clipboard.writeText(this.pix.payload);
      this.copied = true;
      setTimeout(() => (this.copied = false), 2000);
    },
  },
};
</script>
