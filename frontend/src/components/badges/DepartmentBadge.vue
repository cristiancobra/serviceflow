<template>
  <div class="department-badge-container">
    <!-- Badge de visualização -->
    <span
      v-if="!isEditing"
      @click="startEdit"
      :class="badgeClasses"
      :style="badgeStyle"
      class="cursor-pointer hover:opacity-80 transition-opacity"
      :title="department ? `Departamento: ${department.name}` : 'Clique para definir departamento'"
    >
      <font-awesome-icon v-if="department?.icon" :icon="`fa-solid ${department.icon}`" class="mr-1" />
      {{ badgeLabel }}
    </span>

    <!-- Select de edição -->
    <div v-else class="inline-edit-department">
      <departments-select-input
        v-model="localDepartmentId"
        name="department_id"
        fieldsToDisplay="name"
        fieldNull="Sem departamento"
        @update:modelValue="saveDepartment"
      />
      <button @click="cancelEdit" class="btn-cancel-edit ms-2" title="Cancelar">
        <font-awesome-icon icon="fa-solid fa-times" />
      </button>
    </div>
  </div>
</template>

<script>
import { submitFormUpdate } from "@/utils/requests/httpUtils";
import DepartmentsSelectInput from "@/components/forms/selects/DepartmentsSelectInput.vue";

export default {
  name: "DepartmentBadge",
  components: {
    DepartmentsSelectInput,
  },
  props: {
    task: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      isEditing: false,
      localDepartmentId: null,
      department: null,
    };
  },
  computed: {
    badgeLabel() {
      return this.department ? this.department.name : 'Sem departamento';
    },
    badgeClasses() {
      const baseClasses = 'inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold border';
      
      if (!this.department) {
        return `${baseClasses} bg-gray-100 text-gray-500 border-gray-300`;
      }
      
      return baseClasses;
    },
    badgeStyle() {
      if (!this.department || !this.department.color) {
        return {};
      }
      
      // Converter cor hex para RGB para criar versão transparente do fundo
      const hex = this.department.color.replace('#', '');
      const r = parseInt(hex.substring(0, 2), 16);
      const g = parseInt(hex.substring(2, 4), 16);
      const b = parseInt(hex.substring(4, 6), 16);
      
      return {
        backgroundColor: `rgba(${r}, ${g}, ${b}, 0.1)`,
        color: this.department.color,
        borderColor: this.department.color,
        borderWidth: '1.5px',
      };
    },
  },
  methods: {
    startEdit() {
      this.localDepartmentId = this.task.department_id;
      this.isEditing = true;
    },
    cancelEdit() {
      this.isEditing = false;
      this.localDepartmentId = this.task.department_id;
    },
    async saveDepartment(departmentId) {
      try {
        const { data, error } = await submitFormUpdate("tasks", this.task.id, {
          department_id: departmentId,
        });

        if (data) {
          this.task.department_id = departmentId;
          this.department = data.department || null;
          this.isEditing = false;
          this.$emit('department-updated', data);
        } else if (error) {
          console.error("Erro ao atualizar departamento:", error);
          alert("Erro ao atualizar departamento");
          this.cancelEdit();
        }
      } catch (error) {
        console.error("Erro ao atualizar departamento:", error);
        alert("Erro ao atualizar departamento");
        this.cancelEdit();
      }
    },
  },
  watch: {
    'task.department': {
      immediate: true,
      handler(newDepartment) {
        this.department = newDepartment;
      },
    },
  },
};
</script>

<style scoped>
.department-badge-container {
  display: inline-block;
  min-width: fit-content;
}

.inline-edit-department {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-cancel-edit {
  padding: 0.25rem 0.5rem;
  background-color: #ef4444;
  color: white;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  font-size: 0.875rem;
  transition: background-color 0.2s;
}

.btn-cancel-edit:hover {
  background-color: #dc2626;
}
</style>
