<template>
    <div>
      <div class="row mt-5 filters-container">
        <button class="col slot doing" @click="emitFilterToDo">
          fazer
        </button>
        <button class="col slot late">
          atrasadas
        </button>
        <button class="col slot done" @click="emitFilterDone">
          feito
        </button>
        <button class="col slot new" @click="toggle()">
          +
        </button>
      </div>

    </div>
  </template>

<script>
// import axios from "axios";

export default {
  name: "TasksFilter",
  data() {
    return {
      isActive: true,
      hasError: false,
      data: null,
    };
  },
  methods: {
    emitFilterDone() {
      this.$emit('filter-done'); // Emitindo o evento para o componente pai
    },
    emitFilterToDo() {
      this.$emit('filter-to-do'); // Emitindo o evento para o componente pai
    },
    toggle() {
      this.$emit("toggle"); // Emitir evento para o componente pai
    },
  },
};
</script>

<style scoped>
.filters-container {
  margin-top: 40px;
  margin-bottom: 50px;
  margin-left: 25%;
  margin-right: 25%;
  display: flex;
  justify-content: center;
}
.slot {
  border-width: 2px;
  border-style: solid;
  border-color: white;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  margin: 0 4px 0 4px;
  color: white;
  font-weight: 800;
  width: 120px;
}
.done {
  background-color: white;
  border-color: #2cb48d;
  color: #2cb48d;
}
.done:hover {
  background-color: #2cb48d;
  color: white;
}
.doing {
  background-color: white;
  border-color: #e78d1f;
  color: #e78d1f;
}
.doing:hover {
  background-color: #e78d1f;
  color: white;
}
.late {
  background-color: white;
  border-color: #b1388d;
  color: #b1388d;
}
.late:hover {
  background-color: #b1388d;
  color: white;
}
.new {
  border-radius: 20px 20px 20px 20px;
  background-color: white;
  border-color: #ff3eb5;
  color: #ff3eb5;
  margin-left: 50px;
  font-size: 16px;
}
.new:hover {
  background-color: #ff3eb5;
  color: white;
  margin-left: 50px;
}
.hidden {
  display: none;
  transition: display 2s;
}
.show {
  display: block;
  transition: display 2s;
}
</style>
