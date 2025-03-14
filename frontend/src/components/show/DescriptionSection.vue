<template>
    <div class="section-container">
        <div class="column-1">
            <div class="subtitle-container">
                <font-awesome-icon icon="fas fa-coins" class="icon" />
                <h2>
                    Descrição
                </h2>
            </div>
            <div v-if="description" class="row">
                <p>
                    {{ description }}
                </p>
            </div>
            <div v-else class="row">
                <p>
                    Sem descrição
                </p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        description: {
            type: String,
            required: false,
        },
    },
};
</script>

<style scoped>
/* Estilos específicos para DescriptionSection */
</style>