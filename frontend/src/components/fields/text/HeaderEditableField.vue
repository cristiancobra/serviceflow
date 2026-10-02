<template>
  <div class="min-w-0">
    <!-- div com role="button" (e não <button>) para não herdar regras globais de botão dos modais -->
    <div
      v-if="!editing"
      role="button"
      tabindex="0"
      class="group inline-flex items-center gap-2 text-left cursor-text rounded px-1 -mx-1 hover:bg-white/10 focus-visible:outline focus-visible:outline-white/60 transition-colors"
      title="Clique para editar"
      @click="startEditing"
      @keydown.enter.prevent="startEditing"
    >
      <span class="break-words" :class="{ 'text-white/60 italic': !modelValue }">
        {{ modelValue || emptyText }}
      </span>
      <font-awesome-icon
        icon="fa-solid fa-pen"
        class="text-xs text-white/60 opacity-0 group-hover:opacity-100 transition-opacity shrink-0"
      />
    </div>
    <input
      v-else
      ref="input"
      v-model="localValue"
      type="text"
      class="w-full rounded-md border border-white/50 bg-white/15 px-2 py-1 text-white placeholder:text-white/60 focus:border-white focus:outline-none focus:ring-2 focus:ring-white/40"
      :placeholder="placeholder"
      @keydown.esc.stop="cancelEditing"
      @keydown.enter.prevent="save"
      @blur="save"
    />
  </div>
</template>

<script>
// Versão do TextEditableField para a faixa roxa do cabeçalho do ModalCard
// (texto branco sobre a cor primária). Clique edita, Enter ou sair do campo salva, Esc cancela.
export default {
  name: "HeaderEditableField",
  props: {
    modelValue: {
      type: [String, Number],
      default: "",
    },
    placeholder: {
      type: String,
      default: "",
    },
    emptyText: {
      type: String,
      default: "sem nome",
    },
  },
  emits: ["save"],
  data() {
    return {
      editing: false,
      localValue: this.modelValue,
    };
  },
  methods: {
    startEditing() {
      this.localValue = this.modelValue;
      this.editing = true;
      this.$nextTick(() => this.$refs.input?.focus());
    },
    save() {
      if (!this.editing) return;
      this.editing = false;
      if (this.localValue !== this.modelValue) {
        this.$emit("save", this.localValue);
      }
    },
    cancelEditing() {
      this.localValue = this.modelValue;
      this.editing = false;
    },
  },
  watch: {
    modelValue(newValue) {
      this.localValue = newValue;
    },
  },
};
</script>
