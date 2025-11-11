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
      validator: (value) => ['unpaid', 'partial', 'paid', 'overdue'].includes(value)
    }
  },
  computed: {
    statusLabel() {
      const labels = {
        'unpaid': 'Pendente',
        'partial': 'Parcial',
        'paid': 'Pago',
        'overdue': 'Vencido'
      };
      return labels[this.status] || 'Desconhecido';
    },
    badgeClasses() {
      const baseClasses = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold';
      
      const statusClasses = {
        'unpaid': 'bg-blue-100 text-blue-800',
        'partial': 'bg-yellow-100 text-yellow-800',
        'paid': 'bg-green-100 text-green-800',
        'overdue': 'bg-red-100 text-red-800'
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
