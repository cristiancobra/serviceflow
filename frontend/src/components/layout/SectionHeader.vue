<template>
  <div class="flex items-center gap-4 mb-6">
    <div class="flex-1 flex items-center text-primary">
      <font-awesome-icon
        v-if="icon"
        :icon="icon"
        class="text-[0.9rem] text-primary-content bg-primary rounded-full mr-4 p-[0.6rem] shrink-0"
      />
      <h2 class="text-[1.4rem] font-extrabold text-left">
        <slot name="title">{{ title }}</slot>
      </h2>
    </div>

    <div v-if="$slots.actions" class="flex items-center gap-2">
      <slot name="actions" />
    </div>
  </div>
</template>

<script>
// Cabeçalho de seção dentro de uma página: ícone em círculo, título na cor primária e,
// opcionalmente, ações à direita. Para seção em card com faixa colorida, use SectionCard.
export default {
  name: "SectionHeader",
  props: {
    title: {
      type: String,
      default: "",
    },
    icon: {
      type: String,
      default: "",
    },
  },
};
</script>
