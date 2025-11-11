<template>
  <div class="dropdown">
    <button
      class="button-status"
      :class="modelValue?.value"
      @click="isOpen = !isOpen"
    >
      {{ modelValue?.label || "definir situação" }}
    </button>
    <ul v-if="isOpen" class="dropdown-menu">
      <li v-for="item in items" :key="item.value" @click="selectStatus(item)">
        <p class="menu-item" :class="item.value">
          {{ item.label }}
        </p>
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
        { value: "draft", label: "rascunho" },
        { value: "submitted", label: "enviada" },
        { value: "accepted", label: "aprovada" },
        { value: "rejected", label: "rejeitada" },
        { value: "canceled", label: "cancelada" },
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
      this.$emit("update:modelValue", status.value);
    },
  },
  watch: {
    status(newStatus) {
      this.modelValue = this.items.find((item) => item.value === newStatus);
    },
  },
  mounted() {
    this.modelValue = this.items.find((item) => item.value === this.status);
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
  font-size: 0.8rem;
  background-color: #f9f9f9;
  border-style: solid;
  border-color: var(--purple-light);
  border-width: 1px;
  border-radius: 4%;
  min-width: 140px;
  margin-left: 0rem;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
  padding: 1rem;
  z-index: 1;
}

.button-status {
    border-width: 2px;
    border-style: solid;
    border-radius: 20px 20px 20px 20px;
    padding: 0.2rem;
    padding-left: 0.4rem;
    padding-right: 0.4rem;
    width: 6rem;
    font-weight: 800;
    font-size: 0.8rem;
    text-align: center;
    border-color: var(--purple-light);
    background-color: var(--purple);
    color: white;
}

.button-status.accepted {
    border-color: var(--green-light);
    background-color: var(--green);
    color: white;
}

.button-status.canceled {
    border-color: var(--canceled-color);
    background-color: var(--canceled-color);
    color: white;
}

.button-status.draft {
    border-color: black;
    background-color: white;
    color: black;
}

.button-status.rejected {
    border-color: var(--red-light);
    background-color: var(--red);
    color: white;
}

.menu-item {
  border-width: 2px;
  border-style: solid;
  border-radius: 15px;
  padding: 0.3rem 0.8rem;
  margin: 0.2rem 0;
  font-weight: 600;
  font-size: 0.8rem;
  text-align: center;
  cursor: pointer;
  transition: opacity 0.2s;
  border-color: var(--purple-light);
  background-color: var(--purple);
  color: white;
}

.menu-item:hover {
  opacity: 0.8;
}

.menu-item.accepted {
  border-color: var(--green-light);
  background-color: var(--green);
  color: white;
}

.menu-item.canceled {
  border-color: var(--canceled-color);
  background-color: var(--canceled-color);
  color: white;
}

.menu-item.draft {
  border-color: black;
  background-color: white;
  color: black;
}

.menu-item.rejected {
  border-color: var(--red-light);
  background-color: var(--red);
  color: white;
}

.menu-item.submitted {
  border-color: var(--purple-light);
  background-color: var(--purple);
  color: white;
}
</style>