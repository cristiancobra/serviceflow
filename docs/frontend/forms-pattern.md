# Padrões de Formulários

## 📋 Padrão Atual (Fevereiro 2025)

Use o **`LeadCreateForm.vue`** como referência para novos formulários.

## ✅ Estrutura Padrão

### Template
```vue
<template>
  <div>
    <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center p-4" 
         style="background-color: rgba(0, 0, 0, 0.25)">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        
        <!-- Header Sticky -->
        <div class="sticky top-0 bg-gradient-to-r from-blue-50 to-blue-25 border-b border-gray-200 px-8 py-6 flex justify-between items-center">
          <div>
            <h3 class="text-2xl font-bold text-gray-800">Título do Formulário</h3>
            <p class="text-gray-600 text-sm mt-1">Descrição do formulário</p>
          </div>
          <button type="button" @click="closeModal">
            <font-awesome-icon icon="fa-solid fa-xmark" class="text-2xl" />
          </button>
        </div>

        <!-- Body -->
        <div class="px-8 py-6">
          <ErrorMessage v-if="formResponse" :formResponse="formResponse" />
          
          <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Campos do formulário -->
          </form>
        </div>

        <!-- Footer Sticky -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-8 py-4 flex justify-end gap-3">
          <button type="button" @click="closeModal">Cancelar</button>
          <button type="submit" @click="submitForm">Criar</button>
        </div>
      </div>
    </div>
  </div>
</template>
```

### Script
```vue
<script>
import { submitFormCreate } from "@/utils/requests/httpUtils";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";

export default {
  name: "ExampleCreateForm",
  components: {
    ErrorMessage,
  },
  emits: ["new-item-event", "update:modelValue"],
  props: {
    modelValue: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      form: {
        name: null,
        // outros campos...
      },
      formResponse: null,
    };
  },
  methods: {
    submitFormCreate,
    closeModal() {
      this.$emit("update:modelValue", false);
      this.formResponse = null;
      this.clearForm();
    },
    async submitForm() {
      const { data, error } = await this.submitFormCreate("endpoint", this.form);

      if (data) {
        this.$emit("update:modelValue", false);
        this.$emit("new-item-event", data);
        this.clearForm();
        this.formResponse = null;
      }
      if (error) {
        this.formResponse = error.response?.data || { errors: { geral: ['Erro ao criar'] } };
      }
    },
    clearForm() {
      // Limpar todos os campos
      this.form.name = null;
    },
  },
};
</script>
```

## 🎨 Classes Tailwind para Cores

### Gradientes de Header
- **Azul** (Leads/Contatos): `from-blue-50 to-blue-25`
- **Verde** (Empresas): `from-green-50 to-green-25`
- **Roxo** (Oportunidades): `from-purple-50 to-purple-25`
- **Amarelo** (Propostas): `from-yellow-50 to-yellow-25`

### Botões
- **Primário**: `bg-gradient-to-r from-blue-600 to-blue-800`
- **Sucesso**: `bg-gradient-to-r from-green-600 to-green-800`
- **Cancelar**: `bg-white border border-gray-300`

## 📐 Grid Responsivo

```vue
<!-- 1 coluna em mobile, 2 em desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div><!-- Campo 1 --></div>
  <div><!-- Campo 2 --></div>
</div>
```

## ✅ Checklist para Novo Formulário

- [ ] Usa `v-model` para controlar visibilidade do modal
- [ ] Emite `update:modelValue` e `new-item-event`
- [ ] Header sticky com gradiente apropriado
- [ ] Footer sticky com botões Cancelar/Criar
- [ ] Usa `ErrorMessage` para exibir erros do backend
- [ ] Tem método `clearForm()` que limpa todos os campos
- [ ] Método `closeModal()` fecha e limpa o formulário
- [ ] Grid responsivo para campos lado a lado
- [ ] `max-h-[90vh] overflow-y-auto` no modal

## 🔗 Formulários com Subformulários

Se seu formulário precisa abrir outro formulário (ex: criar empresa dentro de criar oportunidade):

```vue
<!-- No template principal -->
<button @click="isActiveFormCompany = true">
  + Adicionar nova empresa
</button>

<!-- Fora do modal principal -->
<company-create-form 
  v-model="isActiveFormCompany"
  @new-company-event="addCompanyCreated" 
/>
```

```vue
// No script
data() {
  return {
    isActiveFormCompany: false,
  }
},
methods: {
  addCompanyCreated(newCompany) {
    this.form.company_id = newCompany.id;
    this.isActiveFormCompany = false;
    // Recarregar select se necessário
    this.$refs.companiesSelect?.reload();
  }
}
```

## 📚 Exemplos no Projeto

- ✅ **LeadCreateForm.vue** - Referência principal
- ✅ **CompanyCreateForm.vue** - Formulário de empresa
- ✅ **OpportunityCreateForm.vue** - Formulário com subformulários
- ❌ **DebitInvoiceCreateForm.vue** - Padrão antigo (não usar)

---

**Importante**: Sempre que criar um formulário novo, siga este padrão para manter a consistência!
