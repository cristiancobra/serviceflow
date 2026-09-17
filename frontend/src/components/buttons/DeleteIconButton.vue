<template>
  <button
    type="button"
    :class="['flex items-center justify-center rounded-full bg-red-700 text-white transition hover:scale-110 hover:bg-red-500', size]"
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
        class="w-[90%] max-w-md rounded-lg bg-white shadow-xl"
        @click.stop
      >
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
          <h2 class="text-lg font-bold text-gray-900">{{ modalTitle }}</h2>
          <button
            type="button"
            title="Fechar"
            class="flex h-7 w-7 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-900"
            @click="cancel"
          >
            <font-awesome-icon icon="fa-solid fa-times" />
          </button>
        </div>

        <div class="px-6 py-6">
          <p class="text-gray-800">{{ confirmMessage }}</p>
          <p v-if="warningText" class="mt-2 text-sm text-gray-500">{{ warningText }}</p>
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">
          <button
            type="button"
            class="rounded-md bg-gray-100 px-4 py-2 font-semibold text-gray-700 transition hover:bg-gray-200"
            @click="cancel"
          >
            {{ cancelLabel }}
          </button>
          <button
            type="button"
            class="rounded-md bg-red-700 px-4 py-2 font-semibold text-white transition hover:scale-110 hover:bg-red-500"
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
