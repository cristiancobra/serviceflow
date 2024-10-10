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

// validate model
export const validateModel = (model) => {
  const VALID_MODELS = [
    "accounts",
    "companies",
    "costs",
    "leads",
    "projects",
    "proposals",
    "opportunities",
    "services",
    "tasks",
    "users"
  ];
  if (!VALID_MODELS.includes(model)) {
    throw new Error(`Invalid model: ${model}`);
  }
};