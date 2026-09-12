import TaskDetailModal from "@/components/modals/details/TaskDetailModal.vue";
import LinksModal from "@/components/modals/LinksModal.vue";
import TaskCreateForm from "@/components/forms/TaskCreateForm.vue";

// Registro central dos modais que podem ser abertos via store (openModal mutation).
// Para adicionar um novo modal empilhável, basta importar o componente e listá-lo aqui.
export default {
  TaskDetailModal,
  LinksModal,
  TaskCreateForm,
};
