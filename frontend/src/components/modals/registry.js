import TaskDetailModal from "@/components/modals/details/TaskDetailModal.vue";
import LinksModal from "@/components/modals/LinksModal.vue";
import TaskCreateForm from "@/components/forms/TaskCreateForm.vue";
import CompanyCreateForm from "@/components/forms/CompanyCreateForm.vue";
import LeadCreateForm from "@/components/forms/LeadCreateForm.vue";
import OpportunityCreateForm from "@/components/forms/OpportunityCreateForm.vue";
import ProjectCreateForm from "@/components/forms/ProjectCreateForm.vue";
import CostCreateForm from "@/components/forms/CostCreateForm.vue";
import ServiceCostCreateForm from "@/components/forms/ServiceCostCreateForm.vue";
import ProposalCostCreateForm from "@/components/forms/ProposalCostCreateForm.vue";
import DebitInvoiceCreateForm from "@/components/forms/DebitInvoiceCreateForm.vue";
import OperationalCostInvoiceCreateForm from "@/components/forms/OperationalCostInvoiceCreateForm.vue";
import TransactionCreateForm from "@/components/forms/TransactionCreateForm.vue";
import StandaloneDebitInvoiceCreateForm from "@/components/forms/StandaloneDebitInvoiceCreateForm.vue";
import ProposalCreateForm from "@/components/forms/ProposalCreateForm.vue";
import ServiceCreateForm from "@/components/forms/ServiceCreateForm.vue";
import LinkCreateForm from "@/components/forms/LinkCreateForm.vue";

// Registro central dos modais que podem ser abertos via store (openModal mutation).
// Para adicionar um novo modal empilhável, basta importar o componente e listá-lo aqui.
export default {
  TaskDetailModal,
  LinksModal,
  TaskCreateForm,
  CompanyCreateForm,
  LeadCreateForm,
  OpportunityCreateForm,
  ProjectCreateForm,
  CostCreateForm,
  ServiceCostCreateForm,
  ProposalCostCreateForm,
  DebitInvoiceCreateForm,
  OperationalCostInvoiceCreateForm,
  TransactionCreateForm,
  StandaloneDebitInvoiceCreateForm,
  ProposalCreateForm,
  ServiceCreateForm,
  LinkCreateForm,
};
