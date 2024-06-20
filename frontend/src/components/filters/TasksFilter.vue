<template>
    <div class="row ms-5 me-5 ps-5 pe-5 mt-5 filters-container">
      <div class="col">
        <button
          class="button doing"
          @click="handleButtonClick('doing', 'emitFilterDoing')"
          :class="{ active: activeButton === 'doing' }"
        >
          fazendo
        </button>
      </div>
      <div class="col">
        <button
          class="button to-do"
          @click="handleButtonClick('to-do', 'emitFilterTodo')"
          :class="{ active: activeButton === 'to-do' }"
        >
          fazer
        </button>
      </div>
      <div class="col">
        <button
          class="button late"
          @click="handleButtonClick('late', 'emitFilterLate')"
          :class="{ active: activeButton === 'late' }"
        >
          atrasadas
        </button>
      </div>
      <div class="col">
        <button
          class="button canceled"
          @click="handleButtonClick('canceled', 'emitFilterCanceled')"
          :class="{ active: activeButton === 'canceled' }"
        >
          canceladas
        </button>
      </div>
      <div class="col">
        <button
          class="button done"
          @click="handleButtonClick('done', 'emitFilterDone')"
          :class="{ active: activeButton === 'done' }"
        >
          feitas
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
      activeButton: null,
      data: null,
    };
  },
  methods: {
    handleButtonClick(button, emitEvent) {
      this.activeButton = button; // Ative o botão
      this[emitEvent](); // Emita o evento correspondente
    },
    emitFilterCanceled() {
      this.$emit("filter-canceled"); // Emitindo o evento para o componente pai
    },
    emitFilterDoing() {
      this.$emit("filter-doing"); // Emitindo o evento para o componente pai
    },
    emitFilterDone() {
      this.$emit("filter-done"); // Emitindo o evento para o componente pai
    },
    emitFilterLate() {
      this.$emit("filter-late"); // Emitindo o evento para o componente pai
    },
    emitFilterTodo() {
      this.$emit("filter-to-do"); // Emitindo o evento para o componente pai
    },
  },
};
</script>

<style scoped>
.filters-container {
  margin-top: 40px;
  margin-bottom: 50px;
  display: flex;
  justify-content: center;
  /* width: 100%; */
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
  border-color: var(--blue);
  color: var(--blue);
}
.doing:hover {
  background-color: var(--blue);
  color: white;
}
.doing.active {
  background-color: var(--blue);
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
.to-do {
  background-color: white;
  border-color: var(--orange);
  color: var(--orange);
}
.to-do:hover {
  background-color: var(--orange);
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
