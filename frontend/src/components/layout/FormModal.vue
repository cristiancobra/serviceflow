<template>
  <div v-if="isVisible" class="myModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <font-awesome-icon :icon="icon" class="icon primary" />
          <h5 class="modal-title">{{ title }}</h5>
          <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <slot></slot>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeModal">Fechar</button>
          <button type="button" class="btn btn-primary" @click="onSubmit">Salvar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    isVisible: {
      type: Boolean,
      required: true,
    },
    title: {
      type: String,
      default: "Formulário",
    },
    icon: {
      type: String,
      default: "fa-solid fa-tasks",
    },
  },
  emits: ["close", "submit"],
  methods: {
    closeModal() {
      this.$emit("close");
    },
    onSubmit() {
      this.$emit("submit");
    },
  },
};
</script>