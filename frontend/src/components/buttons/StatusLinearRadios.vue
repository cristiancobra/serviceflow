<template>
  <div id="form-wrapper">
    <div id="debt-amount-slider">

      <input
      type="radio"
      name="status"
      id="wait"
      value="wait"
      v-model="localForm.status"
      @change="emitStatusChange"
      required
      >
      <label for="wait" data-status="PENDENTE"></label>
      
      <input
      type="radio"
      name="status"
      id="to-do"
      value="to-do"
      v-model="localForm.status"
      @change="emitStatusChange"
      required
      >
      <label for="to-do" data-status="FAZER"></label>

      <input
      type="radio"
      name="status"
      id="doing"
      value="doing"
      v-model="localForm.status"
      @change="emitStatusChange"
      required
      >
      <label for="doing" data-status="FAZENDO"></label>

      <input
      type="radio"
      name="status"
      id="done"
      value="done"
      v-model="localForm.status"
      @change="emitStatusChange"
      required
      >
      <label for="done" data-status="FEITO"></label>

      <input
      type="radio"
      name="status"
      id="canceled"
      value="canceled"
      v-model="localForm.status"
      @change="emitStatusChange"
      required
      >
      <label for="canceled" data-status="CANCELADO"></label>
      
    </div>

  </div>
</template>
  
<script>
export default {
  props: {
    form: Object, // Recebendo a variável form como uma propriedade
  },
  data() {
    return {
      localForm: { ...this.form }, // Criando uma cópia local da prop form
    };
  },
  computed: {
    isValid() {
      return this.localForm.status !== null;
    },
  },
  methods: {
    emitStatusChange() {
      console.log('Status atual:', this.localForm.status);
      this.$emit("status-change", this.localForm.status);
    },
    submitForm() {
      // Lógica para manipular o envio do formulário com this.localForm.status
      console.log("Formulário enviado com status:", this.localForm.status);
    },
  },
};
</script>
  
  <style scoped>


#form-wrapper {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}
#debt-amount-slider {
  display: flex;
  flex-direction: row;
  align-content: stretch;
  position: relative;
  width: 100%;
  height: 50px;
  user-select: none;
}
#debt-amount-slider::before {
  content: " ";
  position: absolute;
  height: 2px;
  width: 100%;
  /* width: calc(100% * (var(--number-of-options) - 1) / var(--number-of-options)); */
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #000;
}
input,
label {
  box-sizing: border-box;
  flex: 1;
  user-select: none;
  cursor: pointer;
}
label {
  display: inline-block;
  position: relative;
  width: 20%;
  height: 100%;
  user-select: none;
}
label::before {
  content: attr(data-status);
  position: absolute;
  left: 50%;
  padding-top: 10px;
  transform: translate(-50%, 45px);
  font-size: 12px;
  letter-spacing: 0.4px;
  font-weight: 400;
  white-space: nowrap;
  opacity: 0.85;
  transition: all 0.15s ease-in-out;
}
label::after {
  content: " ";
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  width: 22px;
  height: 22px;
  border: 2px solid #000;
  background: #ffffff;
  border-radius: 50%;
  pointer-events: none;
  cursor: pointer;
  transition: all 0.15s ease-in-out;
}
label:hover::after {
  transform: translate(-50%, -50%) scale(1.25);
}
input {
  display: none;
}
input:checked + label::before {
  font-weight: 800;
  opacity: 1;
  font-size: 13.5px;
}
input:checked + label::after {
  border-width: 2px;
  transform: translate(-50%, -50%) scale(1.5);
}

/* checked button colors */

input[value="wait"]:checked + label::after {
  background-color: var(--wait-color);
}

input[value="to-do"]:checked + label::after {
  background-color: var(--to-do-color);
}

input[value="doing"]:checked + label::after {
  background-color: var(--doing-color);
}

input[value="done"]:checked + label::after {
  background-color: var(--done-color);
}

input[value="canceled"]:checked + label::after {
  background-color: var(--canceled-color);
}

/* checked label colors */

input[value="wait"]:checked + label {
  color: var(--wait-color);
}

input[value="to-do"]:checked + label {
  color: var(--to-do-color);
}

input[value="doing"]:checked + label {
  color: var(--doing-color);
}

input[value="done"]:checked + label {
  color: var(--done-color);
}

input[value="canceled"]:checked + label {
  color: var(--canceled-color);
}

button {
  display: block;
  position: relative;
  margin: 56px auto 0;
  padding: 10px 20px;
  appearance: none;
  transition: all 0.15s ease-in-out;
  font-family: inherit;
  font-size: 24px;
  font-weight: 600;
  background: #fff;
  border: 2px solid #000;
  border-radius: 8px;
  outline: 0;
  user-select: none;
  cursor: pointer;
}
button:hover {
  background: #000;
  color: #fff;
}
button:active {
  transform: scale(0.9);
}
button:focus {
  background: #4caf50;
  border-color: #4caf50;
  color: #fff;
  pointer-events: none;
}
button:focus::before {
  display: inline-block;
  width: 0;
  opacity: 0;
  content: "\f3f4";
  font-family: "Font Awesome 5 Pro";
  font-weight: 900;
  margin-right: 0;
  transform: rotate(0deg);
  animation: spin 1s linear infinite;
}
form:invalid + button {
  pointer-events: none;
  opacity: 0.25;
}
@keyframes spin {
  from {
    transform: rotate(0deg);
    width: 24px;
    opacity: 1;
    margin-right: 12px;
  }
  to {
    transform: rotate(360deg);
    width: 24px;
    opacity: 1;
    margin-right: 12px;
  }
}
</style>
  