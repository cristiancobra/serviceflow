<template>
  <section class="rounded-lg border border-base-300 overflow-hidden">
    <div class="flex items-center gap-2 bg-primary text-primary-content px-6 py-1.5">
      <font-awesome-icon v-if="icon" :icon="icon" class="text-base" />
      <h2 class="text-lg font-semibold">{{ title }}</h2>
      <div v-if="$slots.actions" class="ml-auto">
        <slot name="actions" />
      </div>
    </div>
    <div class="p-6">
      <slot />
    </div>
  </section>
</template>

<script>
// Card de seção com cabeçalho na cor primária, no mesmo estilo do cabeçalho do ModalCard
export default {
  name: "SectionCard",
  props: {
    title: {
      type: String,
      required: true,
    },
    icon: {
      type: String,
      default: "",
    },
  },
};
</script>
