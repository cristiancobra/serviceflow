<template>
  <div class="section-container">
    <div class="section-title">
      <font-awesome-icon icon="fas fa-coins" class="icon" />
      <h2>Descrição</h2>
    </div>
    <div v-if="description" class="table-row">
      <p>
        {{ description }}
      </p>
    </div>
    <div v-else class="table-row">
      <p>Sem descrição</p>
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