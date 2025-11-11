<template>
    <div class="row ms-5 me-5 ps-5 pe-5 mt-5 filters-container">
      <div class="col">
        <button
          class="button pending"
          @click="handleButtonClick('pending', 'emitFilterPending')"
          :class="{ active: activeButton === 'pending' }"
        >
          pendentes
        </button>
      </div>
      <div class="col">
        <button
          class="button overdue"
          @click="handleButtonClick('overdue', 'emitFilterOverdue')"
          :class="{ active: activeButton === 'overdue' }"
        >
          vencidas
        </button>
      </div>
      <div class="col">
        <button
          class="button paid"
          @click="handleButtonClick('paid', 'emitFilterPaid')"
          :class="{ active: activeButton === 'paid' }"
        >
          pagas
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
          class="button all"
          @click="handleButtonClick('all', 'emitFilterAll')"
          :class="{ active: activeButton === 'all' }"
        >
          todas
        </button>
      </div>
    </div>
</template>

<script>
export default {
  name: "InvoicesFilter",
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
      this.activeButton = button;
      this[emitEvent]();
    },
    emitFilterPending() {
      this.$emit("filter-pending");
    },
    emitFilterPaid() {
      this.$emit("filter-paid");
    },
    emitFilterOverdue() {
      this.$emit("filter-overdue");
    },
    emitFilterCanceled() {
      this.$emit("filter-canceled");
    },
    emitFilterAll() {
      this.$emit("filter-all");
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
}

.button {
  border-width: 2px;
  border-style: solid;
  border-radius: 20px;
  padding: 10px 15px;
  margin: 0 4px;
  font-weight: 800;
  width: 120px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.pending {
  background-color: white;
  border-color: var(--orange);
  color: var(--orange);
}
.pending:hover {
  background-color: var(--orange);
  color: white;
}
.pending.active {
  background-color: var(--orange);
  color: white;
}

.overdue {
  background-color: white;
  border-color: #b1388d;
  color: #b1388d;
}
.overdue:hover {
  background-color: #b1388d;
  color: white;
}
.overdue.active {
  background-color: #b1388d;
  color: white;
}

.paid {
  background-color: white;
  border-color: var(--green);
  color: var(--green);
}
.paid:hover {
  background-color: var(--green);
  color: white;
}
.paid.active {
  background-color: var(--green);
  color: white;
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

.all {
  background-color: white;
  border-color: var(--blue);
  color: var(--blue);
}
.all:hover {
  background-color: var(--blue);
  color: white;
}
.all.active {
  background-color: var(--blue);
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