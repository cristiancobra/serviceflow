<template>
  <!-- Par do DeleteIconButton, para ações de linha (editar / excluir) -->
  <IconButton icon="fa-solid fa-edit" color="primary" :title="title" :size="size" :icon-size="iconSize" />
</template>

<script setup>
import IconButton from "@/components/buttons/IconButton.vue";

defineProps({
  title: {
    type: String,
    default: "Editar",
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
</script>
