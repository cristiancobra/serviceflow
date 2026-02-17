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

### Em FormRequests

Sempre use o `prepareForValidation()` para converter datas antes da validação:

```php
// Exemplo: OpportunityRequest.php
protected function prepareForValidation()
{
    if ($this->filled('date_start')) {
        $this->merge([
            'date_start' => \App\Services\DateTimeConversionService::convertJavascriptDate(
                $this->input('date_start')
            ),
        ]);
    }

    if ($this->filled('date_due')) {
        $this->merge([
            'date_due' => \App\Services\DateTimeConversionService::convertJavascriptDate(
                $this->input('date_due')
            ),
        ]);
    }
}
```

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

### Props do DateInput
- `v-model`: Vincula com a variável do formulário
- `label`: Texto do rótulo
- `name`: Nome do campo
- `placeholder`: Texto placeholder
- `autoFillNow`: Se `true`, preenche automaticamente com a data atual

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
- ✅ `OpportunityRequest.php` - Converte 4 campos de data
- ✅ `TaskRequest.php` - Converte date_start e date_due
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

## 📚 Referências

- Service: `backend/app/Services/DateTimeConversionService.php`
- Exemplo Request: `backend/app/Http/Requests/OpportunityRequest.php`
- Componente: `frontend/src/components/forms/inputs/date/DateInput.vue`

---

**Regra de Ouro**: TODA data que vem do frontend DEVE passar pelo `DateTimeConversionService` antes de ir para o banco!
