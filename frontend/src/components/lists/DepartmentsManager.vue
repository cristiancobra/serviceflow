<template>
  <div class="departments-manager">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-xl font-bold text-gray-800">Departamentos</h3>
      <button
        @click="toggleAddForm"
        class="px-4 py-2 bg-primary hover:opacity-90 text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md flex items-center gap-2"
      >
        <font-awesome-icon :icon="showAddForm ? 'fa-solid fa-times' : 'fa-solid fa-plus'" />
        {{ showAddForm ? 'Cancelar' : 'Novo Departamento' }}
      </button>
    </div>

    <!-- Formulário de adicionar novo departamento -->
    <div v-if="showAddForm" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
      <form @submit.prevent="createDepartment" class="space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
            <input
              v-model="newDepartment.name"
              type="text"
              required
              placeholder="Ex: Comercial"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cor</label>
            <div class="flex gap-2">
              <input
                v-model="newDepartment.color"
                type="color"
                class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
              />
              <input
                v-model="newDepartment.color"
                type="text"
                placeholder="#3B82F6"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ícone (Font Awesome)</label>
            <input
              v-model="newDepartment.icon"
              type="text"
              placeholder="fa-cogs"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
            <input
              v-model.number="newDepartment.order"
              type="number"
              min="0"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Descrição (opcional)</label>
          <input
            v-model="newDepartment.description"
            type="text"
            placeholder="Descrição do departamento"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
        <div class="flex gap-2 justify-end">
          <cancel-button @click="toggleAddForm" />
          <button
            type="submit"
            class="px-4 py-2 bg-success hover:opacity-90 text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md flex items-center gap-2"
          >
            <font-awesome-icon icon="fa-solid fa-check" />
            Criar Departamento
          </button>
        </div>
      </form>
    </div>

    <!-- Lista de departamentos -->
    <div class="space-y-2">
      <div
        v-for="department in sortedDepartments"
        :key="department.id"
        class="p-4 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow"
      >
        <div v-if="editingId !== department.id" class="flex items-center justify-between">
          <div class="flex items-center gap-4 flex-1">
            <div
              class="w-12 h-12 rounded-full flex items-center justify-center text-white text-xl"
              :style="{ backgroundColor: department.color }"
            >
              <font-awesome-icon v-if="department.icon" :icon="`fa-solid ${department.icon}`" />
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <h4 class="font-bold text-lg" :style="{ color: department.color }">
                  {{ department.name }}
                </h4>
                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                  Ordem: {{ department.order }}
                </span>
                <span
                  v-if="!department.active"
                  class="text-xs text-red-600 bg-red-100 px-2 py-1 rounded font-semibold"
                >
                  Inativo
                </span>
              </div>
              <p v-if="department.description" class="text-sm text-gray-600 mt-1">
                {{ department.description }}
              </p>
              <p class="text-xs text-gray-400 mt-1">
                Slug: {{ department.slug }} | Cor: {{ department.color }}
              </p>
            </div>
          </div>
          <div class="flex gap-2">
            <button
              @click="startEdit(department)"
              class="px-3 py-2 bg-primary hover:opacity-90 text-white rounded-lg transition-all duration-200"
              title="Editar"
            >
              <font-awesome-icon icon="fa-solid fa-edit" />
            </button>
            <button
              @click="toggleActive(department)"
              :class="department.active 
                ? 'bg-warning hover:opacity-90' 
                : 'bg-success hover:opacity-90'"
              class="px-3 py-2 text-white rounded-lg transition-all duration-200"
              :title="department.active ? 'Desativar' : 'Ativar'"
            >
              <font-awesome-icon :icon="department.active ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" />
            </button>
          </div>
        </div>

        <!-- Formulário de edição -->
        <div v-else class="space-y-3">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
              <input
                v-model="editForm.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Cor</label>
              <div class="flex gap-2">
                <input
                  v-model="editForm.color"
                  type="color"
                  class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
                />
                <input
                  v-model="editForm.color"
                  type="text"
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Ícone (Font Awesome)</label>
              <input
                v-model="editForm.icon"
                type="text"
                placeholder="fa-cogs"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
              <input
                v-model.number="editForm.order"
                type="number"
                min="0"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
            <input
              v-model="editForm.description"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          <div class="flex gap-2 justify-end">
            <cancel-button @click="cancelEdit" />
            <save-button @click="saveDepartment" />
          </div>
        </div>
      </div>

      <div v-if="departments.length === 0" class="text-center py-8 text-gray-500">
        Nenhum departamento cadastrado
      </div>
    </div>
  </div>
</template>

<script>
import { index, submitFormCreate, submitFormUpdate } from "@/utils/requests/httpUtils";
import CancelButton from "@/components/buttons/CancelButton.vue";
import SaveButton from "@/components/buttons/SaveButton.vue";

export default {
  name: "DepartmentsManager",
  components: {
    CancelButton,
    SaveButton,
  },
  data() {
    return {
      departments: [],
      showAddForm: false,
      editingId: null,
      newDepartment: {
        name: "",
        color: "#3B82F6",
        icon: "fa-folder",
        description: "",
        order: 0,
        active: true,
      },
      editForm: {},
    };
  },
  computed: {
    sortedDepartments() {
      return [...this.departments].sort((a, b) => a.order - b.order);
    },
  },
  methods: {
    async getDepartments() {
      try {
        const data = await index("departments");
        this.departments = data || [];
      } catch (error) {
        console.error("Erro ao buscar departamentos:", error);
        this.departments = [];
      }
    },
    toggleAddForm() {
      this.showAddForm = !this.showAddForm;
      if (this.showAddForm) {
        this.resetNewDepartment();
      }
    },
    resetNewDepartment() {
      this.newDepartment = {
        name: "",
        color: "#3B82F6",
        icon: "fa-folder",
        description: "",
        order: this.departments.length,
        active: true,
      };
    },
    async createDepartment() {
      try {
        const slug = this.newDepartment.name
          .toLowerCase()
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/[^\w\s-]/g, "")
          .replace(/\s+/g, "-");

        const { data, error } = await submitFormCreate("departments", {
          ...this.newDepartment,
          slug,
        });

        if (data) {
          this.departments.push(data);
          this.showAddForm = false;
          this.resetNewDepartment();
          this.$emit("department-created", data);
        } else if (error) {
          console.error("Erro ao criar departamento:", error);
          alert("Erro ao criar departamento");
        }
      } catch (error) {
        console.error("Erro ao criar departamento:", error);
        alert("Erro ao criar departamento");
      }
    },
    startEdit(department) {
      this.editingId = department.id;
      this.editForm = { ...department };
    },
    cancelEdit() {
      this.editingId = null;
      this.editForm = {};
    },
    async saveDepartment() {
      try {
        const slug = this.editForm.name
          .toLowerCase()
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/[^\w\s-]/g, "")
          .replace(/\s+/g, "-");

        const { data, error } = await submitFormUpdate("departments", this.editingId, {
          ...this.editForm,
          slug,
        });

        if (data) {
          const index = this.departments.findIndex((d) => d.id === this.editingId);
          if (index !== -1) {
            this.departments.splice(index, 1, data);
          }

          this.cancelEdit();
          this.$emit("department-updated", data);
        } else if (error) {
          console.error("Erro ao salvar departamento:", error);
          alert("Erro ao salvar departamento");
        }
      } catch (error) {
        console.error("Erro ao salvar departamento:", error);
        alert("Erro ao salvar departamento");
      }
    },
    async toggleActive(department) {
      try {
        const { data, error } = await submitFormUpdate("departments", department.id, {
          ...department,
          active: !department.active,
        });

        if (data) {
          const index = this.departments.findIndex((d) => d.id === department.id);
          if (index !== -1) {
            this.departments.splice(index, 1, data);
          }

          this.$emit("department-updated", data);
        } else if (error) {
          console.error("Erro ao alterar status:", error);
          alert("Erro ao alterar status do departamento");
        }
      } catch (error) {
        console.error("Erro ao alterar status:", error);
        alert("Erro ao alterar status do departamento");
      }
    },
  },
  mounted() {
    this.getDepartments();
  },
};
</script>

<style scoped>
.departments-manager {
  width: 100%;
}
</style>
