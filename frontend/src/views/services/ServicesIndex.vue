<template>
  <div class="container mb-5">
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