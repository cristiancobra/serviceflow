# Template: Detail Modal

## 📍 Localização
`frontend/src/components/modals/details/`

## 📋 Estrutura Base

```vue
<template>
  <div>
    <div v-if="modelValue && entity" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop">
      <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
        
        <!-- Header -->
        <div class="sticky top-0 bg-gradient-to-r from-blue-50 to-blue-25 border-b border-gray-200 px-8 py-6">
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <div class="flex items-center gap-4 mb-2">
                <!-- Ícone de Status -->
                <font-awesome-icon icon="fas fa-icon-name" class="text-3xl text-primary" />
                <h3 class="text-2xl font-bold text-gray-800">{{ entity.name }}</h3>
              </div>
              
              <!-- Relacionamentos/Links -->
              <div class="flex items-center gap-2 text-sm mt-2">
                <!-- Adicione links aqui -->
              </div>
            </div>
            
            <button
              type="button"
              class="text-gray-400 hover:text-gray-600 transition-colors"
              @click="closeModal"
            >
              <font-awesome-icon icon="fa-solid fa-xmark" class="text-2xl" />
            </button>
          </div>
        </div>

        <!-- Body -->
        <div class="px-8 py-6">
          
          <!-- Cards de Informações Principais (3 colunas) -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
              <div class="flex items-center gap-2 mb-2">
                <font-awesome-icon icon="fa-solid fa-icon" class="text-primary" />
                <label class="text-sm font-semibold text-gray-700">Campo 1</label>
              </div>
              <!-- Componente de edição -->
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
              <div class="flex items-center gap-2 mb-2">
                <font-awesome-icon icon="fa-solid fa-icon" class="text-success" />
                <label class="text-sm font-semibold text-gray-700">Campo 2</label>
              </div>
              <!-- Componente de edição -->
            </div>

            <div class="bg-primary-50 rounded-lg p-4">
              <div class="flex items-center gap-2 mb-2">
                <font-awesome-icon icon="fa-solid fa-icon" class="text-primary" />
                <label class="text-sm font-semibold text-gray-700">Campo 3</label>
              </div>
              <!-- Valor calculado ou read-only -->
            </div>
          </div>

          <!-- Descrição/Texto Longo -->
          <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Descrição</label>
            <text-area-editable-input 
              name="description" 
              v-model="entity.description"
              placeholder="Adicione uma descrição detalhada"
              @save="updateEntity('description', $event)"
            />
          </div>

          <!-- Lista Relacionada (ex: Jornadas, Tarefas, etc) -->
          <div v-if="entity.relatedItems && entity.relatedItems.length > 0" class="mb-6">
            <div class="flex items-center justify-between mb-4">
              <h4 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <font-awesome-icon icon="fa-solid fa-icon" class="text-primary" />
                Items Relacionados
                <span class="text-sm font-normal text-gray-500">({{ entity.relatedItems.length }})</span>
              </h4>
            </div>
            <!-- Componente de lista -->
          </div>

          <!-- Área de Ação Destrutiva (ex: Cancelamento) -->
          <div v-if="showDestructiveArea" class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
            <h4 class="text-lg font-bold text-red-700 mb-4">Ação Destrutiva</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Campos relacionados -->
            </div>
          </div>
        </div>

        <!-- Footer com Ações -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-8 py-4">
          <div class="flex justify-between items-center">
            <!-- Ações Rápidas (esquerda) -->
            <div class="flex gap-3">
              <button
                type="button"
                class="px-4 py-2 bg-purple-500 text-white rounded-lg font-semibold hover:bg-purple-600 transition-colors"
                @click="primaryAction"
                title="Ação principal"
              >
                <font-awesome-icon icon="fa-solid fa-icon" class="me-2" />
                Ação 1
              </button>

              <button
                type="button"
                class="px-4 py-2 bg-success text-white rounded-lg font-semibold hover:bg-green-700 transition-colors"
                @click="secondaryAction"
                title="Ação secundária"
              >
                <font-awesome-icon icon="fa-solid fa-icon" class="me-2" />
                Ação 2
              </button>

              <button
                type="button"
                class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-colors"
                @click="toggleDestructiveArea"
                :title="showDestructiveArea ? 'Ocultar' : 'Mostrar'"
              >
                <font-awesome-icon icon="fa-solid fa-icon" class="me-2" />
                {{ showDestructiveArea ? 'Ocultar' : 'Ação Destrutiva' }}
              </button>
            </div>

            <!-- Botão Fechar (direita) -->
            <button
              type="button"
              class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
              @click="closeModal"
            >
              Fechar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL } from "@/config/apiConfig";

export default {
  name: "EntityDetailModal",
  components: {
    // Importar componentes necessários
  },
  props: {
    modelValue: {
      type: Boolean,
      required: true,
    },
    entityId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      entity: null,
      loading: false,
      showDestructiveArea: false,
    };
  },
  methods: {
    async loadEntity() {
      if (!this.entityId) return;
      
      this.loading = true;
      try {
        const response = await axios.get(`${BACKEND_URL}entities/${this.entityId}`);
        this.entity = response.data.data;
      } catch (error) {
        console.error("Erro ao carregar entidade:", error);
      } finally {
        this.loading = false;
      }
    },

    async updateEntity(fieldName, editedValue) {
      const updatedField = {};
      updatedField[fieldName] = editedValue;

      try {
        const response = await axios.put(
          `${BACKEND_URL}entities/${this.entityId}`,
          updatedField
        );
        this.entity = response.data.data;
        this.$emit('entity-updated', this.entity);
      } catch (error) {
        console.error("Erro ao atualizar entidade:", error);
      }
    },

    async primaryAction() {
      // Implementar ação principal
    },

    async secondaryAction() {
      // Implementar ação secundária
    },

    toggleDestructiveArea() {
      this.showDestructiveArea = !this.showDestructiveArea;
    },

    closeModal() {
      this.$emit("update:modelValue", false);
    },
  },
  watch: {
    modelValue(newVal) {
      if (newVal) {
        this.loadEntity();
      }
    },
  },
};
</script>

<style scoped>
/* Animação suave para o modal */
.fixed {
  animation: fadeIn 0.2s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* Background com desfoque */
.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px); /* Safari */
}
</style>
```

## 🎨 Padrão de Cores dos Headers

- **Azul** (`from-blue-50 to-blue-25`): Tarefas
- **Roxo** (`from-purple-50 to-purple-25`): Oportunidades
- **Verde** (`from-green-50 to-green-25`): Projetos/Success
- **Amarelo** (`from-yellow-50 to-yellow-25`): Leads/Contatos
- **Indigo** (`from-indigo-50 to-indigo-25`): Empresas

## 📦 Componentes Comuns

- `DateTimeEditableInput`: Campos de data/hora editáveis
- `TextAreaEditableInput`: Áreas de texto editáveis
- `TextEditableField`: Campos de texto simples editáveis
- `[Entity]SelectInput`: Selects para relacionamentos

## ✅ Checklist de Criação

- [ ] Criar arquivo em `/components/modals/details/`
- [ ] Ajustar nome do componente e props
- [ ] Definir cor do header
- [ ] Configurar cards de informações (3 colunas)
- [ ] Adicionar descrição/texto longo
- [ ] Implementar lista de itens relacionados (se houver)
- [ ] Configurar ações principais no footer
- [ ] Adicionar área destrutiva (se necessário)
- [ ] Importar no componente que abrirá o modal
- [ ] Testar abertura/fechamento
- [ ] Testar edição de campos
- [ ] Testar ações do footer

## 🔗 Exemplo de Uso

```vue
<template>
  <div>
    <!-- Botão que abre o modal -->
    <button @click="openEntityModal(entity.id)">
      Ver Detalhes
    </button>

    <!-- Modal -->
    <entity-detail-modal
      v-model="showEntityModal"
      :entityId="selectedEntityId"
      @entity-updated="handleEntityUpdated"
    />
  </div>
</template>

<script>
import EntityDetailModal from "@/components/modals/details/EntityDetailModal.vue";

export default {
  components: {
    EntityDetailModal,
  },
  data() {
    return {
      showEntityModal: false,
      selectedEntityId: null,
    };
  },
  methods: {
    openEntityModal(entityId) {
      this.selectedEntityId = entityId;
      this.showEntityModal = true;
    },
    handleEntityUpdated(updatedEntity) {
      // Atualizar lista local ou recarregar dados
    },
  },
};
</script>
```

## 📝 Notas

- Sempre usar `v-model` para controlar abertura/fechamento
- Emitir evento `entity-updated` quando houver mudanças
- Cards principais sempre em grid de 3 colunas (1 col em mobile)
- Footer sticky para ações sempre visíveis
- Background com blur para destacar modal
- Animação fadeIn de 0.2s
