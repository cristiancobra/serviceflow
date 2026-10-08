<template>
  <!--
    Wrapper do VueDatePicker com a configuração padrão do projeto. Qualquer prop/evento do
    VueDatePicker (v-model, format, :enable-time-picker, @update:modelValue...) passa direto
    e sobrescreve os padrões abaixo.
    teleport="body": o calendário é renderizado fora do modal (não é cortado pelo overflow
    do ModalCard) mas continua posicionado junto ao campo.
  -->
  <VueDatePicker
    locale="pt-BR"
    select-text="Selecionar"
    cancel-text="Cancelar"
    teleport="body"
    auto-position
    menu-class-name="shadow-xl"
  />
</template>

<script>
import VueDatePicker from "@vuepic/vue-datepicker";

export default {
  name: "DatePicker",
  components: {
    VueDatePicker,
  },
};
</script>

<style>
/*
  Cores do calendário pelo tema DaisyUI (inclusive service-dark). Não é scoped porque o menu
  é teleportado para o body; o ":root" dá especificidade sobre o CSS da biblioteca.
*/
:root .dp__theme_light,
:root .dp__theme_dark {
  --dp-font-family: inherit;
  --dp-border-radius: var(--radius-box);
  --dp-background-color: var(--color-base-100);
  --dp-text-color: var(--color-base-content);
  --dp-hover-color: var(--color-base-200);
  --dp-hover-text-color: var(--color-base-content);
  --dp-hover-icon-color: color-mix(in oklab, var(--color-base-content) 60%, transparent);
  --dp-primary-color: var(--color-primary);
  --dp-primary-disabled-color: color-mix(in oklab, var(--color-primary) 50%, transparent);
  --dp-primary-text-color: var(--color-primary-content);
  --dp-secondary-color: color-mix(in oklab, var(--color-base-content) 40%, transparent);
  --dp-border-color: var(--color-base-300);
  --dp-menu-border-color: var(--color-base-300);
  --dp-border-color-hover: color-mix(in oklab, var(--color-base-content) 40%, transparent);
  --dp-disabled-color: var(--color-base-200);
  --dp-disabled-color-text: color-mix(in oklab, var(--color-base-content) 40%, transparent);
  --dp-scroll-bar-background: var(--color-base-200);
  --dp-scroll-bar-color: var(--color-base-300);
  --dp-success-color: var(--color-success);
  --dp-success-color-disabled: color-mix(in oklab, var(--color-success) 50%, transparent);
  --dp-icon-color: color-mix(in oklab, var(--color-base-content) 50%, transparent);
  --dp-danger-color: var(--color-error);
  --dp-marker-color: var(--color-error);
  --dp-tooltip-color: var(--color-base-100);
  --dp-highlight-color: color-mix(in oklab, var(--color-primary) 15%, transparent);
}
</style>
