import axios from 'axios';
import { BACKEND_URL } from "@/config/apiConfig";

//delete a model
export const destroy = async (model, id) => {
  validateModel(model);
  try {
    const response = await axios.delete(
      `${BACKEND_URL}${model}/${id}`
    );
    const { data } = response.data;
    // const successMessage = "Serviço excluído com sucesso";
    // this.$router.push({ name: "proposalsIndex", query: { successMessage } });
    return data;
  } catch (error) {
    console.error("Erro ao deletar serviço:", error);
    throw error;
  }
};

export const destroyRelationship = async (parentModel, parentId, relatedModel, relatedId) => {
  validateModel(parentModel);
  validateModel(relatedModel);

  try {
    const response = await axios.delete(
      `${BACKEND_URL}${parentModel}/${parentId}/${relatedModel}/${relatedId}`
    );

    const { data } = response.data;
    return data;
  } catch (error) {
    console.error(`Erro ao deletar o relacionamento entre ${parentModel} e ${relatedModel}:`, error);
    throw error;
  }
};

// get total of a model
export const getTotalProposals = async () => {
  const url = `${BACKEND_URL}proposals/report`;
  try {
    const response = await axios.get(url);
    const reports = response.data;
    return reports;
  } catch (error) {
    console.error("Error fetching total proposals:", error);
    throw error;
  }
};

// fetches all models to index page
export const index = async (model) => {
  validateModel(model);
  const url = `${BACKEND_URL}${model}`;
  try {
    const response = await axios.get(url);
    const data = response.data.data;
    return data;
  } catch (error) {
    console.error("Error fetching index data:", error);
    throw error;
  }
};

// fetches a single model to SHOW page
export const show = async (model, id) => {
  validateModel(model);
  const url = `${BACKEND_URL}${model}/${id}`;
  try {
    const response = await axios.get(url);
    const data = response.data.data;
    return data;
  } catch (error) {
    console.error("Error fetching show data:", error);
    throw error;
  }
};


export const submitFormCreate = async (model, form) => {
  validateModel(model);
  try {
    const response = await axios.post(
      `${BACKEND_URL}${model}`,
      form
    );

    const { data } = response.data;

    return { data, error: null };

  } catch (error) {
    console.error("Error submitting form:", error);

    let errorData;
    if (!error.response || !error.response.data.errors) {
      errorData = { form: ["Ocorreu um erro ao enviar o formulário. Tente novamente."] };
    } else {
      errorData = error.response.data.errors;
    }

    return { data: null, error: errorData };
  }
};

export const submitFormUpdate = async (model, id, form) => {
  validateModel(model);
  try {
    const response = await axios.patch(
      `${BACKEND_URL}${model}/${id}`,
      form
    );

    const { data } = response.data;

    return { data, error: null };

  } catch (error) {
    console.error("Error submitting form:", error);

    let errorData;
    if (!error.response || !error.response.data.errors) {
      errorData = { form: ["Ocorreu um erro ao enviar o formulário. Tente novamente."] };
    } else {
      errorData = error.response.data.errors;
    }

    return { data: null, error: errorData };
  }
}


// update on field of a model
export const updateField = async (model, id, fieldName, value) => {
  validateModel(model);
  try {
    const response = await axios.patch(
      `${BACKEND_URL}${model}/${id}`,
      { [fieldName]: value }
    );

    const { data } = response.data;
    return data;

  } catch (error) {
    console.error("Error updating fieldName:", error);

    if (!error.response) {
      throw new Error("Ocorreu um erro ao atualizar o campo. Tente novamente.");
    } else {
      throw error;
    }
  }
};

export const updateRelationshipField = async (parentModel, parentId, payload) => {
  validateModel(parentModel);

  try {
    const response = await axios.patch(
      `${BACKEND_URL}${parentModel}/${parentId}`,
      payload
    );

    const { data } = response.data;
    return data;

  } catch (error) {
    console.error("Error updating relationship field:", error);

    if (!error.response) {
      throw new Error("Ocorreu um erro ao atualizar o campo do relacionamento. Tente novamente.");
    } else {
      throw error;
    }
  }
};

// validate model
export const validateModel = (model) => {
  
  const VALID_MODELS = [
    "accounts",
    "bank_accounts",
    "companies",
    "costs",
    "leads",
    "links",
    "invoices",
    "projects",
    "proposals",
    "opportunities",
    "services",
    "tasks",
    "transactions",
    "users"
  ];
  if (!VALID_MODELS.includes(model)) {
    throw new Error(`Invalid model: ${model}`);
  }
};

// Generic GET request for custom routes (não valida modelo)
export const get = async (endpoint) => {
  const url = `${BACKEND_URL}${endpoint}`;
  try {
    const response = await axios.get(url);
    return response.data;
  } catch (error) {
    console.error("Error fetching data:", error);
    throw error;
  }
};

// Generic POST request for custom routes (não valida modelo)
export const post = async (endpoint, data = {}) => {
  const url = `${BACKEND_URL}${endpoint}`;
  try {
    const response = await axios.post(url, data);
    return response.data;
  } catch (error) {
    console.error("Error posting data:", error);
    throw error;
  }
};

// Generic PUT/PATCH request for updating resources
export const update = async (model, id, data) => {
  validateModel(model);
  const url = `${BACKEND_URL}${model}/${id}`;
  try {
    const response = await axios.put(url, data);
    return response.data.data;
  } catch (error) {
    console.error("Error updating data:", error);
    throw error;
  }
};

// Generic POST request for storing new resources
export const store = async (model, data) => {
  validateModel(model);
  const url = `${BACKEND_URL}${model}`;
  try {
    const response = await axios.post(url, data);
    return response.data.data;
  } catch (error) {
    console.error("Error storing data:", error);
    throw error;
  }
};