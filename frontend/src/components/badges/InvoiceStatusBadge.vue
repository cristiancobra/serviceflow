<template>
  <span :class="badgeClasses">
    {{ statusLabel }}
  </span>
</template>

<script>
export default {
  name: 'InvoiceStatusBadge',
  props: {
    status: {
      type: String,
      required: true,
      validator: (value) => ['pending', 'partial', 'paid', 'overdue', 'cancelled'].includes(value)
    }
  },
  computed: {
    statusLabel() {
      const labels = {
        'pending': 'Pendente',
        'partial': 'Parcial',
        'paid': 'Pago',
        'overdue': 'Vencido',
        'cancelled': 'Cancelado'
      };
      return labels[this.status] || 'Desconhecido';
    },
    badgeClasses() {
      const baseClasses = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold';

      const statusClasses = {
        'pending': 'bg-info/10 text-info',
        'partial': 'bg-warning/10 text-warning',
        'paid': 'bg-success/10 text-success',
        'overdue': 'bg-error/10 text-error',
        'cancelled': 'bg-base-200 text-base-content'
      };
      
      return `${baseClasses} ${statusClasses[this.status] || 'bg-base-200 text-base-content'}`;
    }
  }
};
</script>

<style scoped>
span {
  white-space: nowrap;
}
</style>
