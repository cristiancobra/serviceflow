<template>
  <!--
    Botão redondo só com ícone, para ações de linha (ver, editar, excluir...).
    O @click (e .stop etc.) do pai vai direto para o <button> nativo.
    Variantes prontas: EditIconButton e DeleteIconButton.
  -->
  <button
    type="button"
    :class="['flex shrink-0 items-center justify-center rounded-full transition hover:scale-110', colorClasses[color], size]"
    :title="title"
  >
    <font-awesome-icon :icon="icon" fixed-width :class="iconSize" />
  </button>
</template>

<script setup>
defineProps({
  icon: {
    type: String,
    required: true,
  },
  title: {
    type: String,
    default: "",
  },
  color: {
    type: String,
    default: "primary",
    validator: (value) => ["primary", "info", "success", "error", "neutral"].includes(value),
  },
  size: {
    type: String,
    default: "w-7 h-7",
  },
  iconSize: {
    type: String,
    default: "text-sm",
  },
});

// Classes completas (não interpoladas) para o Tailwind encontrar
const colorClasses = {
  primary: "bg-primary text-primary-content",
  info: "bg-info text-info-content",
  success: "bg-success text-success-content",
  error: "bg-error text-error-content",
  neutral: "bg-base-300 text-base-content",
};
</script>
