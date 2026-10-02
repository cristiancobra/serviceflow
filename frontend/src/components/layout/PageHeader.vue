<template>
  <div class="flex items-center justify-center mb-8 pt-4 px-8 pb-[0.4rem] rounded-t-[14px] bg-primary">
    <div class="flex-[12] flex items-center justify-start">
      <button
        v-if="showBack"
        type="button"
        class="p-2 mr-2 text-white/80 hover:text-white transition-colors cursor-pointer"
        title="Voltar"
        @click="$emit('back')"
      >
        <font-awesome-icon icon="fa-solid fa-arrow-left" />
      </button>
      <font-awesome-icon
        v-if="icon"
        :icon="icon"
        class="text-[1.2rem] bg-base-100 rounded-full mr-[0.8rem] p-[0.3rem] shrink-0"
        :class="iconClass"
      />
      <h1 class="text-[1.4rem] leading-10 font-extrabold text-left text-white">
        <slot name="title">{{ title }}</slot>
      </h1>
    </div>

    <div v-if="$slots.actions" class="flex-[2] flex justify-center gap-3">
      <slot name="actions" />
    </div>
  </div>
</template>

<script>
// Cabeçalho padrão das páginas: faixa na cor primária com ícone, título e,
// opcionalmente, botão de voltar e ações à direita.
export default {
  name: "PageHeader",
  props: {
    title: {
      type: String,
      default: "",
    },
    icon: {
      type: String,
      default: "",
    },
    // Cor do ícone dentro do círculo claro (ex: "text-error" em Contas a Pagar)
    iconClass: {
      type: String,
      default: "text-primary",
    },
    // Mostra a seta de voltar; a página decide o destino tratando o evento @back
    showBack: {
      type: Boolean,
      default: false,
    },
  },
  emits: ["back"],
};
</script>
