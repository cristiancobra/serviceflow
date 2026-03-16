export function convertBrToCurrency(value) {
  // Se já é número, retorna direto
  if (typeof value === 'number') {
    return value;
  }

  // Verifica se o valor é uma string
  if (typeof value === 'string') {
    // Remove espaços
    value = value.trim().replace(/\s+/g, '');
    
    // Se contém vírgula, é formato brasileiro (ex: 1.500,00 ou 500,00)
    if (value.includes(',')) {
      // Remove pontos (separadores de milhar) e substitui vírgula por ponto
      value = value.replace(/\./g, '').replace(',', '.');
    }
    // Se não tem vírgula mas tem ponto, já está em formato americano (ex: 1500.00 ou 50.00)
    // Neste caso, não faz nada, já está correto
  }

  // Converte a string para número
  const numberValue = parseFloat(value);

  // Verifica se a conversão resultou em um valor válido
  if (isNaN(numberValue)) {
    console.error(`Valor inválido: ${value}`);
    return null;
  }

  return numberValue;
}

export function convertCurrencyToBr(value) {
  if (value === null || value === undefined) {
    console.error("Valor é nulo ou indefinido.");
    return ''; // Retorna uma string vazia ou outro valor padrão
  }

  if (typeof value === 'string') {
    value = parseFloat(value.replace(',', '.')); // Converte a string para número
  }

  if (isNaN(value)) {
    console.error(`Valor inválido: ${value}`);
    return '';
  }

  return value.toFixed(2).replace('.', ',');
}

export function formatCurrencySymbol(value, locale = 'pt-BR', currency = 'BRL') {

  if (value !== null && value !== undefined) {
    let convertedValue = convertBrToCurrency(value);

    const formattedValue = new Intl.NumberFormat(locale, {
      style: 'currency',
      currency: currency,
    }).format(convertedValue);

    return formattedValue;
  } else {
    console.error("Valor é nulo ou indefinido, não será formatado.");
  }
}
