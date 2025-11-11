<template>
  <div class="section-container">
    <div class="section-title">
      <font-awesome-icon icon="fas fa-file-invoice" class="icon" />
      <h2>Itens da proposta</h2>
    </div>
    <div class="flex w-full" v-for="service in services" v-bind:key="service.id">
      <div class="w-1/12 flex justify-center items-center ps-5">
        <font-awesome-icon icon="fa-solid fa-coins" class="primary text-black" />
      </div>
      <router-link
        class="no-link w-6/12 flex items-center"
        :to="{ name: 'serviceShow', params: { id: service.service_id } }"
        :key="service.service_id"
      >
        <div class="text-black">
            {{ service.name }}
        </div>
      </router-link>
      <div class="w-2/12 flex items-center justify-center">
        <integer-editable-field
          v-model="service.quantity"
          @save="emitUpdateProposal('quantity', service.service_id, $event)"
        />
        <span class="text-black font-bold ps-1">x</span>
      </div>
      <div class="w-2/12 flex items-center justify-end text-black">
        <money-field name="price" v-model="service.price" />
      </div>
      <div class="w-2/12 flex items-center justify-end font-bold text-black">
        <money-field name="total_price" v-model="service.total_price" />
      </div>
    </div>
  </div>
</template>

<script>
import MoneyField from "@/components/fields/number/MoneyField.vue";
import IntegerEditableField from "../fields/number/IntegerEditableField.vue";

export default {
  props: {
    services: {
      type: Array,
      required: false,
    },
  },
  components: {
    MoneyField,
    IntegerEditableField,
  },
  methods: {
    async emitUpdateProposal(fieldName, serviceId, editedValue) {
      this.$emit("update-proposal", fieldName, serviceId, editedValue);
    },
  },
};
</script>

<style scoped>
.icon-column {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  margin: 1rem;
  flex-basis: 0%;
}

.integer-column {
  display: flex;
  align-items: center;
  justify-content: center;
  /* margin: 1rem; */
  flex-basis: 10%;
}

.price-column {
  display: flex;
  align-items: center;
  justify-content: right;
  /* margin: 1rem; */
  flex-basis: 10%;
}

.total-price-column {
  display: flex;
  align-items: center;
  justify-content: right;
  /* margin: 1rem; */
  flex-basis: 15%;
  font-weight: 800;
}

.title-column {
  display: flex;
  align-items: left;
  justify-content: left;
  /* margin: 1rem; */
  flex-basis: 70%;
}
</style>