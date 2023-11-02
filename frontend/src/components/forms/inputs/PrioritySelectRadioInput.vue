<template>
  <div class="mb-5">
    <label class="form-label mb-2" :for="name">Prioridade</label>
    <br>
    <input
      type="radio"
      :id="'low'"
      value="low"
      name="priority"
      v-model="localForm.priority"
      @change="emitPriorityChange"
    />
    <label class="priority low" :for="'low'">Baixa</label>

    <input
      type="radio"
      :id="'medium'"
      value="medium"
      name="priority"
      v-model="localForm.priority"
      @change="emitPriorityChange"
      checked
    />
    <label class="priority medium" :for="'medium'">Média</label>

    <input
      type="radio"
      :id="'high'"
      value="high"
      name="priority"
      v-model="localForm.priority"
      @change="emitPriorityChange"
    />
    <label class="priority high" :for="'high'">Alta</label>
  </div>
</template>
  
<script>
export default {
  name: "PrioritySelectRadioInput",
  props: {
    form: Object, // Recebendo a variável form como uma propriedade
    label: String,
  },
  data() {
    return {
      localForm: { ...this.form }, // Criando uma cópia local da prop form
    };
  },
  methods: {
    emitPriorityChange() {
      // Emitir um evento personalizado para notificar o componente pai
      this.$emit("priority-change", this.localForm.priority);
    },
  },
};
</script>
  
  <style>
.priority {
  display: inline-block;
  padding: 8px 16px;
  font-size: 16px;
  background-color: #f0f0f0;
  border: 1px solid #ccc;
  cursor: pointer;
  user-select: none;
  width: 25%;
  text-align: center;
}
/* priorities */
.low {
  border-radius: 16px 0px 0px 16px;
  border-color: var(--gray);
}
.medium {
  border-color: var(--blue);
}
.high {
  border-radius: 0px 16px 16px 0px;
  border-color: var(--red);
}

input[type="radio"] {
  display: none;
}

input[type="radio"][value="low"]:checked + label {
  background-color: var(--gray);
  color: #fff;
  border: 1px solid var(--gray);
}

input[type="radio"][value="medium"]:checked + label {
  background-color: var(--blue);
  color: #fff;
  border: 1px solid var(--blue);
}

input[type="radio"][value="high"]:checked + label {
  background-color: var(--red);
  color: #fff;
  border: 1px solid var(--red);
}
</style>