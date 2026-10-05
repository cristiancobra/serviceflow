# Plano de reformulação do layout

Objetivo: deixar o frontend consistente e sustentável usando **DaisyUI 5 + Tailwind 4**, com o tema escuro (`service-dark`) funcionando em todas as telas e com componentes compartilhados que a IA consiga reutilizar em vez de copiar.

A regra que orienta todo código novo está no [Guia de Estilos](./styling-guide.md). Este documento trata da **dívida existente**: o que falta migrar, em que ordem e como.

## Status

| Etapa | Situação |
|---|---|
| 0. Remover classes do Bootstrap | ✅ Concluída |
| 1. Cores fixas → tokens do tema | ✅ Concluída (com pendências listadas na etapa) |
| 2. Componentes de estrutura de página | ✅ Concluída |
| 3. Botões padronizados | 🔄 Padrão decidido; execução pendente |
| 4. Listas como tabela | ⏳ Pendente |
| 5. Feedback ao usuário (confirmação, toast, loading) | ⏳ Pendente |
| 6. Atualizar a documentação de `docs/frontend/` | 🔄 `styling-guide.md` reescrito; faltam `forms-pattern.md` e `detail-modal-template.md` |
| 7. Enxugar o CSS restante | ⏳ Contínuo (consequência das etapas anteriores) |

## Diagnóstico (setembro de 2026)

Números levantados no `frontend/src` depois da etapa 0:

- **Cores fixas**: ~900 classes Tailwind com cor fixa (`text-gray-*`, `bg-white`, `bg-red-*`...), 335 valores hexadecimais em CSS e 151 usos de variáveis antigas (`var(--primary)`, `var(--green)`...), contra só 16 usos dos tokens do DaisyUI (`var(--color-*)`).
- **Estrutura de página duplicada**: o cabeçalho de página (`page-header` + `page-title` + `page-icon` + `page-action`) aparece em 30 telas, com o CSS copiado em pelo menos 7 arquivos, além do `lists.css`. O mesmo vale para `empty-state`, `list-line` e `status-toggle`.
- **Botões**: 66 botões usam `btn` do DaisyUI; o resto usa variações próprias (`btn-action`, `btn-view`, `btn-edit`, `btn-delete`, `btn-create`, `btn-back`, `button`, `myButton`, `button-circular`).
- **Listas**: 9 listas feitas de `div` flex com larguras fixas (`w-1/10`, `w-3/10`), que espremem no celular.
- **Feedback**: 28 `alert()`/`confirm()` nativos do navegador; carregamento indicado por texto "Carregando..." em 13 lugares, nenhum com `loading` do DaisyUI.
- **CSS**: ~1.700 linhas em `src/assets/css/` (camada `legacy`) e ~4.000 linhas de `<style scoped>` em 99 arquivos.
- **Documentação**: `forms-pattern.md`, `styling-guide.md` e `detail-modal-template.md` ensinam `bg-white`, `text-gray-700` etc. Isso realimenta o problema: quem segue a documentação gera cor fixa.

## Etapa 1: Cores fixas → tokens do tema

**Por quê**: cada `bg-white` ou `text-gray-700` fica errado no tema escuro. Hoje três telas têm correção manual (`[data-theme="service-dark"] ...`) só por causa disso.

### Fase 1a: classes Tailwind (✅ concluída)

Feita por script: 1.362 trocas em 82 arquivos (`.vue` e `.js`). Ficaram de fora, de propósito, os casos listados em "O que não muda" abaixo.

**Decisão de contraste**: no tema claro, os tokens `success`, `warning`, `info` e `error` estavam claros demais para serem usados como texto (contraste de 1,9 a 3,3 sobre `base-100`, abaixo do mínimo WCAG de 4,5). Eles foram escurecidos em `main.css`, mantendo matiz e croma e baixando só a luminosidade, até chegar a cerca de 4,5:1. Isso também tornou legível o texto branco dos botões `btn-success`, `btn-warning` etc. O tema escuro já passava e não mudou. Não clarear esses tokens de volta sem refazer a conta.


Tabela de conversão. Vale também com prefixos (`hover:`, `focus:`, `group-hover:`...).

**Neutros** (o fundo da página é `base-200`; cards e painéis são `base-100`):

| Antes | Depois |
|---|---|
| `bg-white` | `bg-base-100` |
| `bg-gray-50`, `bg-gray-100` | `bg-base-200` |
| `bg-gray-200`, `bg-gray-300` | `bg-base-300` |
| `text-black`, `text-gray-900`, `text-gray-800` | `text-base-content` |
| `text-gray-700` | `text-base-content/80` |
| `text-gray-600` | `text-base-content/70` |
| `text-gray-500` | `text-base-content/60` |
| `text-gray-400` | `text-base-content/50` |
| `text-gray-300` | `text-base-content/30` |
| `border-gray-100` | `border-base-200` |
| `border-gray-200`, `border-gray-300` | `border-base-300` |
| `border-gray-400`, `border-gray-500` | `border-base-content/20` |
| `from-gray-*` / `to-gray-*` (50–200) | `from-base-200` / `to-base-300` (mesma lógica dos fundos) |

**Semânticas** (cor com significado de estado):

| Família | Token | Fundos claros (50–200) | Fundos fortes (400+) | Texto | Bordas claras (100–300) | Bordas fortes (400+) |
|---|---|---|---|---|---|---|
| `red`, `rose` | `error` | `bg-error/10` | `bg-error` | `text-error` | `border-error/30` | `border-error` |
| `green`, `emerald` | `success` | `bg-success/10` | `bg-success` | `text-success` | `border-success/30` | `border-success` |
| `yellow`, `amber`, `orange` | `warning` | `bg-warning/10` | `bg-warning` | `text-warning` | `border-warning/30` | `border-warning` |
| `blue`, `sky` | `info` | `bg-info/10` | `bg-info` | `text-info` | `border-info/30` | `border-info` |

- **Anéis de foco** (`ring-blue-*`, `focus:border-blue-*`) viram `ring-primary` / `focus:border-primary`: é a cor de destaque da marca, não informação.
- **Anéis semânticos** (`ring-red-*` etc.) viram `ring-error` etc.
- **O que não muda**:
  - `text-white` e `bg-white/10`, `bg-white/80`: são usados sobre fundos coloridos e funcionam nos dois temas.
  - `bg-black/30`, `bg-black/50`: sombreamento de overlay.
  - Paletas categóricas (`purple`, `indigo`, `teal`, `cyan`, `pink`, `violet`, `lime`): diferenciam categorias (tipos, status), não estados. Ficam para uma revisão manual; se virarem tokens, o caminho é criar cores próprias no tema em `main.css`.
  - Cinzas escuros de fundo (`bg-gray-400` a `bg-gray-900`): geralmente são botões ou badges com significado específico. Revisão manual, caso a caso.

### Fase 1b: CSS (`<style scoped>` e `assets/css/`) (✅ concluída)

Feita por script, levando em conta a propriedade CSS: 344 cores e 181 variáveis antigas trocadas.

- **Variáveis antigas**: viraram tokens. `--primary`/`--purple` → `--color-primary`, `--green` → `--color-success`, `--red` → `--color-error`, `--blue` → `--color-info`, `--orange` → `--color-warning`, `--gray` → `base-content` a 50%, e os `--*-light` viraram `color-mix(in oklab, var(--color-X) 15%, var(--color-base-100))`. O `variables.css` ficou só com `--box-shadow-value`.
- **Hexadecimais neutros**: cinzas escuros viraram `base-content` com `color-mix` (80%, 60%, 50%); cinzas claros viraram `base-200`/`base-300`; `white` em `background` virou `base-100`.
- **Hexadecimais semânticos**: vermelhos, verdes, azuis e amarelos viraram `error`/`success`/`info`/`warning`. As versões claras usadas como fundo viraram tint de 15% sobre `base-100`. `#007bff` (azul do Bootstrap) virou `primary`, porque era usado como fallback de `var(--primary)`.
- **Cores por nome**: `red` → `error`, `lightgray` → `base-300`, `gray` em borda → `base-content` a 50%.
- **Correções manuais de tema escuro**: removida a do `modal.css` (ficou redundante). A do `variables.css` foi embora junto com as variáveis.
- **`rgba(var(--primary-rgb), …)`**: essa variável nunca foi definida, então as 4 declarações eram inválidas e ignoradas pelo navegador. Os anéis de foco viraram `color-mix` da primária. A do fundo da navbar foi **removida**: convertê-la deixaria a navbar rosa 80%, sendo que hoje ela aparece transparente com blur.

**Mantido de propósito**: `white` como cor de texto e borda (usado sobre fundos coloridos), sombras `rgba(0, 0, 0, …)`, cinzas de fundo de botões com texto branco (`CancelButton`, `gray` em botões desabilitados) e as cores categóricas (`#7c3aed`, `#48d1cc`...).

### Pendências da etapa 1

- ~~**Primária como texto no tema escuro**~~ **Decidido (outubro de 2026): manter como está.** `#B1388D` sobre o `base-100` escuro dá 3,15:1, abaixo dos 4,5:1 do WCAG para texto. Mas, avaliado na tela (navbar, modal de fatura, tela de empresa), ficou aceitável. As alternativas tinham custo: clarear a primária no `service-dark` obrigaria a usar texto escuro nos `btn-primary`, e um token só para texto fugiria do padrão DaisyUI. A correção local do `MoneyEditableField` continua.
- **Rosa `#ff3eb5`** dos botões "novo" (`.new` em `style.css`, `TasksIndex`, `ProjectsIndex`): é um acento próprio, parecido mas diferente da primária. Resolver na etapa 3 (botões).
- **Cinzas escuros de fundo** (`bg-gray-400` a `900`, 26 usos, e `#6b7280`/`#4b5563` no `CancelButton`): revisar na etapa 3.

### Como verificar

- `npm run build` sem erro.
- Abrir as telas principais nos dois temas (light e dark): listas, telas de detalhe, modais de detalhe e formulários.
- Buscar resíduos: `grep -rE '(text|bg|border)-(gray|red|green|blue|yellow|orange)-[0-9]' frontend/src`.

## Etapa 2: Componentes de estrutura de página (✅ concluída)

- **`PageHeader`** (`components/layout/PageHeader.vue`): props `title`, `icon`, `icon-class` e `show-back` (emite `@back`), slots `title` (título com conteúdo próprio, ex: campo editável) e `actions`. Aplicado nas 28 telas que tinham `page-header`. As regras globais `.page-header`, `.page-title`, `.page-icon` e `.page-action` foram apagadas do `show.css`, assim como os estilos locais `btn-back`/`page-action` e o CSS morto de `LeadsList`/`CompaniesList`. A seta de voltar agora é branca, porque antes era cinza sobre a faixa roxa.
- **`EmptyState`** (`components/layout/EmptyState.vue`): props `text`, `icon` e `description`, com slot para uma ação. Aplicado nas 9 ocorrências de `.empty-state`. As versões com título (`LeadsList`, `CompaniesList`) ficaram com ícone e título um pouco menores, iguais às demais.
- **`StatusToggle`** (`components/buttons/StatusToggle.vue`): badge `badge-soft` de ativo/inativo. Clicável nas listas (emite `@toggle`) e só indicador com `readonly` nas telas de detalhe. Aplicado nas listas e telas de cartões, contas bancárias e despesas recorrentes.

- **Todos os modais usam `ModalCard`**: o `TaskDetailModal` (que tinha cabeçalho azulado próprio) e a confirmação do `DeleteIconButton` foram migrados, e o overlay do `CreditInvoiceCreateForm` foi padronizado. Assim, todo modal tem o mesmo cabeçalho: faixa roxa, ícone, texto branco e X à direita. O `modal.css` (classes `.myModal*`, sem uso) foi apagado.
- **Nome do elemento no cabeçalho dos modais de detalhe**: tarefa, contato, oportunidade e fatura mostram o nome editável no subtítulo do `ModalCard` (`subtitle-editable`, via `HeaderEditableField`), e o nome editável repetido no conteúdo foi removido.

**Não incluídos**: estados vazios escritos direto com Tailwind (sem a classe `.empty-state`), como os de `TransactionsList` e das seções de faturas. Migrar para `EmptyState` quando o arquivo for alterado.

## Etapa 3: Botões

**Padrão decidido** (outubro de 2026), detalhado no [Guia de Estilos](./styling-guide.md#botões): o papel do botão define a classe (`btn-primary` principal, `btn-ghost` cancelar, `btn-secondary` secundária, `btn-error` destrutiva, `btn-sm btn-square btn-ghost` ícone em lista). Decisões:

- **Formato arredondado**: o tema passa de pílula (`--radius-field: 2rem`) para `0.5rem`, igual aos botões feitos à mão de hoje. Vale também para inputs e selects.
- **No cabeçalho roxo**: botão branco com texto roxo (`btn bg-base-100 text-primary`). Com isso, o ajuste global do `.btn.btn-primary` em `style.css` (borda branca e crescimento no hover) pode ser apagado.
- **Cancelar**: só texto (`btn-ghost`).

**Levantamento** (antes da migração): 72 botões montados com Tailwind (`px-6 py-2 rounded-lg` com `bg-primary`, `bg-base-100` com borda ou `bg-error`), 44 com `btn` do DaisyUI, ~25 com classes próprias e ~75 só de ícone ou texto.

**Execução**:
1. Tema: `--radius-field` para `0.5rem` nos dois temas; apagar o ajuste global do `.btn.btn-primary`.
2. Botões no `PageHeader`: trocar para o estilo branco.
3. Botões montados com Tailwind: converter pelo papel (fundo `primary` → `btn-primary`; `base-100` com borda → `btn-ghost`; `error` → `btn-error`; `px-2/px-3 py-1` → `btn-sm`), mantendo só as classes de layout (`w-full`, `flex-1`, `ml-auto`...).
4. Classes próprias (`btn-action`, `btn-create`, `btn-save`, `btn-cancel`, `button*`, `myButton`...): converter e apagar o CSS delas (`style.css`, `login.css` e `<style scoped>`).
5. Componente `RowActions` (ver/editar/excluir) para as listas.
6. Componentes: padronizar o visual interno de `JourneyToggleButton`, `AddJourneyButton` e `SelectStatusButton`; substituir `SaveButton`, `CancelButton` e `ButtonNewForm` por `btn`; apagar `CloseButton` e `LogoutButton`, que estão sem uso.
7. Botões só de ícone ou texto (~75): revisar caso a caso. Muitos são links ou ícones dentro de campos e devem continuar assim.

## Etapa 4: Listas como tabela

Trocar as 9 listas `list-header` + `list-line` por:

```html
<div class="overflow-x-auto">
  <table class="table table-zebra">
    <thead>...</thead>
    <tbody>...</tbody>
  </table>
</div>
```

No celular a tabela rola na horizontal em vez de espremer as colunas. Começar pelas listas financeiras (cartões, contas bancárias, despesas recorrentes), que têm estrutura idêntica.

## Etapa 5: Feedback ao usuário

- **`ConfirmModal`** baseado no `ModalCard` (os modais de exclusão das listas financeiras já são quase isso), aberto via store como os outros modais do `registry.js`, retornando uma Promise. Substitui os `confirm()` nativos.
- **Toast** global (`toast` + `alert` do DaisyUI) para sucesso e erro, substituindo os `alert()` nativos e as mensagens espalhadas (`AddMessage`, `SuccessMessage`, `ErrorMessage`).
- **Carregamento**: `loading loading-spinner` ou skeleton (`skeleton`) no lugar do texto "Carregando...".

## Etapa 6: Documentação

O `styling-guide.md` já foi reescrito e é a referência da regra. Falta reescrever os exemplos de `forms-pattern.md` e `detail-modal-template.md` usando tokens do tema, `ModalCard` e os componentes criados nas etapas 2 a 5. Enquanto isso não for feito, a documentação contradiz o CLAUDE.md.

## Etapa 7: CSS restante

Não atacar diretamente. As etapas 1 a 4 removem a maior parte do CSS legado e do `<style scoped>`. No fim, revisar o que sobrou em `src/assets/css/` e apagar os arquivos vazios ou sem uso. Apagar também o `frontend/tailwind.config.js`: é sobra do Tailwind 3, não é carregado pelo Tailwind 4 e define um tema (`mytheme`) que não vale mais, o que confunde quem o encontra.
