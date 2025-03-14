<template>
    <div class="section-container">
        <div class="subtitle-container">
            <font-awesome-icon icon="fas fa-dollar" class="icon" />
            <h2>
                Custos e margem de lucro
            </h2>
        </div>
        <div class="table-row">
            <div class="icon-column">
                <font-awesome-icon icon="fa fa-clock" />
            </div>
            <div class="label-column-extra">
                Custo operacional
            </div>
            <div class="extra-column">
                <hours-decimal-editable-field name="total_hours" v-model="localTotalHours"
                    placeholder="quantidade total de horas" @save="updateProposal('total_hours', $event)" />
                h
            </div>
            <div class="price-colum">
                <money-field name="total_operational_cost" v-model="localTotalOperationalCost" />
            </div>
        </div>

        <div class="table-row" :class="{ 'highlight': isHighlighted }">
            <div class="icon-column">
                <font-awesome-icon icon="fa fa-clock" />
            </div>
            <div class="label-column">
                Custos de propdução
            </div>
            <div class="price-column">
                <money-field name="total_third_party_cost" v-model="localTotalThirdPartyCost" />
            </div>
        </div>

        <div class="table-row">
            <div class="icon-column">
                <font-awesome-icon icon="fas fa-percent" />
            </div>
            <div class="label-column-extra">
                Lucro:
            </div>
            <div class="price-column">
                <decimal-editable-field name="total_profit_percentage" v-model="localTotalProfitPercentage"
                    placeholder="percentual do lucro" @save="updateProposal('total_profit_percentage', $event)" />
                %
            </div>
            <div class="price-column">
                <money-field name="total_profit" v-model="localTotalProfit" />
            </div>
        </div>
        <div class="table-row">
            <div class="icon-column">
                <font-awesome-icon icon="fas fa-dollar-sign" />
            </div>
            <div class="label-column">
                Preço:
            </div>
            <div class="price-column">
                <money-field name="price" v-model="localTotalPrice" />
            </div>
        </div>
        <div class="table-row">
            <div class="icon-column">
                <font-awesome-icon icon="fas fa-credit-card" />
            </div>
            <div class="label-column">
                Parcelamento:
            </div>
            <div class="extra-column">
                <integer-editable-field v-model="localInstallmentQuantity"
                    @save="updateProposal('installment_quantity', $event)" placeholder="quantidade de parcelas" />
            </div>
        </div>
        <div class="table-row">
            <div class="icon-column">
                <font-awesome-icon icon="fa fa-calendar-alt" />
            </div>
            <div class="label-column">
                Data de criação:
            </div>
            <div class="extra-column">
                {{ formatDateBr(proposal.created_at) }}
            </div>
        </div>
    </div>
</template>

<script>
import { formatDateBr } from "@/utils/date/dateUtils";
import DecimalEditableField from "@/components/fields/number/DecimalEditableField";
import IntegerEditableField from "@/components/fields/number/IntegerEditableField.vue";
import HoursDecimalEditableField from '../../components/fields/number/HoursDecimalEditableField.vue';
import MoneyField from "@/components/fields/number/MoneyField.vue";

export default {
    props: {
        proposal: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            isHighlighted: false,
            localInstallmentQuantity: this.proposal.installment_quantity,
            localTotalHours: this.proposal.total_hours,
            localTotalOperationalCost: this.proposal.total_operational_cost,
            localTotalPrice: this.proposal.total_price,
            localTotalProfitPercentage: this.proposal.total_profit_percentage,
            localTotalThirdPartyCost: this.proposal.total_third_party_cost,
            localTotalProfit: this.proposal.total_profit,
        }
    },
    components: {
        DecimalEditableField,
        IntegerEditableField,
        HoursDecimalEditableField,
        MoneyField,
    },
    methods: {
        formatDateBr,
        highlight() {
            this.isHighlighted = true;
            setTimeout(() => {
                this.isHighlighted = false;
            }, 2000);
        },
        updateProposal(field, value) {
            this.$emit("update-proposal", field, value);
        },
    },
    watch: {
        proposal: {
            handler(newProposal) {
                this.localTotalHours = newProposal.total_hours;
                this.localTotalOperationalCost = newProposal.total_operational_cost;
                this.localTotalThirdPartyCost = newProposal.total_third_party_cost;
                this.localProfit = newProposal.profit;
                this.localTotalPrice = newProposal.total_price;
                this.localTotalProfitPercentage = newProposal.total_profit_percentage;
                this.localInstallmentQuantity = newProposal.installment_quantity;
            },
            deep: true,
        },
        'proposal.total_third_party_cost'(newVal) {
            this.localTotalThirdPartyCost = newVal;
            this.highlight();
        },
    },
};

</script>

<style scoped>
.icon-column {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin: 1rem;
    flex-basis: 0%;
}

.label-column {
    display: flex;
    align-items: left;
    justify-content: left;
    margin: 1rem;
    flex-basis: 85%;
}

.label-column-extra {
    display: flex;
    align-items: left;
    justify-content: left;
    margin: 1rem;
    flex-basis: 75%;
}

.extra-column {
    display: flex;
    align-items: center;
    justify-content: left;
    margin: 1rem;
    flex-basis: 10%;
}

.price-column {
    display: flex;
    align-items: center;
    justify-content: right;
    flex-basis: 15%;
}
</style>