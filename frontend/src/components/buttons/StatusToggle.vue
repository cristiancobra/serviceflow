<template>
  <component
    :is="readonly ? 'span' : 'button'"
    :type="readonly ? undefined : 'button'"
    class="badge badge-soft badge-sm rounded-full font-semibold"
    :class="[active ? 'badge-success' : 'badge-error', { 'cursor-pointer hover:brightness-95': !readonly }]"
    :title="readonly ? undefined : (active ? 'Clique para desativar' : 'Clique para ativar')"
    @click="!readonly && $emit('toggle')"
  >
    {{ active ? activeLabel : inactiveLabel }}
  </component>
</template>

<script>
// Badge de ativo/inativo. Clicável (emite @toggle) nas listas; com readonly vira só indicador.
export default {
  name: "StatusToggle",
  props: {
    active: {
      type: Boolean,
      required: true,
    },
    activeLabel: {
      type: String,
      default: "Ativo",
    },
    inactiveLabel: {
      type: String,
      default: "Inativo",
    },
    readonly: {
      type: Boolean,
      default: false,
    },
  },
  emits: ["toggle"],
};
</script>
