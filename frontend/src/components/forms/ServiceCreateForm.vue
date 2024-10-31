<template>
  <div>
    <button type="button" class="button button-new d-flex justify-content-center" @click=openModal>
      <font-awesome-icon icon="fa-solid fa-plus" class="" />
    </button>

    <div v-if="isModalVisible" class="myModal">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="myModal-header">
            <font-awesome-icon icon="fa-solid fa-tasks" class="myModal-icon" />
            <h5 class="myModal-title" id="taskModalLabel">Novo serviço</h5>
            <button type="button" class="myModal-button-close" @click="closeModal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm">
              <div class="row">
                <div class="col">
                  <TextInput label="Nome" name="name" v-model="form.name" placeholder="nome do serviço" />
                  <div class="error-row" v-if="errors.name">
                    <span class="error-text">* {{ errors.name[0] }}</span>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row">
                  <div class="col-4">
                    <label class="labels" for="observations"> Observações </label>
                  </div>
                  <div class="col-8">
                    <input class="form-control" type="text" id="observations" v-model="form.observations"
                      placeholder="Digite o nome do responsável por garantir a execução do projeto" />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row">
                  <div class="col-4">
                    <label class="labels" for="hours">Tempo de trabalho</label>
                  </div>
                  <div class="col">
                    <label class="labels">horas</label>
                  </div>
                  <div class="col">
                    <input class="form-control" type="number" name="hours" v-model="form.hours" placeholder="HH" min="0"
                      max="23" />
                  </div>
                  <div class="col">
                    <label class="labels">minutos</label>
                  </div>
                  <div class="col">
                    <input class="form-control" type="number" name="minutes" v-model="form.minutes" placeholder="MM"
                      min="0" max="59" />
                  </div>
                </div>
                <div class="row">
                  <div class="col-4">
                    <label class="labels" for="labor_hourly_rate_minutes">
                      Valor da hora de trabalho
                    </label>
                  </div>
                  <div class="col">
                    <input class="form-control text-end" type="text" name="labor_hourly_rate"
                      v-model="form.labor_hourly_rate" v-mask-decimal.br="2" />
                  </div>
                </div>
                <div class="row">
                  <div class="col-4">
                    <label class="labels" for="profit_percentage">
                      Margem de lucro
                    </label>
                  </div>
                  <div class="col-2 d-flex">
                    <label class="labels" for="profit_percentage">
                      percentual
                    </label>
                  </div>
                  <div class="col-2">
                    <input class="form-control text-end" type="text" name="profit_percentage"
                      v-model="form.profit_percentage" v-mask="'000'" />
                  </div>
                  <div class="col-2 d-flex">
                    <label class="labels" for="profit">
                      valor em R$
                    </label>
                  </div>
                  <div class="col-2">
                    <input class="form-control text-end" type="text" name="profit" v-model="form.profit"
                      v-mask-decimal.br="2" />
                  </div>
                  <div class="row">
                    <div class="col-4">
                      <label class="labels" for="price">
                        Preço final
                      </label>
                    </div>
                    <div class="col-8">
                      <input class="form-control text-end" type="text" name="price" v-model="form.price"
                        v-mask-decimal.br="2" disabled />
                    </div>
                  </div>
                </div>
              </div>
              <div class="row ms-5 me-5 mt-4 mb-2">
                <button type="submit" class="btn new">Criar</button>
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
  name: "ServiceCreateForm",
  emits: ["new-service-event"],
  data() {
    return {
      allStatus: [],
      message: null,
      data: [],
      errors: [],
      form: {
        name: null,
        observations: null,
        labor_hours: null,
        labor_hourly_rate: 0,
        profit_percentage: 0,
        profit: 0,
        price: 0,
        hours: 1,
        minutes: 0,
      },
      isModalVisible: false,
      userInput: true,
    };
  },
  components: {
    TextInput,
  },
  methods: {
    submitFormCreate,
    calculateProfit() {
      if (this.userInput) {
        let formattedLaborHours = this.form.hours + this.form.minutes / 60;
        let formattedLaborHourlyRate = parseFloat(this.form.labor_hourly_rate.replace(',', '.'));
        let operationalCost = formattedLaborHourlyRate * formattedLaborHours;
        let priceWithoutProfit = operationalCost / (1 - this.form.profit_percentage / 100);
        this.form.profit = parseFloat((priceWithoutProfit - operationalCost).toFixed(2));
        // this.calculatePrice();
      }
    },
    calculatePrice() {
      console.log('profit', this.form.profit);
      let operationalCost = parseFloat(this.form.labor_hourly_rate.replace(',', '.')) * (this.form.hours + this.form.minutes / 60);
      operationalCost = parseFloat(operationalCost.toFixed(2));
      console.log('operationalCost', operationalCost);
      let currentProfit = parseFloat(this.form.profit.replace(',', '.')).toFixed(2);
      console.log('currentProfit', currentProfit);
      this.form.price = (operationalCost + currentProfit);
      // if (typeof this.form.labor_hourly_rate === 'string') {
        // this.form.price = (parseFloat(this.form.profit) + parseFloat(this.form.labor_hourly_rate.replace(',', '.')) * (this.form.hours + this.form.minutes / 60)).toFixed(2);
      // } else {
      //   this.form.price = (parseFloat(this.form.profit) + this.form.labor_hourly_rate * (this.form.hours + this.form.minutes / 60)).toFixed(2);
      // }
      console.log('price depois', this.form.price);
    },
    closeModal() {
      this.isModalVisible = false;
    },
    async submitForm() {
      this.form.labor_hours = this.form.hours * 3600 + this.form.minutes * 60;
      this.form.labor_hourly_rate = parseFloat(this.form.labor_hourly_rate.replace(',', '.'));
      this.form.profit = parseFloat(this.form.profit.replace(',', '.'));
      this.form.price = parseFloat(this.form.price.replace(',', '.'));

      const { data, error } = await this.submitFormCreate("services", this.form);

      if (data) {
        this.isModalVisible = false;
        this.$emit("new-service-event", data);
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
  watch: {
    'form.labor_hourly_rate': 'calculatePrice',
    'form.hours': 'calculatePrice',
    'form.minutes': 'calculatePrice',
    'form.profit_percentage': function () {
      this.userInput = true;
      this.calculateProfit();
    },
    // 'form.hours': function () {
    //   this.userInput = true;
    //   this.calculatePrice();
    // },
    // 'form.minutes': function () {
    //   this.userInput = true;
    //   this.calculatePrice();
    // },
    'form.profit': function (newVal) {
      this.userInput = false;
      this.form.profit = newVal;
      this.calculatePrice();
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