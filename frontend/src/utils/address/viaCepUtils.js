// Consulta de CEP no ViaCEP (gratuito, sem chave). Usado para preencher o código IBGE
// do município, exigido na NFS-e.
// Usa fetch e não axios para não enviar o token da API do ServiceFlow para um serviço externo.
export const fetchAddressByCep = async (cep) => {
  const digits = String(cep || "").replace(/\D/g, "");
  if (digits.length !== 8) return null;

  try {
    const response = await fetch(`https://viacep.com.br/ws/${digits}/json/`);
    if (!response.ok) return null;

    const data = await response.json();
    if (data.erro) return null;

    return {
      ibgeCityCode: data.ibge,
      city: data.localidade,
      state: data.uf,
    };
  } catch (error) {
    console.error("Erro ao consultar o CEP:", error);
    return null;
  }
};
