<template>
  <div class="mb-5">
    <label class="form-label mb-2" :for="name">Prioridade</label>
    <br>
    <input
      type="radio"
      :id="'low'"
      value="low"
      name="priority"
      @change="updatePriority('low')"
      :checked="priority === 'low'"
    />
    <label class="priority low" :for="'low'">Baixa</label>

    <input
      type="radio"
      :id="'medium'"
      value="medium"
      name="priority"      
      @change="updatePriority('medium')"
      :checked="priority === 'medium'"
    />
    <label class="priority medium" :for="'medium'">Média</label>

    <input
      type="radio"
      :id="'high'"
      value="high"
      name="priority"      
      @change="updatePriority('high')"
      :checked="priority === 'high'"
    />
    <label class="priority high" :for="'high'">Alta</label>
  </div>
</template>
  
<script>
export default {
  name: "PrioritySelectRadioInput",
  props: {
    priority: String,
    label: String,
  },
  data() {
    return {
      modelValue: this.priority,
    };
  },
  methods: {
    updatePriority(value) {
      this.$emit("priority-change", value);
    },
  },
  // watch: {
  //   priority: {
  //     handler(newValue) {
  //       this.modelValue = newValue;
  //     },
  //     immediate: true,
  //   },
  // },
  mounted() {
    console.log(this.priority);
    console.log(this.modelValue);
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