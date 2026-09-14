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
        'pending': 'bg-blue-100 text-blue-800',
        'partial': 'bg-yellow-100 text-yellow-800',
        'paid': 'bg-green-100 text-green-800',
        'overdue': 'bg-red-100 text-red-800',
        'cancelled': 'bg-gray-100 text-gray-800'
      };
      
      return `${baseClasses} ${statusClasses[this.status] || 'bg-gray-100 text-gray-800'}`;
    }
  }
};
</script>

<style scoped>
span {
  white-space: nowrap;
}
</style>
