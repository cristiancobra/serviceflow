<template>
  <div
    class="modal-card bg-base-100 rounded-2xl shadow-2xl w-full max-h-[90vh] overflow-y-auto"
    :class="compact ? compactSize : sizeClass"
  >
    <div class="modal-card-header">
      <div class="modal-card-header-title">
        <font-awesome-icon v-if="icon" :icon="icon" class="modal-card-icon" />
        <div class="min-w-0 flex-1">
          <h2 :class="{ 'modal-card-title-label': subtitle || subtitleEditable }">{{ title }}</h2>
          <HeaderEditableField
            v-if="subtitleEditable"
            class="modal-card-subtitle"
            :model-value="subtitle"
            :placeholder="subtitlePlaceholder"
            :empty-text="subtitlePlaceholder || 'sem nome'"
            @save="$emit('save-subtitle', $event)"
          />
          <p v-else-if="subtitle" class="modal-card-subtitle">{{ subtitle }}</p>
        </div>
      </div>
      <button
        type="button"
        class="modal-card-close"
        @click="$emit('close')"
        aria-label="Fechar"
      >
        <font-awesome-icon icon="fa-solid fa-xmark" />
      </button>
    </div>

    <div class="modal-card-body">
      <slot />
    </div>

    <div v-if="$slots.footer" class="modal-card-footer">
      <slot name="footer" />
    </div>
  </div>
</template>

<script>
import HeaderEditableField from "@/components/fields/text/HeaderEditableField.vue";

export default {
  name: "ModalCard",
  components: {
    HeaderEditableField,
  },
  props: {
    title: {
      type: String,
      default: "",
    },
    subtitle: {
      type: String,
      default: "",
    },
    // Nome do elemento editável direto no cabeçalho (emite @save-subtitle com o novo valor).
    // Usar nos modais de detalhe, em vez de repetir o nome editável no conteúdo.
    subtitleEditable: {
      type: Boolean,
      default: false,
    },
    subtitlePlaceholder: {
      type: String,
      default: "",
    },
    icon: {
      type: String,
      default: "",
    },
    size: {
      type: String,
      default: "md",
      validator: (value) => ["sm", "md", "lg", "xl"].includes(value),
    },
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
    // Largura usada quando compact é true. Modais com conteúdo mais largo (tabelas, listas)
    // podem passar algo maior que o padrão max-w-md.
    compactSize: {
      type: String,
      default: "max-w-md",
    },
  },
  emits: ["close", "save-subtitle"],
  computed: {
    sizeClass() {
      return {
        sm: "max-w-md",
        md: "max-w-2xl",
        lg: "max-w-4xl",
        xl: "max-w-6xl",
      }[this.size];
    },
  },
};
</script>

<style scoped>
.modal-card {
  animation: modal-card-fade-in 0.2s ease-in-out;
}

@keyframes modal-card-fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.modal-card-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  background-color: var(--color-primary);
  border-radius: 1rem 1rem 0 0;
  padding: 1.5rem 2rem;
  position: sticky;
  top: 0;
  z-index: 1;
}

.modal-card-header-title {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex: 1;
  min-width: 0; /* deixa nomes longos quebrarem a linha sem empurrar o X para fora */
}

.modal-card-header-title h2 {
  color: white;
  font-size: 1.4rem;
  font-weight: 800;
  margin: 0;
}

/* Com subtítulo, o título vira um rótulo pequeno (o tipo: "Tarefa", "Oportunidade")
   e o subtítulo (o nome do elemento) passa a ser o texto principal */
.modal-card-header-title h2.modal-card-title-label {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.modal-card-subtitle {
  color: white;
  font-size: 1.4rem;
  font-weight: 800;
  line-height: 1.3;
  margin: 0.125rem 0 0;
}

.modal-card-icon {
  font-size: 1.2rem;
  color: var(--color-primary);
  background-color: var(--color-base-100);
  border-radius: 50%;
  padding: 0.6rem;
  flex-shrink: 0;
}

.modal-card-close {
  color: rgba(255, 255, 255, 0.8);
  transition: color 0.2s;
  flex-shrink: 0;
  font-size: 1.25rem;
  background: transparent;
  border: none;
  cursor: pointer;
}

.modal-card-close:hover {
  color: white;
}

.modal-card-body {
  padding: 2rem;
}

.modal-card-footer {
  position: sticky;
  bottom: 0;
  background-color: var(--color-base-200);
  border-top: 1px solid var(--color-base-300);
  padding: 1rem 2rem;
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  border-radius: 0 0 1rem 1rem;
}
</style>
