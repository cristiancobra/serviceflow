  export function translateStatus(status) {

    const translations = {
        'to-do': 'fazer',
        'done': 'feito',
        'canceled': 'cancelada',
        'doing': 'fazendo',
        'wait': 'pendente',
      };

    return translations[status] || status;

  }
  
  export function translatePriority(priority) {

    const translations = {
        'low': 'baixa',
        'medium': 'média',
        'high': 'alta',
      };

    return translations[priority] || priority;

  }
  