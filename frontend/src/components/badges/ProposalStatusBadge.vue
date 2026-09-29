<template>
  <span :class="badgeClasses">
    {{ statusLabel }}
  </span>
</template>

<script>
export default {
  name: 'ProposalStatusBadge',
  props: {
    proposal: {
      type: Object,
      required: true,
    }
  },
  computed: {
    // Determina o status baseado nas colunas datetime (prioridade)
    currentStatus() {
      if (this.proposal.paid_at) return 'paid';
      if (this.proposal.canceled_at) return 'canceled';
      if (this.proposal.rejected_at) return 'rejected';
      if (this.proposal.accepted_at) return 'accepted';
      if (this.proposal.submitted_at) return 'submitted';
      if (this.proposal.draft_at) return 'draft';
      return 'draft'; // padrão
    },
    statusLabel() {
      const labels = {
        'draft': 'Rascunho',
        'submitted': 'Enviada',
        'accepted': 'Aceita',
        'rejected': 'Rejeitada',
        'canceled': 'Cancelada',
        'paid': 'Paga'
      };
      return labels[this.currentStatus] || 'Desconhecido';
    },
    badgeClasses() {
      const baseClasses = 'inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold w-full';
      
      const statusClasses = {
        'draft': 'bg-base-200 text-base-content border-2 border-base-content/20',
        'submitted': 'bg-purple-100 text-purple-800 border-2 border-purple-400',
        'accepted': 'bg-success/10 text-success border-2 border-success',
        'rejected': 'bg-error/10 text-error border-2 border-error',
        'canceled': 'bg-warning/10 text-warning border-2 border-warning',
        'paid': 'bg-info/10 text-info border-2 border-info'
      };
      
      return `${baseClasses} ${statusClasses[this.currentStatus] || 'bg-base-200 text-base-content'}`;
    }
  }
};
</script>

<style scoped>
span {
  white-space: nowrap;
}
</style>
