# Componentes Reutilizáveis

## 🧩 Componentes Disponíveis

### Botões

#### ButtonNewForm
**Localização**: `frontend/src/components/buttons/ButtonNewForm.vue`

**Uso**: Botão para abrir formulários de criação

```vue
<button-new-form 
  target="opportunity" 
  @open-modal="isCreateModalVisible = true" 
/>
```

**Props**:
- `target`: String - Identificador do tipo de item (opcional)

**Events**:
- `@open-modal`: Emitido quando o botão é clicado

**Observação**: Use kebab-case no template (`button-new-form`)

---

### Inputs de Formulário

#### TextInput
**Localização**: `frontend/src/components/forms/inputs/text/TextInput.vue`

```vue
<TextInput
  label="Nome"
  type="text"
  name="name"
  v-model="form.name"
  placeholder="Digite o nome"
/>
```

**Props**:
- `label`: String - Rótulo do campo
- `type`: String - Tipo do input (text, email, etc)
- `name`: String - Nome do campo
- `v-model`: Vinculação bidirecional
- `placeholder`: String - Texto placeholder

---

#### TextAreaInput
**Localização**: `frontend/src/components/forms/inputs/textarea/TextAreaInput.vue`

```vue
<TextAreaInput
  label="Descrição"
  name="description"
  v-model="form.description"
  placeholder="Digite a descrição"
  :rows="4"
/>
```

**Props**:
- `label`: String - Rótulo do campo
- `name`: String - Nome do campo
- `v-model`: Vinculação bidirecional
- `placeholder`: String - Texto placeholder
- `rows`: Number - Quantidade de linhas visíveis

---

#### DateInput
**Localização**: `frontend/src/components/forms/inputs/date/DateInput.vue`

```vue
<DateInput
  v-model="form.date_start"
  label="Data de Início"
  name="date_start"
  placeholder="Selecione a data"
  :autoFillNow="true"
/>
```

**Props**:
- `label`: String - Rótulo do campo
- `name`: String - Nome do campo
- `v-model`: Vinculação bidirecional
- `placeholder`: String - Texto placeholder
- `autoFillNow`: Boolean - Preenche com data atual automaticamente

**Importante**: Veja [Tratamento de Datas](../backend/date-handling.md) para mais detalhes

---

### Componentes de Seleção (Selects)

#### LeadsSelectInput
**Localização**: `frontend/src/components/forms/selects/LeadsSelectInput.vue`

```vue
<LeadsSelectInput
  ref="leadsSelect"
  label="Contato"
  name="lead_id"
  v-model="form.lead_id"
  fieldsToDisplay="name"
  fieldNull="Nenhum"
/>
```

**Props**:
- `label`: String - Rótulo do campo
- `name`: String - Nome do campo
- `v-model`: Vinculação bidirecional
- `fieldsToDisplay`: String - Campo a ser exibido
- `fieldNull`: String - Texto para opção vazia

**Métodos Públicos**:
- `reload()`: Recarrega a lista de leads do backend

**Uso do reload**:
```javascript
// Após criar um novo lead
this.$refs.leadsSelect?.reload();
```

---

#### CompaniesSelectInput
**Localização**: `frontend/src/components/forms/selects/CompaniesSelectInput.vue`

```vue
<CompaniesSelectInput
  ref="companiesSelect"
  label="Empresa"
  name="company_id"
  v-model="form.company_id"
  :fieldsToDisplay="['business_name', 'legal_name']"
  fieldNull="Nenhuma"
/>
```

**Props**:
- `label`: String - Rótulo do campo
- `name`: String - Nome do campo
- `v-model`: Vinculação bidirecional
- `fieldsToDisplay`: String | Array - Campo(s) a ser(em) exibido(s)
- `fieldNull`: String - Texto para opção vazia

**Métodos Públicos**:
- `reload()`: Recarrega a lista de empresas do backend

---

#### UsersSelectInput
**Localização**: `frontend/src/components/forms/selects/UsersSelectInput.vue`

```vue
<UsersSelectInput
  label="Responsável"
  v-model="form.user_id"
  fieldsToDisplay="name"
  autoSelect="true"
/>
```

**Props**:
- `label`: String - Rótulo do campo
- `v-model`: Vinculação bidirecional
- `fieldsToDisplay`: String - Campo a ser exibido
- `autoSelect`: String - Seleciona o usuário atual automaticamente

---

#### ProjectsSelectInput
**Localização**: `frontend/src/components/forms/selects/ProjectsSelectInput.vue`

```vue
<ProjectsSelectInput
  label="Projeto"
  v-model="form.project_id"
  fieldsToDisplay="name"
  :autoSelect="false"
  fieldNull="Nenhum"
/>
```

**Props**:
- `label`: String - Rótulo do campo
- `v-model`: Vinculação bidirecional
- `fieldsToDisplay`: String - Campo a ser exibido
- `autoSelect`: Boolean - Seleciona automaticamente se houver apenas um
- `fieldNull`: String - Texto para opção vazia

---

### Mensagens

#### ErrorMessage
**Localização**: `frontend/src/components/forms/messages/ErrorMessage.vue`

```vue
<ErrorMessage v-if="formResponse" :formResponse="formResponse" />
```

**Props**:
- `formResponse`: Object - Resposta de erro do backend Laravel

**Formato esperado** (Laravel validation errors):
```javascript
{
  errors: {
    name: ['O campo nome é obrigatório'],
    email: ['O email é inválido']
  }
}
```

---

### Componentes de Busca/Filtro

#### SearchInput
**Localização**: `frontend/src/components/filters/SearchInput.vue`

```vue
<SearchInput 
  v-model="searchTerm" 
  placeholder="Digite para buscar" 
/>
```

**Props**:
- `v-model`: Vinculação bidirecional com termo de busca
- `placeholder`: String - Texto placeholder

---

## 📋 Padrão para Criar Novo Componente

### 1. Localização
Coloque o componente na pasta apropriada:
- Botões → `components/buttons/`
- Inputs → `components/forms/inputs/{tipo}/`
- Selects → `components/forms/selects/`
- Mensagens → `components/forms/messages/`

### 2. Estrutura Básica
```vue
<template>
  <!-- Seu HTML aqui -->
</template>

<script>
export default {
  name: "ComponentName", // PascalCase
  props: {
    // Props aqui
  },
  emits: ["update:modelValue"], // Se usar v-model
  data() {
    return {
      // Estado interno
    }
  },
  methods: {
    // Métodos aqui
  }
}
</script>

<style scoped>
/* Estilos específicos */
</style>
```

### 3. Props Comuns
- `label`: String - Rótulo do campo
- `name`: String - Nome do campo (para forms)
- `placeholder`: String - Texto placeholder
- `modelValue`: Qualquer - Para v-model

### 4. Events Comuns
- `update:modelValue`: Para v-model
- `@click`: Para botões
- `@change`: Para mudanças de valor

---

## ✅ Checklist Antes de Criar Componente Novo

- [ ] Verifique se já existe componente similar
- [ ] Consulte esta documentação
- [ ] Use convenção de nomenclatura (PascalCase)
- [ ] Coloque na pasta apropriada
- [ ] Documente as props e events
- [ ] Adicione exemplo de uso nesta documentação

---

## 🔍 Componentes por Categoria

### 📝 Formulários
- TextInput
- TextAreaInput
- DateInput
- LeadsSelectInput
- CompaniesSelectInput
- UsersSelectInput
- ProjectsSelectInput

### 🔘 Botões
- ButtonNewForm

### 💬 Mensagens
- ErrorMessage
- SuccessMessage

### 🔎 Filtros
- SearchInput

---

**Última atualização**: Fevereiro 2025
