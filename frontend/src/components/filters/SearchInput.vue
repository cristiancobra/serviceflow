<template>
    <div class="search-container">
        <input 
            type="text" 
            class="search-input" 
            :value="modelValue" 
            :placeholder="placeholder" 
            @input="$emit('update:modelValue', $event.target.value)"
        />
    </div>
</template>

<script>
export default {
    name: 'SearchInput',
    emits: ['update:modelValue'],
    props: {
        modelValue: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: 'Digite para buscar'
        }
    }
}
</script>

<style scoped>
.search-container {
    margin-bottom: 2rem;
    margin-top: 2rem;
}

.search-input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1rem;
}

.search-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>