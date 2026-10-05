<template>
  <!-- Sem overflow-hidden: dropdowns e popovers dos campos (position absolute) precisam vazar do card;
       por isso o arredondamento fica no próprio cabeçalho -->
  <section class="rounded-lg border border-base-300">
    <div class="flex items-center gap-2 rounded-t-[calc(var(--radius-lg)-1px)] bg-primary text-primary-content px-4 py-1">
      <h2 class="text-base font-semibold">{{ title }}</h2>
      <div v-if="$slots.actions" class="ml-auto">
        <slot name="actions" />
      </div>
    </div>
    <div class="p-4">
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
  },
};
</script>
