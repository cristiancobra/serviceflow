<template>
  <div class="container row">
    <div class="col-1">
      <p class="icon">
        <font-awesome-icon icon="fa-solid fa-triangle-exclamation" />
      </p>
    </div>
    <div class="col">
      {{ formResponse }}
    </div>
  </div>
</template>
    
    <script>
export default {
  props: ["formResponse"],
};
</script>
    
    <style scoped>
li {
  margin-bottom: -16px;
}
.container {
  text-align: left;
  font-weight: 600;
  color: var(--green);
  border-style: solid;
  border-width: 2px;
  border-color: var(--green);
  border-radius: 6px;
  padding: 16px;
  background-color: var(--green-light);
}
.icon {
  font-size: 42px;
  text-align: center;
}
</style>