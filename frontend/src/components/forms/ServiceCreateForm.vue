<template>
  <div>
    <button
      type="button"
      class="button button-new d-flex justify-content-center"
      @click="openModal"
    >
      <font-awesome-icon icon="fa-solid fa-plus" class="" />
    </button>

    <div v-if="isModalVisible" class="myModal">
      <div class="modal-content">
        <div class="myModal-header">
          <font-awesome-icon icon="fa-solid fa-tasks" class="myModal-icon" />
          <h5 class="myModal-title" id="taskModalLabel">Novo serviço</h5>
          <button
            type="button"
            class="myModal-button-close"
            @click="closeModal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="submitForm">
            <div class="form-section">
              <div class="table-row">
                <div class="column-100">
                  <TextInput
                    label="Nome"
                    name="name"
                    v-model="form.name"
                    placeholder="nome do serviço"
                  />
                  <div class="error-row" v-if="errors.name">
                    <span class="error-text">* {{ errors.name[0] }}</span>
                  </div>
                </div>
              </div>

              <div class="table-row">
                <div class="column-25">
                  <label class="labels" for="observations"> Observações </label>
                </div>
                <div class="column-80">
                  <input
                    class="form-control"
                    type="textarea"
                    id="observations"
                    v-model="form.observations"
                    placeholder="Digite o nome do responsável por garantir a execução do projeto"
                  />
                </div>
              </div>
            </div>

            <div class="form-section">
              <div class="section-title">Precificação:</div>
              <div class="table-row">
                <div class="column-25">
                  <label class="labels" for="hours">Tempo de trabalho</label>
                </div>
                <div class="col">
                  <label class="labels">horas</label>
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
                  <label class="labels">minutos</label>
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
                <div class="column-15">
                  <label class="labels" for="labor_hourly_rate_minutes">
                    Valor da hora
                  </label>
                </div>
                <div class="col">
                  <input
                    class="form-control text-end"
                    type="text"
                    name="labor_hourly_rate"
                    v-model="form.labor_hourly_rate"
                    v-mask-decimal.br="2"
                  />
                </div>
              </div>

              <div class="table-row">
                <div class="column-25">
                  <label class="labels" for="profit_percentage">
                    Margem de lucro
                  </label>
                </div>
                <div class="column-10">
                  <label class="labels" for="profit_percentage"> % </label>
                </div>
                <div class="price-column">
                  <input
                    class="form-control text-end"
                    type="text"
                    name="profit_percentage"
                    v-model="form.profit_percentage"
                    @input="onProfitInput"
                  />
                </div>
                <div class="column-5">
                  <label class="labels" for="profit"> R$ </label>
                </div>
                <div class="price-column">
                  <input
                    class="form-control text-end"
                    type="text"
                    name="profit"
                    v-model="form.profit"
                    v-mask-decimal.br="2"
                  />
                </div>
              </div>
              <div class="table-row">
                <div class="column-80">
                  <label class="labels" for="price"> Preço final </label>
                </div>
                <div class="price-column">
                  <input
                    class="form-control text-end"
                    type="text"
                    name="price"
                    v-model="form.price"
                    v-mask-decimal.br="2"
                    disabled
                  />
                </div>
              </div>
            </div>

            <div v-if="costs.length === 0" class="table-row">
              <p>Você ainda não possui custos cadastrados.</p>
            </div>
            <div v-else class="form-section">
              <div class="section-title">Custos:</div>
              <div class="table-row" v-for="cost in costs" :key="cost.id">
                <div class="column-40">
                  <label :for="cost.id">
                    {{ cost.name }}
                  </label>
                </div>
                <div class="column-10">
                  <input
                    type="number"
                    min="0"
                    :id="cost.id"
                    v-model.number="cost.quantity"
                    placeholder="0"
                    class=""
                    @input="updateTotalPrice(cost)"
                  />
                </div>
                <div class="column-10">
                  <p class="price">R$ {{ cost.price }}</p>
                </div>
                <div class="column-10">
                  <p
                    class="total-price"
                    :class="{ 'price-active': cost.total_price > 0 }"
                  >
                    R$ {{ cost.total_price }}
                  </p>
                </div>
              </div>
            </div>

            <div class="table-row">
              <button
                type="button"
                class="btn btn-secondary"
                @click="closeModal"
              >
                Fechar
              </button>
              <button type="submit" class="button button-new">Criar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { index, submitFormCreate } from "@/utils/requests/httpUtils";
import TextInput from "./inputs/text/TextInput";

export default {
  name: "ServiceCreateForm",
  emits: ["new-service-event"],
  data() {
    return {
      allStatus: [],
      costs: [],
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
      message: null,
      userInput: false,
    };
  },
  components: {
    TextInput,
  },
  methods: {
    index,
    submitFormCreate,
    calculateProfit() {
      let formattedLaborHours = this.form.hours + this.form.minutes / 60;
      let laborHourlyRate = this.form.labor_hourly_rate;
      if (typeof laborHourlyRate === "string") {
        laborHourlyRate = laborHourlyRate.replace(",", ".");
      }
      let formattedLaborHourlyRate = parseFloat(laborHourlyRate);
      let operationalCost = formattedLaborHourlyRate * formattedLaborHours;

      // Calculate third party cost
      let thirdPartyCost = this.costs.reduce(
        (sum, cost) => sum + parseFloat(cost.total_price.replace(",", ".")),
        0
      );

      let totalCost = operationalCost + thirdPartyCost;
      let profitPercentage = this.form.profit_percentage / 100;
      let priceWithoutProfit = totalCost / (1 - profitPercentage);
      this.form.profit = (priceWithoutProfit - totalCost)
        .toFixed(2)
        .replace(".", ",");

      // this.calculatePrice();
    },
    calculatePrice() {
      let laborHourlyRate = this.form.labor_hourly_rate;
      if (typeof laborHourlyRate === "string") {
        laborHourlyRate = laborHourlyRate.replace(",", ".");
      }
      let operationalCost =
        parseFloat(laborHourlyRate) *
        (this.form.hours + this.form.minutes / 60);
      operationalCost = parseFloat(operationalCost.toFixed(2));

      this.form.labor_hourly_total = operationalCost;

      let profit = this.form.profit;
      if (typeof profit === "string") {
        profit = profit.replace(",", ".");
      }
      profit = parseFloat(profit);

      this.updateFinalPrice();
    },
    clearForm() {
      this.form = {
        labor_hourly_rate: "",
        hours: 0,
        minutes: 0,
        profit: "",
        price: 0,
        labor_hourly_total: 0,
        labor_hours: 0,
        costs: [],
      };
      this.costs = [];
      this.errors = null;
    },
    closeModal() {
      this.isModalVisible = false;
    },
    async getCosts() {
      this.costs = await this.index("costs");
      this.costs.forEach((cost) => {
        cost.total_price = "0,00";
      });
    },
    handleKeydown(event) {
      if (event.key === "Escape" || event.key === "Esc") {
        this.closeModal();
      }
    },
    async submitForm() {
      this.form.labor_hours = this.form.hours * 3600 + this.form.minutes * 60;
      this.form.labor_hourly_rate = parseFloat(
        this.form.labor_hourly_rate.replace(",", ".")
      );
      this.form.profit = parseFloat(this.form.profit.replace(",", "."));
      this.form.price = parseFloat(this.form.price.replace(",", "."));

      this.form.costs = this.costs
        .filter((cost) => cost.quantity > 0)
        .map((cost) => ({
          id: cost.id,
          quantity: cost.quantity,
          price: cost.price,
          total_price: cost.total_price,
        }));

      const { data, error } = await this.submitFormCreate(
        "services",
        this.form
      );

      if (data) {
        this.isModalVisible = false;
        this.$emit("new-service-event", data);
        this.clearForm();
      }
      if (error) {
        this.errors = error;
      }
    },
    onProfitPercentageInput() {
      this.userInput = true;
      this.calculateProfit();
      this.userInput = false;
    },
    openModal() {
      this.isModalVisible = true;
    },
    updateTotalPrice(cost) {
      if (cost.quantity > 0) {
        cost.total_price = (cost.quantity * cost.price).toFixed(2);
      } else {
        cost.total_price = "0,00";
      }
      this.updateFinalPrice();
    },
    updateFinalPrice() {
      // let totalCost = this.costs.reduce(
      //   (sum, cost) => sum + parseFloat(cost.total_price.replace(",", ".")),
      //   0
      // );

      let profit = this.form.profit;
      if (typeof profit === "string") {
        profit = profit.replace(",", ".");
      }
      profit = parseFloat(profit);

      this.form.price = (
        // totalCost +
        parseFloat(this.form.labor_hourly_total || 0) +
        profit
      ).toFixed(2);

      this.updateProfitPercentage();
    },
    updateProfitPercentage() {
      let profit = this.form.profit;
      if (typeof profit !== "string") {
        profit = profit.toString();
      }
      profit = profit.replace(",", ".");
      profit = parseFloat(profit);

      let price = this.form.price;
      if (typeof price !== "string") {
        price = price.toString();
      }
      price = price.replace(",", ".");
      price = parseFloat(price);

      console.log(`Profit: ${profit}, Price: ${price}`);
      if (price > 0) {
        this.form.profit_percentage = ((profit / price) * 100).toFixed(2);
      } else {
        this.form.profit_percentage = "0";
      }
      console.log(`Profit Percentage: ${this.form.profit_percentage}`);
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
    isModalVisible(newVal) {
      if (newVal) {
        this.getCosts();
      }
    },
    "form.labor_hourly_rate": "calculatePrice",
    "form.hours": "calculatePrice",
    "form.minutes": "calculatePrice",
    "form.profit": function (newVal) {
      this.userInput = false;
      this.form.profit = newVal;
      this.calculatePrice();
    },
  },
  mounted() {
    document.addEventListener("keydown", this.handleKeydown);
  },
  beforeUnmount() {
    document.removeEventListener("keydown", this.handleKeydown);
  },
};
</script>

<style scoped>
.quantity-column {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-basis: 5%;
  text-align: right;
  margin-right: 2rem;
}

.price {
  text-align: right;
}

.total-price {
  text-align: right;
}

.price-active {
  font-weight: bold;
  color: var(--primary);
}

.title-column {
  display: flex;
  align-items: left;
  justify-content: left;
  flex-basis: 45%;
}
</style>