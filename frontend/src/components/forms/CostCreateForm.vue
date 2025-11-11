<template>
  <div>
    <button type="button" class="button button-new" @click=openModal>
      <font-awesome-icon icon="fa-solid fa-cogs" class="button-icon" />
      <span class="button-text"></span>
    </button>

    <div v-if="isModalVisible" class="myModal">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <font-awesome-icon icon="fa-solid fa-tasks" class="icon pe-3 primary" />
            <h2 class="modal-title" id="taskModalLabel">Novo custo</h2>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm">
              <div class="">
                <div class="col-12">
                  <TextInput label="Nome" name="name" v-model="form.name" placeholder="nome do custo" />
                  <div class="error-row" v-if="errors.name">
                    <span class="error-text">* {{ errors.name[0] }}</span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-2">
                  <label class="labels" for="observations"> Descrição </label>
                </div>
                <div class="col-10">
                  <input class="form-control" type="text" id="observations" v-model="form.observations"
                    placeholder="Descreva o custo" />
                </div>
              </div>
              <div class="row">
                <div class="col-4">
                  <label class="labels" for="price">
                    Preço</label>
                </div>
                <div class="col">
                  <input class="form-control" type="text" name="price" v-model="form.price" v-mask-decimal.br="2" />
                  <div class="error-row" v-if="errors.price">
                    <span class="error-text">* {{ errors.price[0] }}</span>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeModal">Fechar</button>
                <button type="submit" class="button-new">criar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { submitFormCreate } from "@/utils/requests/httpUtils";
import TextInput from "./inputs/text/TextInput";

export default {
  emits: ["new-cost-event"],
  data() {
    return {
      allStatus: [],
      formattedLaborHours: "",
      formattedLaborHourlyRate: "",
      laborHourlyRateMinutes: "",
      message: null,
      data: [],
      errors: [],
      form: {
        name: null,
        observations: null,
        price: null,
      },
      isModalVisible: false,
    };
  },
  components: {
    TextInput,
  },
  methods: {
    submitFormCreate,
    closeModal() {
      this.isModalVisible = false;
    },
    async submitForm() {
      if (this.form.price) {
        this.form.price = parseFloat(this.form.price.replace(',', '.'));
      }
      const { data, error } = await this.submitFormCreate("costs", this.form);

      if (data) {
        this.isModalVisible = false;
        this.$emit("new-cost-event", data);
      }
      if (error) {
        this.errors = error;
      }
    },
    openModal() {
      this.isModalVisible = true;
    },
    validateTimeInput() {
      const input = this.form.labor_hours;
      const timeRegex = /^(2[0-3]|[01]?[0-9]):([0-5]?[0-9])$/;

      if (!timeRegex.test(input)) {
        // O valor não está no formato de hora válido, você pode tratar isso aqui.
        // Por exemplo, definir um valor padrão ou exibir uma mensagem de erro.
        this.form.labor_hours = ""; // Defina um valor padrão vazio por enquanto.
      }
    },
  },
};
</script>

<style scoped>

.button-icon {
    margin-right: 0.5rem;
}

.button-text {
    font-size: 1rem;
    font-weight: 600;
}

.container {
  border-style: solid;
  border-color: #ff3eb5;
  border-width: 2px;
  margin-left: 180px;
  margin-right: 180px;
  margin-bottom: 60px;
  margin-top: 60px;
  padding: 20px;
  border-radius: 16px;
  transition: all 0.5s;
  text-align: left;
  font-weight: 800;
}

.labels {
  text-align: left;
  margin-left: 0;
  padding-top: 12px;
}

.new {
  background-color: #ff3eb5;
  color: white;
  font-weight: 800;
  padding: 10px 20px 10px 20px;
}
</style>