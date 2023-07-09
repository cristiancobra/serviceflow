<template>
  <div>

    <LeadsFilter @toggle="toggle" />

    <div v-bind:class="{ hidden: isActive }">
      <LeadCreateForm @new-lead-event="addLeadCreated($event)" />
    </div>

    <template v-if="leads.length > 0">
      <div class="row leads-container">
        <LeadsList :leads="leads" />
      </div>
    </template>
    <template v-else>
      <NoLeadsMessage @new-lead-event="addLeadCreated($event)" />
    </template>

  </div>
</template>

<script>
import axios from "axios";
import LeadsFilter from "@/components/filters/LeadsFilter.vue";
import LeadsList from "@/components/LeadsList.vue";
import NoLeadsMessage from '@/components/messages/NoLeadsMessage.vue';
import LeadCreateForm from "@/components/forms/LeadCreateForm.vue";

export default {
  name: "LeadsIndex",
  components: {
    LeadsFilter,
    LeadCreateForm,
    LeadsList,
    NoLeadsMessage,
  },
  data() {
    return {
      isActive: true,
      hasError: false,
      data: null,
      leads: [],
      newLead: {},
    };
  },
  methods: {
    toggle() {
      this.isActive = !this.isActive;
    },
    getLeads() {
      axios
        .get("http://localhost:8191/api/leads")
        .then((response) => {
          this.leads = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    addLeadCreated(newLead) {
      this.leads.push(newLead.lead);
      console.log("Novo lead adicionado:", newLead.lead);
      !this.toggle();
    },
  },
  mounted() {
    this.getLeads();
  },
  computed: {
    leadsData() {
      return this.leads || [];
    },
  },
};
</script>

<style scoped>
.filters-container {
  margin-top: 40px;
  margin-bottom: 50px;
  margin-left: 25%;
  margin-right: 25%;
  display: flex;
  justify-content: center;
}
.slot {
  border-width: 2px;
  border-style: solid;
  border-color: white;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  margin: 0 4px 0 4px;
  color: white;
  font-weight: 800;
  width: 120px;
}
.done {
  background-color: white;
  border-color: #2cb48d;
  color: #2cb48d;
}
.done:hover {
  background-color: #2cb48d;
  color: white;
}
.doing {
  background-color: white;
  border-color: #e78d1f;
  color: #e78d1f;
}
.doing:hover {
  background-color: #e78d1f;
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
.new {
  border-radius: 20px 20px 20px 20px;
  background-color: white;
  border-color: #ff3eb5;
  color: #ff3eb5;
  margin-left: 50px;
  font-size: 16px;
}
.new:hover {
  background-color: #ff3eb5;
  color: white;
  margin-left: 50px;
}
.leads-container {
  margin-left: 10%;
  margin-right: 10%;
  margin-bottom: 10%;
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
