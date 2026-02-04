<template>
  <div class="bg-red-50 border-2 border-red-500 rounded-lg p-4 mb-6">
    <div class="flex gap-4">
      <div class="flex-shrink-0 pt-0.5">
        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="text-2xl text-red-600" />
      </div>
      <div class="flex-1">
        <h3 class="text-red-800 font-bold text-lg mb-3">Erros na validação</h3>
        <div v-for="(errors, field) in formResponse.errors" :key="field" class="mb-4 last:mb-0">
          <div class="flex items-center gap-2 mb-2">
            <span class="inline-block px-3 py-1 bg-red-200 text-red-800 text-sm font-semibold rounded-full">
              {{ formatFieldName(field) }}
            </span>
          </div>
          <ul class="space-y-1 ml-4">
            <li v-for="(error, index) in errors" :key="index" class="text-red-700 text-sm flex items-start gap-2">
              <span class="text-red-500 font-bold mt-0.5">•</span>
              <span>{{ error }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    formResponse: {
      type: Object,
      required: true,
    },
  },
  methods: {
    formatFieldName(field) {
      const fieldNames = {
        name: 'Nome',
        email: 'Email',
        cel_phone: 'Telefone',
        comments: 'Observações',
        user_id: 'Usuário',
        company_id: 'Empresa',
        linkedin: 'LinkedIn',
        facebook: 'Facebook',
        instagram: 'Instagram',
        other_social_media: 'Redes Sociais',
        contact_date: 'Data de Contato',
        source: 'Origem',
        source_contact_channel: 'Canal de Contato',
        reason_for_initial_contact: 'Motivo do Contato',
      };
      return fieldNames[field] || field.charAt(0).toUpperCase() + field.slice(1).replace(/_/g, ' ');
    },
  },
};
</script>