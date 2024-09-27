<template>
  <div class="container mt-5 mb-5">
    <ServicesList />
  </div>
</template>

<script>
import ServicesList from "@/components/lists/ServicesList.vue";

export default {
  name: "servicesIndex",
  components: {
    ServicesList,
  },
};
</script>