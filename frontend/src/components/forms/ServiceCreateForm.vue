<template>
  <div>
    <div class="mt-5 mb-5 success">
      {{ message }}
    </div>

    <div id="form" class="container">
      <form @submit.prevent="submitForm">
        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="name"> Nome </label>
            </div>
            <div class="col-10">
              <input
                class="form-control"
                type="text"
                id="name"
                v-model="form.name"
                placeholder="Digite um nome para seu projeto"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="observations"> Observações </label>
            </div>
            <div class="col-10">
              <input
                class="form-control"
                type="text"
                id="observations"
                v-model="form.observations"
                placeholder="Digite o nome do responsável por garantir a execução do projeto"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col">
              <label class="labels" for="hours">Tempo de trabalho</label>
            </div>
            <div class="col">
              <input
                class="form-control"
                type="number"
                name="hours"
                v-model="form.hours"
                placeholder="HH"
                min="0"
                max="23"
              />
            </div>
            <div class="col">
              <input
                class="form-control"
                type="number"
                name="minutes"
                v-model="form.minutes"
                placeholder="MM"
                min="0"
                max="59"
              />
            </div>

            <div class="col">
              <label class="labels" for="labor_hourly_rate_minutes">
                Valor da hora de trabalho</label
              >
            </div>
            <div class="col">
              <input
                class="form-control"
                type="text"
                name="labor_hourly_rate"
                v-model="form.labor_hourly_rate"
                v-mask-decimal.br="2"
                @input="updateLaborHourlyRate"
              />
            </div>

            <div class="col">
              <label class="labels" for="profit_percentage">
                Percentual de lucro</label
              >
            </div>
            <div class="col">
              <input
                class="form-control"
                type="text"
                name="profit_percentage"
                v-model="form.profit_percentage"
                v-mask="'00'"
              />
            </div>
          </div>
        </div>

        <div class="row ms-5 me-5 mt-4 mb-2">
          <button type="submit" class="btn new">Criar</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ServiceCreateForm",
  emits: ["new-service-event"],
  data() {
    return {
      allStatus: [],
      formattedLaborHours: "",
      formattedLaborHourlyRate: "",
      laborHourlyRateMinutes: "",
      message: null,
      data: [],
      form: {
        name: null,
        observations: null,
        labor_hours: null,
        labor_hourly_rate: 0,
        profit_percentage: 0,
        hours: 1, // Campo para horas
        minutes: 0, // Campo para minutos
      },
    };
  },
  methods: {
    async submitForm() {
      this.form.labor_hours = this.form.hours * 3600 + this.form.minutes * 60;
      
      axios
        .post("http://localhost:8191/api/services", this.form)
        .then((response) => {
          this.data = response.data;
          this.newServiceEvent(this.data);
        });
    },
    newServiceEvent(data) {
      this.$emit("new-service-event", data);
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