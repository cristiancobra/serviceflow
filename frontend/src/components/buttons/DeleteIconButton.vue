<template>
  <!-- button/label: botão DaisyUI (ex: rodapé de modal); senão: ícone redondo de linha -->
  <button
    v-if="button || label"
    type="button"
    class="btn btn-error"
    :title="title"
    @click="openModal"
  >
    <font-awesome-icon icon="fa-solid fa-trash" />
    <span v-if="label">{{ label }}</span>
  </button>
  <IconButton
    v-else
    icon="fa-solid fa-trash-alt"
    color="error"
    :title="title"
    :size="size"
    :icon-size="iconSize"
    @click="openModal"
  />

  <teleport to="body">
    <!-- z-[1000]: este botão também é usado dentro de outros modais (z-50) -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm"
      @click.self="cancel"
    >
      <ModalCard :title="modalTitle" icon="fa-solid fa-trash" size="sm" @close="cancel">
        <p class="text-base-content">{{ confirmMessage }}</p>
        <p v-if="warningText" class="mt-2 text-sm text-base-content/60">{{ warningText }}</p>

        <template #footer>
          <button type="button" class="btn btn-ghost" @click="cancel">{{ cancelLabel }}</button>
          <button type="button" class="btn btn-error" @click="confirmDelete">{{ confirmLabel }}</button>
        </template>
      </ModalCard>
    </div>
  </teleport>
</template>

<script setup>
import { ref } from "vue";
import ModalCard from "@/components/modals/ModalCard.vue";
import IconButton from "@/components/buttons/IconButton.vue";

defineProps({
  // Texto do botão; implica o formato de botão
  label: {
    type: String,
    default: "",
  },
  // Formato de botão (btn btn-error) mesmo sem texto
  button: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: "Excluir",
  },
  modalTitle: {
    type: String,
    default: "Confirmar Exclusão",
  },
  confirmMessage: {
    type: String,
    default: "Tem certeza que deseja excluir este item?",
  },
  warningText: {
    type: String,
    default: "Esta ação não poderá ser desfeita.",
  },
  confirmLabel: {
    type: String,
    default: "Excluir",
  },
  cancelLabel: {
    type: String,
    default: "Cancelar",
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

const emit = defineEmits(["confirm"]);

const showModal = ref(false);

function openModal() {
  showModal.value = true;
}

function cancel() {
  showModal.value = false;
}

function confirmDelete() {
  showModal.value = false;
  emit("confirm");
}
</script>
