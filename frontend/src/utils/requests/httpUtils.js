import axios from 'axios';
import { BACKEND_URL } from "@/config/apiConfig";


// fetches all models to index page
export const fetchIndexData = async (model) => {
    if (!["tasks", "projects", "opportunities", "users"].includes(model)) {
      throw new Error(`Invalid model: ${model}`);
    }
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

  // fetches a single model to show page
export const fetchShowData = async (model, id) => {
    if (!["tasks", "projects", "opportunities", "users"].includes(model)) {
      throw new Error(`Invalid model: ${model}`);
    }
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
    if (!["tasks", "projects", "opportunities", "users"].includes(model)) {
      throw new Error(`Invalid model: ${model}`);
    }
  
    try {
      const response = await axios.post(
        `${BACKEND_URL}${model}`,
        form
      );
  
      const { data } = response.data;
      return data;
  
    } catch (error) {
      console.error("Error submitting form:", error);
  
      if (!error.response) {
        throw new Error("Ocorreu um erro ao enviar o formulário. Tente novamente.");
      } else {
        throw error;
      }
    }
  };
  