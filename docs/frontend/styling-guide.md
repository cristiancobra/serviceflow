# Guia de Estilos e CSS

O frontend usa **DaisyUI 5 + Tailwind CSS 4**. Este guia é a regra para todo template novo ou alterado. A dívida que ainda não segue a regra está mapeada no [Plano de Reformulação do Layout](./plano-de-reformulacao-do-layout.md).

## Regra: ordem de preferência

1. **Componente Vue do projeto**, quando já existe um para o caso. Reutilize antes de criar.
   - Cabeçalho de página: `PageHeader` (`components/layout/PageHeader.vue`)
   - Lista ou seção vazia: `EmptyState` (`components/layout/EmptyState.vue`)
   - Ativo/inativo: `StatusToggle` (`components/buttons/StatusToggle.vue`)
   - Modais: `ModalCard` (`components/modals/ModalCard.vue`)
   - Card de seção com cabeçalho roxo (título + ícone), ex: seções do modal de oportunidade: `SectionCard` (`components/common/SectionCard.vue`)
   - Botão de fechar: `CloseButton`
   - Status: `SelectStatusButton`
   - Campos: `components/forms/inputs/` (formulários) e `components/fields/` (campos editáveis nas telas de detalhe)
2. **Componente DaisyUI** para elementos genéricos: `btn`, `input`, `select`, `textarea`, `checkbox`, `radio`, `toggle`, `badge`, `card`, `table`, `alert`, `loading`, `dropdown`, `menu`, `join`, `tabs`, `fieldset`.
3. **Utilitários Tailwind** só para layout, espaçamento e ajustes pontuais: `flex`, `grid grid-cols-2`, `gap-4`, `p-4`, `mb-2`, `w-full`, `text-sm`, `font-semibold`.

## Proibido

- **Classes do Bootstrap.** O Bootstrap não está instalado. `row`, `col-*`, `d-flex`, `justify-content-*`, `w-100`, `text-muted`, `form-group`, `form-label`, `form-control`, `modal-header`/`body`/`footer`, `btn-close`, `navbar-*`, `dropdown-item`, `pagination` não fazem nada.
- **Sintaxe do DaisyUI 4**: `form-control`, `input-bordered`, `select-bordered`, `label-text`.
- **Cores fixas**: `bg-white`, `text-gray-700`, `bg-red-100`, `#374151`, `var(--green)`... Elas quebram o tema escuro. Use os tokens (tabela abaixo).
- **Classes CSS novas** (`.btn-view`, `.modal-overlay`...) e regras novas em `src/assets/css/`. Se um padrão se repete e é específico do domínio, crie um componente Vue. `<style scoped>` só para o que o Tailwind não resolve (animações, pseudo-elementos).

## Configuração e temas

- Tudo fica em `frontend/src/assets/css/main.css`: `@plugin "daisyui"` e os temas `service-light` (padrão) e `service-dark`.
- O `frontend/tailwind.config.js` é sobra do Tailwind 3 e **não é carregado**. Não edite.
- O CSS antigo de `src/assets/css/` é importado na camada `legacy`, abaixo de `components` e `utilities`. Por isso qualquer classe DaisyUI ou Tailwind aplicada num elemento sobrescreve esse CSS.
- O tema é aplicado em `<html data-theme="...">` por `utils/theme/themeManager.js`, conforme a preferência da conta (claro, escuro ou automático por horário).

## Cores: sempre tokens do tema

| Uso | Classe Tailwind | Em CSS |
|---|---|---|
| Fundo de card, painel, modal | `bg-base-100` | `var(--color-base-100)` |
| Fundo da página, hover, faixa de destaque | `bg-base-200` | `var(--color-base-200)` |
| Bordas, divisórias | `border-base-300` | `var(--color-base-300)` |
| Texto principal | `text-base-content` | `var(--color-base-content)` |
| Texto secundário | `text-base-content/70` | `color-mix(in oklab, var(--color-base-content) 70%, transparent)` |
| Texto apagado, placeholder | `text-base-content/50` | idem, 50% |
| Cor da marca, foco | `text-primary`, `bg-primary`, `ring-primary` | `var(--color-primary)` |
| Erro, sucesso, aviso, informação | `text-error`, `bg-success`, `border-warning`, `text-info`... | `var(--color-error)` etc. |
| Fundo claro de status (badge, alerta) | `bg-error/10`, `bg-success/10`... | `color-mix(in oklab, var(--color-error) 15%, var(--color-base-100))` |

- `text-white` é aceitável sobre fundos coloridos (`bg-primary`, `bg-error`...). Ainda melhor: `text-primary-content`, `text-error-content` etc.
- Os tokens `success`, `warning`, `info` e `error` do tema claro foram escurecidos para dar contraste de pelo menos 4,5:1 como texto. Não clareie sem refazer a conta.
- `text-primary` no tema escuro dá 3,15:1. Isso foi avaliado e aceito; ver a decisão no plano. Não crie correções locais novas para isso.

## Exemplos

### Botões

```vue
<button class="btn btn-primary">Salvar</button>          <!-- ação principal -->
<button class="btn btn-ghost">Cancelar</button>          <!-- cancelar, voltar -->
<button class="btn btn-secondary">Fechar fatura</button> <!-- ação secundária -->
<button class="btn btn-error">Excluir</button>           <!-- destrutiva -->

<!-- Ação de ícone (listas) -->
<button class="btn btn-sm btn-square btn-ghost" title="Editar">
  <font-awesome-icon icon="fa-solid fa-edit" />
</button>
```

O `.btn.btn-primary` tem um ajuste global em `src/assets/css/style.css` (borda branca e leve aumento no hover).

### Campos de formulário

```vue
<div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2">
  <div class="fieldset md:col-span-2">
    <label for="name" class="fieldset-legend justify-start">
      Nome <span class="text-error">*</span>
    </label>
    <input id="name" v-model="form.name" type="text" class="input w-full" required />
    <span v-if="errors.name" class="text-error text-xs">{{ errors.name[0] }}</span>
  </div>

  <div class="fieldset">
    <label for="category" class="fieldset-legend">Categoria</label>
    <select id="category" v-model="form.category" class="select w-full">...</select>
  </div>

  <label class="flex items-center gap-2 cursor-pointer select-none">
    <input type="checkbox" v-model="form.is_active" class="checkbox checkbox-primary checkbox-sm" />
    <span class="text-sm font-medium">Ativo</span>
  </label>
</div>

<div class="flex justify-end gap-3 pt-4 border-t border-base-300">
  <button type="button" class="btn btn-ghost" @click="cancel">Cancelar</button>
  <button type="submit" class="btn btn-primary">Salvar</button>
</div>
```

Referência pronta: `components/forms/BankAccountForm.vue`.

### Modais

Use sempre o `ModalCard`.

**Modais abertos via store** (detalhe, criação): registre o componente em `components/modals/registry.js`. O `App.vue` já fornece o overlay e empilha os modais lado a lado.

**Modal local de um componente:**

```vue
<div
  v-if="showModal"
  class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm"
  @click.self="closeModal"
>
  <ModalCard title="Confirmar Exclusão" icon="fa-solid fa-trash" size="sm" @close="closeModal">
    <p>Tem certeza?</p>

    <template #footer>
      <button class="btn btn-ghost" @click="closeModal">Cancelar</button>
      <button class="btn btn-error" @click="confirm">Excluir</button>
    </template>
  </ModalCard>
</div>
```

`size`: `sm` (max-w-md), `md` (max-w-2xl), `lg` (max-w-4xl) ou `xl` (max-w-6xl). Referência: `components/lists/CreditCardsList.vue`.

**Modais de detalhe** (tarefa, contato, oportunidade, fatura...): o tipo vai no `title` e o **nome do elemento no subtítulo, editável no próprio cabeçalho**. Assim ele continua visível quando o conteúdo rola. **Não repita o nome no conteúdo.**

```vue
<ModalCard
  title="Tarefa"
  :subtitle="task?.name || ''"
  :subtitle-editable="!!task"
  subtitle-placeholder="Nome da tarefa"
  icon="fa-solid fa-tasks"
  @close="closeModal"
  @save-subtitle="updateTask('name', $event)"
>
```

O campo editável do cabeçalho é o `HeaderEditableField` (`components/fields/text/`), a versão branca sobre roxo do `TextEditableField`. Referência: `components/modals/details/TaskDetailModal.vue`.

### Layout responsivo

Breakpoints do Tailwind: `sm` 640px, `md` 768px, `lg` 1024px, `xl` 1280px, `2xl` 1536px.

```vue
<!-- 1 coluna no celular, 2 no tablet, 3 no desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">...</div>

<!-- Só no celular / só a partir do tablet -->
<div class="block md:hidden">...</div>
<div class="hidden md:block">...</div>

<!-- Ajuste só abaixo de md -->
<div class="p-5 max-md:p-4">...</div>
```

### Status, alertas e carregamento

```vue
<span class="badge badge-success">Pago</span>
<span class="badge badge-soft badge-warning">Parcial</span>

<div role="alert" class="alert alert-error alert-soft">
  <font-awesome-icon icon="fa-solid fa-circle-exclamation" />
  <span>{{ errorMessage }}</span>
</div>

<span class="loading loading-spinner loading-md"></span>
```

## Ícones

Font Awesome 6, com registro explícito em `src/main.js` (`library.add(...)`). Um ícone que não está registrado não aparece. Confira a lista antes de usar um ícone novo; se ele não estiver lá, adicione o import e o `library.add`.

## Referências

- [DaisyUI 5: componentes](https://daisyui.com/components/)
- [Tailwind CSS 4](https://tailwindcss.com/docs)
- Temas: `frontend/src/assets/css/main.css`
