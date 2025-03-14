<template>
    <div class="section-container">
        <div class="subtitle-container">
            <font-awesome-icon icon="fas fa-file-invoice" class="icon" />
            <h2>
                Itens da proposta
            </h2>
        </div>
        <div class="table-row" v-for="service in services" v-bind:key="service.id">
            <div class="icon-column">
                <font-awesome-icon icon="fa-solid fa-coins" class="primary" />
            </div>
            <div class="title-column">
                <p class="name">
                    {{ service.name }}
                </p>
            </div>
            <div class="integer-column">
                {{ service.quantity }} x
            </div>
            <div class="price-column">
                <money-field name="price" v-model="service.price" />
            </div>
            <div class="total-price-column">
                <money-field name="total_price" v-model="service.total_price" />
            </div>
        </div>
    </div>
</template>

<script>
import MoneyField from "@/components/fields/number/MoneyField.vue";

export default {
    props: {
        services: {
            type: Array,
            required: false,
        },
    },
    components: {
        MoneyField,
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
    flex-basis: 5%;
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