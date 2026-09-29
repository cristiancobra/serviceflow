<template>
  <div role="alert" class="alert alert-success alert-soft mb-5 font-semibold">
    <font-awesome-icon icon="fa-solid fa-triangle-exclamation" class="text-4xl" />
    <span>{{ formResponse }}</span>
  </div>
</template>
    
    <script>
export default {
  props: ["formResponse"],
};
</script>
