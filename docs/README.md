# Documentação ServiceFlow

Bem-vindo à documentação do projeto ServiceFlow! 📚

## Sobre o Projeto

ServiceFlow é um sistema de gestão de serviços e oportunidades de negócio desenvolvido com:
- **Backend**: Laravel (PHP)
- **Frontend**: Vue.js 3 + Tailwind CSS + DaisyUI
- **Database**: MySQL

## 📑 Índice da Documentação

### 📘 Padrões e Convenções
- [Padrões de Formulários](./frontend/forms-pattern.md) - Como criar formulários seguindo o padrão do projeto
- [Padrões de Componentes](./frontend/components-pattern.md) - Componentes reutilizáveis e suas convenções
- [Tratamento de Datas](./backend/date-handling.md) - Como lidar com datas entre frontend e backend

### 🎨 Frontend (Vue.js)
- [Estrutura de Componentes](./frontend/component-structure.md)
- [Estilos e CSS](./frontend/styling-guide.md)
- [Componentes de Formulário](./frontend/form-components.md)
- [Componentes de Seleção (Selects)](./frontend/select-components.md)

### ⚙️ Backend (Laravel)
- [Estrutura de Requests](./backend/request-validation.md)
- [Services e Helpers](./backend/services.md)
- [API Endpoints](./backend/api-endpoints.md)

### 🔧 Utilitários
- [Comandos Úteis](./utils/commands.md)
- [Troubleshooting](./utils/troubleshooting.md)

## 🚀 Como Usar Esta Documentação

1. **Para criar um novo formulário**: Consulte [Padrões de Formulários](./frontend/forms-pattern.md)
2. **Para criar um novo componente**: Consulte [Padrões de Componentes](./frontend/components-pattern.md)
3. **Dúvida sobre datas**: Consulte [Tratamento de Datas](./backend/date-handling.md)
4. **Precisa de um componente existente**: Consulte [Componentes de Formulário](./frontend/form-components.md)

## 📝 Convenções Gerais

### Nomenclatura
- **Componentes Vue**: PascalCase (ex: `OpportunityCreateForm.vue`)
- **Arquivos JS/TS**: camelCase (ex: `dateUtils.js`)
- **CSS Classes**: kebab-case (ex: `btn-primary`)
- **Variáveis PHP**: snake_case (ex: `date_start`)

### Estrutura de Pastas
```
frontend/src/
├── components/
│   ├── buttons/      # Botões reutilizáveis
│   ├── forms/        # Formulários e inputs
│   ├── lists/        # Componentes de listagem
│   └── ...
├── views/            # Páginas principais
├── assets/           # CSS, imagens, etc
└── utils/            # Funções utilitárias
```

## 🆘 Precisa de Ajuda?

Se algo não está documentado aqui, verifique:
1. Componentes similares no projeto
2. Commits recentes relacionados
3. Adicione à documentação depois de resolver! 😉

---

**Última atualização**: Fevereiro 2025
