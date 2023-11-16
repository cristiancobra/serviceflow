<template>
  <div>
    <div class="row mt-5 filters-container">
      <button
        class="col slot doing"
        @click="handleButtonClick('doing', 'emitFilterToDo')"
        :class="{ active: activeButton === 'doing' }"
      >
        fazer
      </button>
      <button
        class="col slot late"
        @click="handleButtonClick('late', 'emitFilterLate')"
        :class="{ active: activeButton === 'late' }"
      >
        atrasadas
      </button>
      <button
        class="col slot canceled"
        @click="handleButtonClick('canceled', 'emitFilterCanceled')"
        :class="{ active: activeButton === 'canceled' }"
      >
        canceladas
      </button>
      <button
        class="col slot done"
        @click="handleButtonClick('done', 'emitFilterDone')"
        :class="{ active: activeButton === 'done' }"
      >
        feitas
      </button>
      <button class="col slot button-new" @click="toggle()">+</button>
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
      activeButton: null,
      data: null,
    };
  },
  methods: {
    handleButtonClick(button, emitEvent) {
      this.activeButton = button; // Ative o botão
      this[emitEvent](); // Emita o evento correspondente
    },
    emitFilterDone() {
      this.$emit("filter-done"); // Emitindo o evento para o componente pai
    },
    emitFilterToDo() {
      this.$emit("filter-to-do"); // Emitindo o evento para o componente pai
    },
    emitFilterCanceled() {
      this.$emit("filter-canceled"); // Emitindo o evento para o componente pai
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
  margin: 0 6px 0 6px;
  color: white;
  font-weight: 800;
  width: 120px;
}
.canceled {
  background-color: white;
  border-color: var(--gray);
  color: var(--gray);
}
.canceled:hover {
  background-color: var(--gray);
  color: white;
}
.canceled.active {
  background-color: var(--gray);
  color: white;
}
.done {
  background-color: white;
  border-color: var(--green);
  color: var(--green);
}
.done:hover {
  background-color: var(--green);
  color: white;
}
.done.active {
  background-color: var(--green);
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
.doing.active {
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
.late.active {
  background-color: #b1388d;
  color: white;
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
