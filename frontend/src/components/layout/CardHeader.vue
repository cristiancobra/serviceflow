<template>
  <div class="card" v-bind:class="getStatusClass(status)">
    <div class="row ms-1">
      <div class="col-1 pt-2 status" v-bind:class="getStatusClass(status)">
        <font-awesome-icon
          class="icon"
          :class="{ loading: isLoading }"
          :icon="faIcon"
        />
        <p class="duration"></p>
      </div>
      <div class="col-11 ps-3">
        <div class="row">
          <p class="title" :class="{ loading: isLoading }">
            <TextEditableInput
              name="name"
              v-model="localName"
              placeholder="descrição detalhada da tarefa"
              @save="emitSave"
            />
          </p>
          <p class="second-line">
            {{ localSecondLine }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import TextEditableInput from "@/components/forms/inputs/text/TextEditableInput.vue";

export default {
  name: "CardHeader",
  data() {
    return {
      localName: "Nome",
      localSecondLine: null,
      isLoading: true,
      users: [],
    };
  },
  components: {
    TextEditableInput,
  },
  props: {
    faIcon: {
      type: String,
    },
    name: {
      type: String,
    },
    secondLine: {
      type: String,
    },
    status: {
      type: String,
    },
  },
  methods: {
    getStatusClass(status) {
      return {
        "bg-danger": status === "pending",
        "bg-warning": status === "in_progress",
        "bg-success": status === "done",
      };
    },
    // getStatusIcon(status) {
    //   return {
    //     pending: "fas fa-hourglass-half",
    //     in_progress: "fas fa-play",
    //     done: "fas fa-check",
    //   }[status];
    // },
    emitSave(editedValue) {
      console.log("editedValue", editedValue);
        this.$emit("save", editedValue);
    },
  },
  watch: {
    name(newValue) {
      this.localName = newValue;
      this.isLoading = false;
    },
    secondLine(newValue) {
      this.localSecondLine = newValue;
    },
  },
  mounted() {
    console.log(this.name);
    console.log(this.localName);
  },
};
</script>

<style scoped>
.icon {
  font-size: 4rem;
  text-align: center;
  font-weight: 400;
  color: var(--purple);
}

.second-line {
  font-size: 1.2rem;
  font-weight: 400;
  text-align: left;
  color: black;
}

.title {
  font-size: 2rem;
  font-weight: 900;
  padding-top: 10px;
  padding-bottom: 0px;
  color: var(--purple);
}

.loading {
  color: var(--gray);
}
</style>