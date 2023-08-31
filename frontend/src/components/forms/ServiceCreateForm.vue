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
              <label class="labels" for="labor_hours">Horas de Trabalho</label>
            </div>
            <div class="col">
              <input
                class="form-control"
                type="text"
                name="labor_hours"
                v-model="form.labor_hours"
                v-mask="'000'"
              />
            </div>
            <div class="col">
              <select id="minutes" v-model="form.minutes">
                <option
                  v-for="minute in minutes"
                  :key="minute.value"
                  :value="minute.value"
                >
                  {{ minute.label }}
                </option>
              </select>
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
      minutes: [
        { value: null, label: "" },
        { value: 0.25, label: "15 minutos" },
        { value: 0.5, label: "30 minutos" },
        { value: 0.75, label: "45 minutos" },
      ],
      message: null,
      data: [],
      form: {
        name: null,
        observations: null,
        labor_hours: null,
        labor_hourly_rate: null,
        minutes: null,
        profit_percentage: null,
      },
    };
  },
  methods: {
    async submitForm() {
      console.log(this.form);
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
    // validateInput() {
    //   // Verifica se o valor é maior que 100
    //   // Verifica se há mais de um ponto decimal
    //   const parts = this.formattedLaborHours.split(".");
    //   if (parts.length > 2) {
    //     this.formattedLaborHours =
    //       parts[0] +
    //       "." +
    //       parts.slice(1, parts.length - 1).join("") +
    //       parts[parts.length - 1];
    //   }

    //   // Limita o número de dígitos a 3 além do decimal
    //   const decimalIndex = this.formattedLaborHours.indexOf(".");
    //   if (
    //     decimalIndex !== -1 &&
    //     decimalIndex < this.formattedLaborHours.length - 4
    //   ) {
    //     this.formattedLaborHours = this.formattedLaborHours.substring(
    //       0,
    //       decimalIndex + 4
    //     );
    //   }

    //   // Atualiza a propriedade labor_hours com o valor atualizado de formattedLaborHours
    //   this.form.labor_hours = parseFloat(
    //     this.formattedLaborHours.replace(",", ".")
    //   );
    // },
    // updateLaborHourlyRate() {
    //   // Remove todos os caracteres não numéricos, exceto o ponto decimal
    //   this.formattedLaborHourlyRate = this.formattedLaborHourlyRate.replace(/[^0-9.]/g, '');

    //   // Atualiza a propriedade laborHourlyRate com o valor atualizado de formattedLaborHourlyRate
    //   this.laborHourlyRate = parseFloat(this.formattedLaborHourlyRate.replace(',', '.'));
    // },
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