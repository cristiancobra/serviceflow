# Padrões de Formulários

## 📋 Padrão Atual (Outubro 2026)

Use o **`LeadCreateForm.vue`** como referência para novos formulários.

## ✅ Estrutura Padrão

### Template
O modal é sempre o `ModalCard` (cabeçalho roxo com ícone, título e X). Os botões ficam no slot `#footer` e seguem o [padrão de botões](./styling-guide.md#botões).

```vue
<template>
  <ModalCard
    title="Novo Contato"
    subtitle="Adicione um novo contato ao sistema"
    icon="fa-solid fa-user-plus"
    @close="closeModal"
  >
    <ErrorMessage v-if="formResponse" :formResponse="formResponse" />

    <form id="exampleCreateForm" @submit.prevent="submitForm" class="space-y-6">
      <!-- Campos do formulário -->
    </form>

    <template #footer>
      <button type="button" class="btn btn-ghost" @click="closeModal">Cancelar</button>
      <button type="submit" form="exampleCreateForm" class="btn btn-primary">
        <font-awesome-icon icon="fa-solid fa-plus" /> Criar
      </button>
    </template>
  </ModalCard>
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

## 🎨 Cores e botões

Cabeçalho e cores vêm do `ModalCard` e do tema; não use gradientes nem cores fixas (`bg-white`, `from-blue-600`...). Botões: `btn btn-primary` para criar/salvar e `btn btn-ghost` para cancelar (ver [Guia de Estilos](./styling-guide.md#botões)).

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
- [ ] Usa `ModalCard` (título, ícone e `@close`)
- [ ] Botões no slot `#footer`: `btn btn-ghost` (Cancelar) e `btn btn-primary` (Criar)
- [ ] Usa `ErrorMessage` para exibir erros do backend
- [ ] Tem método `clearForm()` que limpa todos os campos
- [ ] Método `closeModal()` fecha e limpa o formulário
- [ ] Grid responsivo para campos lado a lado

## 🔗 Formulários com Subformulários

Se seu formulário precisa abrir outro formulário (ex: criar empresa dentro de criar oportunidade):

```vue
<!-- No template principal -->
<button type="button" class="btn btn-ghost btn-sm" @click="isActiveFormCompany = true">
  <font-awesome-icon icon="fa-solid fa-plus" /> Adicionar nova empresa
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
