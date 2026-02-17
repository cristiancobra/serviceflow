# Guia de Estilos e CSS

## 🎨 Tecnologias de Estilo

O projeto usa uma combinação de:
- **Tailwind CSS** - Classes utilitárias (padrão atual)
- **DaisyUI** - Componentes baseados em Tailwind
- **CSS Custom** - Estilos legados em `frontend/src/assets/css/`

## 📘 Padrão Atual: Tailwind CSS

### Por que Tailwind?
✅ Classes reutilizáveis
✅ Responsivo por padrão
✅ Mantém consistência visual
✅ Reduz CSS customizado

### Exemplo
```vue
<!-- ❌ Não faça (CSS customizado) -->
<div class="my-custom-card">...</div>

<!-- ✅ Faça (Tailwind) -->
<div class="bg-white rounded-lg shadow-md p-4">...</div>
```

## 🎨 Paleta de Cores

### Cores Principais (DaisyUI)
```css
--color-primary: #B1388D        /* Roxo principal */
--color-secondary: oklch(...)   /* Secundária */
--color-accent: oklch(...)      /* Destaque */
--color-success: #2CB48D        /* Verde sucesso */
--color-error: #fb2c36          /* Vermelho erro */
```

### Gradientes por Contexto
```vue
<!-- Leads/Contatos -->
<div class="bg-gradient-to-r from-blue-50 to-blue-25">

<!-- Empresas -->
<div class="bg-gradient-to-r from-green-50 to-green-25">

<!-- Oportunidades -->
<div class="bg-gradient-to-r from-purple-50 to-purple-25">

<!-- Propostas -->
<div class="bg-gradient-to-r from-yellow-50 to-yellow-25">
```

## 🔘 Botões

### Botão Primário (DaisyUI)
```vue
<button class="btn btn-primary">
  Criar
</button>
```

**Estilos aplicados**:
- Borda branca de 1px (contraste em fundos roxos)
- Hover: borda aumenta para 3px com glow
- Transição suave de 0.3s

### Botão com Gradiente
```vue
<!-- Botão Azul -->
<button class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg font-semibold hover:shadow-lg transition-all hover:-translate-y-0.5">
  Criar
</button>

<!-- Botão Verde -->
<button class="px-6 py-2 bg-gradient-to-r from-green-600 to-green-800 text-white rounded-lg font-semibold hover:shadow-lg transition-all hover:-translate-y-0.5">
  Salvar
</button>
```

### Botão Cancelar
```vue
<button class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
  Cancelar
</button>
```

## 📱 Responsividade

### Breakpoints Tailwind
```
sm: 640px   - Mobile grande
md: 768px   - Tablet
lg: 1024px  - Desktop pequeno
xl: 1280px  - Desktop
2xl: 1536px - Desktop grande
```

### Grid Responsivo
```vue
<!-- 1 coluna em mobile, 2 em tablet+ -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div>Coluna 1</div>
  <div>Coluna 2</div>
</div>

<!-- 1 coluna em mobile, 2 em tablet, 3 em desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
  <div>Item 1</div>
  <div>Item 2</div>
  <div>Item 3</div>
</div>
```

### Ocultar/Mostrar por Tamanho
```vue
<!-- Visível apenas em mobile -->
<div class="block md:hidden">Mobile only</div>

<!-- Oculto em mobile -->
<div class="hidden md:block">Desktop only</div>
```

## 🪟 Modais

### Estrutura Padrão
```vue
<div class="fixed inset-0 z-50 flex items-center justify-center p-4" 
     style="background-color: rgba(0, 0, 0, 0.25)">
  <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
    <!-- Conteúdo do modal -->
  </div>
</div>
```

**Classes importantes**:
- `fixed inset-0` - Ocupa tela inteira
- `z-50` - Fica sobre outros elementos
- `max-h-[90vh]` - Altura máxima de 90% da tela
- `overflow-y-auto` - Scroll interno se necessário
- `rounded-2xl` - Bordas bem arredondadas
- `shadow-2xl` - Sombra forte

### Header Sticky
```vue
<div class="sticky top-0 bg-gradient-to-r from-blue-50 to-blue-25 border-b border-gray-200 px-8 py-6">
  <!-- Título e botão fechar -->
</div>
```

### Footer Sticky
```vue
<div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-8 py-4 flex justify-end gap-3">
  <!-- Botões de ação -->
</div>
```

## 📝 Inputs

### Input de Texto
```vue
<input 
  type="text"
  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
  placeholder="Digite aqui"
/>
```

### TextArea
```vue
<textarea
  rows="3"
  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-none"
  placeholder="Digite aqui"
></textarea>
```

### Select
```vue
<select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
  <option>Opção 1</option>
  <option>Opção 2</option>
</select>
```

## 🎯 Classes Customizadas

### Localização
`frontend/src/assets/css/style.css`

### Botão Primary (customizado)
```css
.btn.btn-primary {
  border: 1px solid white;
  transition: all 0.3s ease;
  box-sizing: border-box;
}

.btn.btn-primary:hover {
  border: 3px solid white;
  box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
  transform: scale(1.05);
}
```

## 📏 Espaçamento

### Padrão de Espaçamento
```vue
<!-- Padding interno -->
<div class="p-4">    <!-- 1rem (16px) -->
<div class="p-6">    <!-- 1.5rem (24px) -->
<div class="p-8">    <!-- 2rem (32px) -->

<!-- Margin -->
<div class="mt-2">   <!-- margin-top: 0.5rem -->
<div class="mb-4">   <!-- margin-bottom: 1rem -->

<!-- Gap em Flex/Grid -->
<div class="flex gap-3">     <!-- 0.75rem -->
<div class="grid gap-6">     <!-- 1.5rem -->
```

### Espaçamento em Formulários
```vue
<!-- Entre campos -->
<form class="space-y-6">
  <div>Campo 1</div>
  <div>Campo 2</div>
</form>
```

## 🎭 Transições e Animações

### Transição de Cor
```vue
<button class="transition-colors duration-200 hover:bg-blue-600">
  Botão
</button>
```

### Transição Completa
```vue
<button class="transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5">
  Botão com Lift
</button>
```

### Fade In/Out
```vue
<div class="transition-opacity duration-300" 
     :class="isVisible ? 'opacity-100' : 'opacity-0'">
  Conteúdo
</div>
```

## ✅ Boas Práticas

### ✅ FAÇA
1. Use classes Tailwind sempre que possível
2. Use `space-y-*` para espaçamento vertical consistente
3. Use grid responsivo (`grid-cols-1 md:grid-cols-2`)
4. Adicione transições em hover (`transition-colors`)
5. Use `rounded-lg` ou `rounded-2xl` para bordas arredondadas

### ❌ NÃO FAÇA
1. Criar CSS customizado para coisas que Tailwind já faz
2. Usar valores fixos de pixel (use classes Tailwind)
3. Esquecer responsividade
4. Misturar padrões (use apenas Tailwind nos novos componentes)

## 📚 Referências

- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [DaisyUI Components](https://daisyui.com/components/)
- Arquivo de estilos customizados: `frontend/src/assets/css/style.css`
- Configuração Tailwind: `backend/tailwind.config.js`

---

**Regra de Ouro**: Prefira Tailwind CSS sobre CSS customizado. Se precisar de algo que Tailwind não oferece, considere se realmente precisa antes de criar CSS novo!
