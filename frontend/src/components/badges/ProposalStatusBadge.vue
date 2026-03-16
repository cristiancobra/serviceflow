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
        'accepted': 'Aprovada',
        'rejected': 'Rejeitada',
        'canceled': 'Cancelada',
        'paid': 'Paga'
      };
      return labels[this.currentStatus] || 'Desconhecido';
    },
    badgeClasses() {
      const baseClasses = 'inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold w-full';
      
      const statusClasses = {
        'draft': 'bg-gray-100 text-gray-800 border-2 border-gray-400',
        'submitted': 'bg-purple-100 text-purple-800 border-2 border-purple-400',
        'accepted': 'bg-green-100 text-green-800 border-2 border-green-400',
        'rejected': 'bg-red-100 text-red-800 border-2 border-red-400',
        'canceled': 'bg-orange-100 text-orange-800 border-2 border-orange-400',
        'paid': 'bg-emerald-100 text-emerald-800 border-2 border-emerald-500'
      };
      
      return `${baseClasses} ${statusClasses[this.currentStatus] || 'bg-gray-100 text-gray-800'}`;
    }
  }
};
</script>

<style scoped>
span {
  white-space: nowrap;
}
</style>
