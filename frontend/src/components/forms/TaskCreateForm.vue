<template>
  <div>
    <div class="mt-5 mb-5 success">
      {{ message }}
    </div>

    <div id="form" class="container">
      <form @submit.prevent="submitForm">
        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="name"> Nome </label>
            </div>
            <div class="col-10">
              <input
                class="form-control"
                type="text"
                id="name"
                v-model="form.name"
                placeholder="Digite um nome para seu projeto"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="description"> Descrição </label>
            </div>
            <div class="col-10">
              <input
                class="form-control"
                type="text"
                id="description"
                v-model="form.description"
                placeholder="Digite o nome do responsável por garantir a execução do projeto"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="company_id"> Empresa </label>
            </div>
            <div class="col-10">
              <input
                class="form-control"
                type="text"
                id="company_id"
                v-model="form.company_id"
                placeholder="Digite o nome da empresa"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="contact_id"> Contato </label>
            </div>
            <div class="col-7">
              <select
                class="form-select"
                id="contact_id"
                v-model="form.contact_id"
                placeholder="Selecione o contato"
              >
                <option disabled value="">Selecione um contato</option>
                <option v-for="lead in leads" :key="lead.id" :value="lead.id">
                  {{ lead.name }}
                </option>
              </select>
            </div>
            <div class="col-2 button-new m-1" @click="toggle()">+</div>
          </div>
          <div :class="{ hidden: isActive }">
            <LeadCreateForm @new-lead-event="addLeadCreated($event)" />
          </div>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="user_id"> Responsável </label>
            </div>
            <div class="col-10">
              <input
                class="form-control"
                type="text"
                id="user_id"
                v-model="form.user_id"
                placeholder="Digite o nome do responsável por garantir a execução do projeto"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="date_start"> Início </label>
            </div>
            <div class="col-10">
              <input
                class="form-control"
                type="date"
                id="date_start"
                v-model="form.date_start"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="date_due"> Prazo final </label>
            </div>
            <div class="col-10">
              <input
                class="form-control"
                type="date"
                id="date_due"
                v-model="form.date_due"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="date_conclusion">
                Data de conclusão
              </label>
            </div>
            <div class="col-10">
              <input
                class="form-control"
                type="date"
                id="date_conclusion"
                v-model="form.date_conclusion"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="row mt-4 mb-4">
            <div class="col-2">
              <label class="labels" for="status"> Prioridade </label>
            </div>
            <div class="col-10">
              <input
                type="radio"
                id="low"
                value="low"
                name="priority"
                v-model="form.priority"
              />
              <label class="priority low" for="low">Baixa</label>

              <input
                type="radio"
                id="medium"
                value="medium"
                name="priority"
                v-model="form.priority"
                checked
              />
              <label class="priority medium" for="medium">Média</label>

              <input
                type="radio"
                id="high"
                value="high"
                name="priority"
                v-model="form.priority"
              />
              <label class="priority high" for="high">Alta</label>
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col-2">
              <label class="labels" for="status"> Situação </label>
            </div>
            <div class="col-10">
              <select
                class="form-select"
                id="status"
                name="status"
                v-model="form.status"
                aria-label="Selecione a situação do projeto"
              >
                <option
                  v-for="(status, index) in allStatus"
                  v-bind:key="index"
                  :value="status"
                >
                  {{ status }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <div class="row ms-5 me-5 mt-4 mb-2">
          <button type="submit" class="button-new">Criar</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import LeadCreateForm from "./LeadCreateForm.vue";

export default {
  name: "TaskCreateForm",
  emits: ["new-task-event"],
  components: {
    LeadCreateForm,
  },
  data() {
    return {
      allStatus: [],
      message: null,
      newTask: null,
      leads: [],
      data: [],
      isActive: true,
      form: {
        name: null,
        description: null,
        company_id: null,
        contact_id: null,
        user_id: null,
        date_start: null,
        date_due: null,
        date_conclusion: null,
        status: "to-do",
        priority: "medium",
      },
    };
  },
  methods: {
    addLeadCreated(newLead) {
      this.leads.push(newLead.lead);
      !this.toggle();
      this.form.contact_id = newLead.lead.id;
      console.log(this.form.contact_id);
      console.log(newLead)
    },
    clearForm() {
      this.form.name = null;
      this.form.description = null;
      this.form.company_id = null;
      this.form.contact_id = null;
      this.form.user_id = null;
      this.form.date_start = null;
      this.form.date_due = null;
      this.form.date_conclusion = null;
      this.form.priority = "medium";
    },
    getLeads() {
      axios
        .get("http://localhost:8191/api/leads")
        .then((response) => {
          this.leads = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    getTasksStatus() {
      axios
        .get("http://localhost:8191/api/tasks/status")
        .then((response) => {
          this.allStatus = response.data;
        })
        .catch((error) => console.log(error));
    },
    async submitForm() {
      axios
        .post("http://localhost:8191/api/tasks", this.form)
        .then((response) => {
          this.newTask = response.data.task;
          this.newTaskEvent(this.newTask);
          this.clearForm();
        });
    },
    newTaskEvent(data) {
      this.$emit("new-task-event", data);
    },
    toggle() {
      this.isActive = !this.isActive;
    },
  },
  mounted() {
    this.getTasksStatus();
    this.getLeads();
  },
};
</script>

<style scoped>
.container {
  border-style: solid;
  border-color: #ff3eb5;
  border-width: 2px;
  margin-left: 180px;
  margin-right: 180px;
  margin-bottom: 60px;
  margin-top: 60px;
  padding: 20px;
  border-radius: 16px;
  transition: all 0.5s;
  text-align: left;
  font-weight: 800;
}
.labels {
  text-align: left;
  margin-left: 0;
}
.radio-group {
  display: flex;
  gap: 10px;
  align-items: center;
}

input[type="radio"] {
  display: none;
}

.priority {
  display: inline-block;
  padding: 8px 16px;
  font-size: 16px;
  background-color: #f0f0f0;
  border: 1px solid #ccc;
  cursor: pointer;
  user-select: none;
}

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