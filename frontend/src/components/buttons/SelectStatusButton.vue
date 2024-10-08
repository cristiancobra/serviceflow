<template>
    <div class="dropdown">
        <button class="button-status" :class="modelValue?.value" @click="isOpen = !isOpen">
            {{ modelValue?.label || 'definir situação' }}
        </button>
        <ul v-if="isOpen" class="dropdown-menu">
            <li v-for="item in items" :key="item.value" @click="selectStatus(item)">
                {{ item.label }}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isOpen: false,
            modelValue: null,
            items: [
                { value: 'draft', label: 'rascunho' },
                { value: 'submitted', label: 'enviada' },
                { value: 'accepted', label: 'aprovada' },
                { value: 'rejected', label: 'rejeitada' },
                { value: 'canceled', label: 'cancelada' }
            ],
        };
    },
    props: {
        status: {
            type: String,
            required: true,
        },
    },
    methods: {
        selectStatus(status) {
            this.modelValue = status;
            this.isOpen = false;
            this.$emit('update:modelValue', status.value);
        },
    },
    watch: {
        status(newStatus) {
            this.modelValue = this.items.find(item => item.value === newStatus);
        },
    },
    mounted() {
        this.modelValue = this.items.find(item => item.value === this.status);
    },
};
</script>

<style scoped>
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-menu {
    display: block;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    padding: 12px 16px;
    z-index: 1;
}
</style>