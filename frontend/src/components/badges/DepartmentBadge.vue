<template>
  <div class="inline-block min-w-fit">
    <!-- Badge de visualização (apenas ícone) -->
    <span
      v-if="!isEditing"
      @click="startEdit"
      :class="badgeClasses"
      :style="badgeStyle"
      class="cursor-pointer hover:opacity-80 transition-opacity"
      :title="department ? `Departamento: ${department.name}` : 'Clique para definir departamento'"
    >
      <font-awesome-icon 
        v-if="department?.icon" 
        :icon="`fa-solid ${department.icon}`" 
        class="text-base"
      />
      <font-awesome-icon 
        v-else
        icon="fa-solid fa-folder"
        class="text-base"
      />
    </span>

    <!-- Select de edição -->
    <div v-else class="inline-flex items-center gap-2">
      <departments-select-input
        v-model="localDepartmentId"
        name="department_id"
        fieldsToDisplay="name"
        fieldNull="Sem departamento"
        @update:modelValue="saveDepartment"
      />
      <button @click="cancelEdit" class="px-2 py-1 bg-red-500 text-white border-0 rounded cursor-pointer text-sm transition-colors hover:bg-red-600" title="Cancelar">
        <font-awesome-icon icon="fa-solid fa-times" />
      </button>
    </div>
  </div>
</template>

<script>

import axios from "axios";
import { submitFormUpdate } from "@/utils/requests/httpUtils";
import DepartmentsSelectInput from "@/components/forms/selects/DepartmentsSelectInput.vue";
import { BACKEND_URL, TASK_URL_PARAMETER } from "@/config/apiConfig";

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
      const baseClasses = 'inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-semibold border';
      
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
          // Buscar a tarefa completa usando axios e configs já importados
          const response = await axios.get(`${BACKEND_URL}${TASK_URL_PARAMETER}${this.task.id}`);
          const updatedTask = response.data.data || {};
          this.task.department_id = departmentId;
          this.department = updatedTask.department || null;
          this.isEditing = false;
          this.$emit('department-updated', updatedTask);
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
/* Estilos customizados removidos - usando Tailwind */
</style>
