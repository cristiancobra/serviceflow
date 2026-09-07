<template>
  <div>
    <label v-if="label" class="form-label" :for="name">{{ label }}</label>
    <div class="input-money-wrapper">
      <span v-if="showCurrencySymbol" class="input-money-symbol">R$</span>
      <input
        ref="inputEl"
        class="form-control input-money"
        :class="{ 'has-symbol': showCurrencySymbol }"
        type="text"
        inputmode="decimal"
        :id="name"
        :name="name"
        :placeholder="placeholder"
        :disabled="disabled"
        :value="displayValue"
        @keydown="onKeydown"
        @paste="onPaste"
        @focus="onFocus"
        v-bind="$attrs"
      />
    </div>
  </div>
</template>

<script>
const CONTROL_KEYS = [
  "Tab", "Escape", "Enter", "ArrowLeft", "ArrowRight", "ArrowUp", "ArrowDown",
  "Home", "End", "Shift", "Control", "Alt", "Meta", "CapsLock", "F5",
];
const MAX_DIGITS = 13; // até R$ 99.999.999.999,99

export default {
  inheritAttrs: false,
  props: {
    name: String,
    label: String,
    placeholder: {
      type: String,
      default: "0,00",
    },
    disabled: Boolean,
    showCurrencySymbol: {
      type: Boolean,
      default: false,
    },
    allowNegative: {
      type: Boolean,
      default: false,
    },
    modelValue: [String, Number],
  },
  emits: ["update:modelValue"],
  data() {
    const { digits, negative } = this.toDigits(this.modelValue);
    return {
      digits,
      negative,
    };
  },
  computed: {
    displayValue() {
      if (this.digits === "") {
        return "";
      }
      const value = parseInt(this.digits, 10) / 100;
      const formatted = value.toLocaleString("pt-BR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
      return this.negative ? `-${formatted}` : formatted;
    },
  },
  methods: {
    toDigits(value) {
      if (value === null || value === undefined || value === "") {
        return { digits: "", negative: false };
      }
      const num = typeof value === "string" ? parseFloat(value.replace(",", ".")) : value;
      if (isNaN(num)) {
        return { digits: "", negative: false };
      }
      const cents = Math.round(Math.abs(num) * 100);
      return { digits: String(cents), negative: num < 0 };
    },
    appendDigit(digit) {
      if (this.digits.length >= MAX_DIGITS) {
        return;
      }
      this.digits = (this.digits + digit).replace(/^0+(?=\d)/, "");
      this.emitValue();
    },
    removeLastDigit() {
      this.digits = this.digits.slice(0, -1);
      if (this.digits === "") {
        this.negative = false;
      }
      this.emitValue();
    },
    clear() {
      this.digits = "";
      this.negative = false;
      this.emitValue();
    },
    toggleNegative() {
      if (!this.allowNegative) {
        return;
      }
      this.negative = !this.negative;
      this.emitValue();
    },
    emitValue() {
      const numeric = this.digits === ""
        ? null
        : (this.negative ? -1 : 1) * (parseInt(this.digits, 10) / 100);
      this.$emit("update:modelValue", numeric);
    },
    moveCursorToEnd() {
      const el = this.$refs.inputEl;
      if (el) {
        const length = el.value.length;
        el.setSelectionRange(length, length);
      }
    },
    onFocus() {
      this.$nextTick(this.moveCursorToEnd);
    },
    onKeydown(event) {
      if (this.disabled) {
        return;
      }
      if (event.ctrlKey || event.metaKey || CONTROL_KEYS.includes(event.key)) {
        return;
      }
      if (event.key >= "0" && event.key <= "9") {
        event.preventDefault();
        this.appendDigit(event.key);
        this.$nextTick(this.moveCursorToEnd);
        return;
      }
      if (event.key === "Backspace") {
        event.preventDefault();
        this.removeLastDigit();
        this.$nextTick(this.moveCursorToEnd);
        return;
      }
      if (event.key === "Delete") {
        event.preventDefault();
        this.clear();
        return;
      }
      if (event.key === "-") {
        event.preventDefault();
        this.toggleNegative();
        return;
      }
      event.preventDefault();
    },
    onPaste(event) {
      if (this.disabled) {
        return;
      }
      event.preventDefault();
      const text = event.clipboardData.getData("text");
      const onlyDigits = text.replace(/\D/g, "");
      if (!onlyDigits) {
        return;
      }
      this.digits = (this.digits + onlyDigits).slice(-MAX_DIGITS).replace(/^0+(?=\d)/, "");
      if (this.allowNegative && text.trim().startsWith("-")) {
        this.negative = true;
      }
      this.emitValue();
      this.$nextTick(this.moveCursorToEnd);
    },
  },
  watch: {
    modelValue(newValue) {
      const { digits, negative } = this.toDigits(newValue);
      if (digits !== this.digits || negative !== this.negative) {
        this.digits = digits;
        this.negative = negative;
      }
    },
  },
};
</script>

<style scoped>
.input-money-wrapper {
  position: relative;
}

.input-money-symbol {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #6b7280;
  font-weight: 500;
  pointer-events: none;
}

.input-money {
  text-align: right;
  width: 100%;
}

.input-money.has-symbol {
  padding-left: 2.5rem;
}
</style>
