export function convertBrToCurrency(value) {
  // Verifica se o valor é uma string e remove caracteres não numéricos, substituindo a vírgula por ponto
  if (typeof value === 'string') {
    // Remove caracteres não numéricos e substitui a vírgula por ponto
    value = value.replace(/\s+/g, '').replace(',', '.');
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
