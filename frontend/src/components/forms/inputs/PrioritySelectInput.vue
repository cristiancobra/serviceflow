<template>
  <div class="mb-5">
    <label class="form-label mb-2" :for="'priority-' + uniqueId">Prioridade</label>
    <br>
    <input type="radio" :id="'low-' + uniqueId" value="low" :name="'priority-' + uniqueId" @change="updatePriority('low')" :checked="localValue === 'low'" />
    <label class="item" :class="['priority', { 'low': localValue === 'low' }]" :for="'low-' + uniqueId">Baixa</label>

    <input type="radio" :id="'medium-' + uniqueId" value="medium" :name="'priority-' + uniqueId" @change="updatePriority('medium')" :checked="localValue === 'medium'" />
    <label class="item" :class="['priority', { 'medium': localValue === 'medium' }]" :for="'medium-' + uniqueId">Média</label>

    <input type="radio" :id="'high-' + uniqueId" value="high" :name="'priority-' + uniqueId" @change="updatePriority('high')" :checked="localValue === 'high'" />
    <label class="item" :class="['priority', { 'high': localValue === 'high' }]" :for="'high-' + uniqueId">Alta</label>
  </div>
</template>

<script>
export default {
  name: "PrioritySelectInput",
  props: {
    id: String,
    label: String,
    modelValue: String,
  },
  data() {
    return {
      localValue: this.modelValue,
      uniqueId: Math.random().toString(36).substring(2, 9), 
    };
  },
  watch: {
    modelValue(newVal) {
      this.localValue = newVal;
    },
  },
  methods: {
    updatePriority(value) {
      this.localValue = value;
      console.log(this.localValue);
      this.$emit("update:modelValue", this.localValue);
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
.item.low {
  border-radius: 16px 0px 0px 16px;
  border-color: var(--gray);
  color: #fff;
}

.item.medium {
  border-color: var(--blue);
  color: #fff;
}

.item.high {
  border-radius: 0px 16px 16px 0px;
  border-color: var(--red);
  color: #fff;
}

input[type="radio"] {
  display: none;
}

</style>