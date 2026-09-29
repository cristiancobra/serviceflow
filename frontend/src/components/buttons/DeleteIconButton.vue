<template>
  <button
    type="button"
    :class="['flex items-center justify-center rounded-full bg-error text-white transition hover:scale-110 hover:bg-error', size]"
    :title="title"
    @click="openModal"
  >
    <font-awesome-icon icon="fa-solid fa-trash-alt" fixed-width :class="iconSize" />
  </button>

  <teleport to="body">
    <div
      v-if="showModal"
      class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/50"
      @click="cancel"
    >
      <div
        class="w-[90%] max-w-md rounded-lg bg-base-100 shadow-xl"
        @click.stop
      >
        <div class="flex items-center justify-between border-b border-base-300 px-6 py-4">
          <h2 class="text-lg font-bold text-base-content">{{ modalTitle }}</h2>
          <button
            type="button"
            title="Fechar"
            class="flex h-7 w-7 items-center justify-center rounded-md text-base-content/60 hover:bg-base-200 hover:text-base-content"
            @click="cancel"
          >
            <font-awesome-icon icon="fa-solid fa-times" />
          </button>
        </div>

        <div class="px-6 py-6">
          <p class="text-base-content">{{ confirmMessage }}</p>
          <p v-if="warningText" class="mt-2 text-sm text-base-content/60">{{ warningText }}</p>
        </div>

        <div class="flex justify-end gap-3 border-t border-base-300 px-6 py-4">
          <button
            type="button"
            class="rounded-md bg-base-200 px-4 py-2 font-semibold text-base-content/80 transition hover:bg-base-300"
            @click="cancel"
          >
            {{ cancelLabel }}
          </button>
          <button
            type="button"
            class="rounded-md bg-error px-4 py-2 font-semibold text-white transition hover:scale-110 hover:bg-error"
            @click="confirmDelete"
          >
            {{ confirmLabel }}
          </button>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
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
