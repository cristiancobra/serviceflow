<template>
  <div class="mb-5">
    <label class="form-label mb-2" :for="name">Prioridade</label>
    <br>
    <input type="radio" id="low" value="low" name="priority" @change="updatePriority('low')" :checked="priority === 'low'" />
    <label :class="['priority', { 'low': priority === 'low' }]" for="low">Baixa</label>

    <input type="radio" id="medium" value="medium" name="priority" @change="updatePriority('medium')" :checked="priority === 'medium'" />
    <label :class="['priority', { 'medium': priority === 'medium' }]" for="medium">Média</label>

    <input type="radio" id="high" value="high" name="priority" @change="updatePriority('high')" :checked="priority === 'high'" />
    <label :class="['priority', { 'high': priority === 'high' }]" for="high">Alta</label>
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
  color: #fff;
}

.medium {
  border-color: var(--blue);
  color: #fff;
}

.high {
  border-radius: 0px 16px 16px 0px;
  border-color: var(--red);
  color: #fff;
}

input[type="radio"] {
  display: none;
}

</style>