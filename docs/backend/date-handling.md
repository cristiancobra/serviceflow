# Tratamento de Datas

## 📅 Como Funciona

O projeto usa um serviço centralizado para converter datas entre JavaScript (frontend) e MySQL (backend).

## 🔄 Fluxo de Conversão

```
Frontend (JavaScript Date) 
    ↓
  Backend (Laravel Request)
    ↓
DateTimeConversionService
    ↓
  MySQL (datetime format)
```

## ⚙️ Service: DateTimeConversionService

### Localização
`backend/app/Services/DateTimeConversionService.php`

### Método Principal
```php
public static function convertJavascriptDate($javascriptDate)
{
    // Converte data do JavaScript para formato MySQL
    // Entrada: "2025-02-17T14:30:00.000Z" ou "2025-02-17"
    // Saída: "2025-02-17 14:30:00"
}
```

## 📝 Como Usar no Backend

### Colunas `datetime` x `date`

Antes de converter, veja o tipo da coluna no banco. Os dois casos são tratados de forma diferente:

| Coluna | Exemplos | Ao salvar (Request) | Ao exibir (Resource) |
|---|---|---|---|
| `datetime` (data **e hora**) | `tasks.date_due`, `tasks.date_start` | `convertJavascriptDate()`: guarda em UTC | `convertFromUtc($valor, $timezone)` |
| `date` (só **data**) | `opportunities.date_start/date_due/date_conclusion/date_canceled` | `toLocalDate($valor, $timezone)`: guarda o dia no fuso do usuário | valor puro (`Y-m-d`), **sem** conversão |

Por que a diferença: uma coluna `date` não tem hora. Se ela passar por `convertFromUtc`, `2026-07-17` é lida como meia-noite UTC e aparece como **16/07 21:00** em São Paulo. E se o dia for tirado do horário em UTC, escolher 18/07 às 23:30 grava **19/07**.

### Em FormRequests

Sempre use o `prepareForValidation()` para converter datas antes da validação.

**Coluna `datetime`** (ex: `TaskRequest.php`):

```php
protected function prepareForValidation()
{
    if ($this->filled('date_due')) {
        $this->merge([
            'date_due' => \App\Services\DateTimeConversionService::convertJavascriptDate(
                $this->input('date_due')
            ),
        ]);
    }
}
```

**Coluna `date`** (ex: `OpportunityRequest.php`):

```php
protected function prepareForValidation()
{
    $timezone = Auth::user()->timezone ?? 'America/Sao_Paulo';

    foreach (['date_start', 'date_due', 'date_conclusion', 'date_canceled'] as $field) {
        if ($this->filled($field)) {
            $this->merge([
                $field => \App\Services\DateTimeConversionService::toLocalDate($this->input($field), $timezone),
            ]);
        }
    }
}
```

`toLocalDate()` aceita tanto `"2026-07-17"` (passa direto) quanto um ISO em UTC (`"2026-07-19T02:30:00.000Z"` → `"2026-07-18"` em São Paulo).

### Validação de Datas

```php
public function rules()
{
    return [
        'date_start' => 'nullable|date',
        'date_due' => 'nullable|date|after_or_equal:date_start',
        'date_conclusion' => 'nullable|date',
        'date_canceled' => 'nullable|date',
    ];
}
```

## 🎨 Como Usar no Frontend

### Componente DateInput

```vue
<DateInput
  v-model="form.date_start"
  label="Data de Início"
  name="date_start"
  placeholder="Selecione a data"
  :autoFillNow="true"  <!-- Preenche com data atual -->
/>
```

### Campo editável de coluna `date`: `date-only`

O `DateTimeEditableInput` (`components/fields/datetime/`) mostra data e hora por padrão. Para coluna `date`, use `date-only`: ele esconde a hora e o seletor de hora, e lê e emite `"YYYY-MM-DD"` sem passar por `Date` (que interpretaria como UTC).

```vue
<DateTimeEditableInput date-only label="Início:" :modelValue="opportunity.date_start"
  @save="$emit('update-field', 'date_start', $event)" />
```

Para exibir uma data só leitura, use `displayDate()` de `utils/date/dateUtils.js`, que já trata `"YYYY-MM-DD"` sem conversão. Não use `displayTime()`/`DateTimeValue` em coluna `date`.

### Props do DateInput
- `v-model`: Vincula com a variável do formulário
- `label`: Texto do rótulo
- `name`: Nome do campo
- `placeholder`: Texto placeholder
- `autoFillNow`: Se `true`, preenche automaticamente com a data atual

## ⏰ Prazos (`date_due`) de tarefas e oportunidades

Mudanças de prazo não são uma edição simples: cada alteração de um prazo já definido fica registrada em `due_date_changes` (prazo anterior, novo, motivo, observação, usuário).

- **Backend**: o trait `TracksDueDateChanges` (nos models `Task` e `Opportunity`) grava o histórico no `updated`. O controller passa o motivo com `$model->withDueDateChangeReason($reason, $note)` antes do `save()`. O trait `ValidatesDueDateChange` dos Requests **exige motivo ao adiar** (nova data depois da atual); `other` exige observação. Antecipar não exige motivo; definir o primeiro prazo não gera registro.
- **Motivos**: `DueDateChange::REASONS`, expostos em `GET due-date-change-reasons`.
- **Frontend**: use o `DueDateEditableInput` para editar prazo. Ele pede o motivo ao adiar, mostra "Prazo original · adiado Nx" com o histórico e emite o payload pronto para o PUT (`{ date_due, date_due_change_reason?, date_due_change_note? }`). Passe `:changes="entidade.due_date_changes"` (o `show`/`update` carregam `dueDateChanges.user`) e `date-only` para oportunidade.
- Para colocar o rastreio em outra entidade: use os dois traits, sobrescreva `dueDateHasTime()` se a coluna for `date`, e carregue `dueDateChanges.user` no resource.

## 📋 Padrão de Nomenclatura

### Backend (Laravel - snake_case)
```php
$opportunity->date_start     // Data de início
$opportunity->date_due       // Data de prazo/vencimento
$opportunity->date_conclusion // Data de conclusão
$opportunity->date_canceled  // Data de cancelamento
```

### Frontend (Vue - camelCase no JS, kebab no template)
```javascript
// No data()
form: {
  date_start: null,
  date_due: null,
}
```

```vue
<!-- No template -->
<DateInput v-model="form.date_start" />
```

## ⚠️ Importante

### ✅ SEMPRE faça:
1. Use `DateTimeConversionService` no `prepareForValidation()`
2. Valide com `'date'` nas rules
3. Use `nullable` se a data for opcional
4. Use `after_or_equal` para validar ordem de datas

### ❌ NUNCA faça:
1. Enviar data do frontend sem conversão
2. Fazer conversão manual com `strtotime()` ou similar
3. Armazenar datas como string no banco
4. Esquecer de validar datas relacionadas (ex: data_due >= data_start)

## 🔍 Exemplos no Projeto

### FormRequests com Datas
- ✅ `OpportunityRequest.php` - 4 campos `date` com `toLocalDate()`
- ✅ `TaskRequest.php` - Campos `datetime` com `convertJavascriptDate()`
- ✅ `ProposalRequest.php` - Converte date_due

### Formulários com DateInput
- ✅ `OpportunityCreateForm.vue` - date_start e date_due
- ✅ `TaskCreateForm.vue` - date_start e date_due
- ✅ `DebitInvoiceCreateForm.vue` - date_due

## 🐛 Troubleshooting

### Erro: "Invalid date format"
**Problema**: Data não foi convertida no `prepareForValidation()`
**Solução**: Adicione conversão usando `DateTimeConversionService`

### Erro: "The date_due must be a date after or equal to date_start"
**Problema**: Ordem das datas está incorreta ou conversão falhou
**Solução**: Verifique se ambas as datas foram convertidas e a ordem está correta

### Data salva errada no banco
**Problema**: Timezone não foi considerado
**Solução**: O `DateTimeConversionService` já trata timezone, verifique se está sendo usado

### Data aparece um dia antes (ex: 17/07 vira 16/07 21:00)
**Problema**: Coluna `date` passando por `convertFromUtc` no Resource ou por `new Date()` no frontend
**Solução**: Devolva o valor puro no Resource e use `date-only` / `displayDate()` no frontend (veja "Colunas `datetime` x `date`")

## 📚 Referências

- Service: `backend/app/Services/DateTimeConversionService.php`
- Exemplo Request: `backend/app/Http/Requests/OpportunityRequest.php`
- Componente: `frontend/src/components/forms/inputs/date/DateInput.vue`

---

**Regra de Ouro**: TODA data que vem do frontend DEVE passar pelo `DateTimeConversionService` antes de ir para o banco, com o método certo para o tipo da coluna (`convertJavascriptDate` para `datetime`, `toLocalDate` para `date`)!
