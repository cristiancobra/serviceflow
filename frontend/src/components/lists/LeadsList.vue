<template>
  <div id="line-container" class="row">
    <div class="col m-3 card" v-for="lead in leads" v-bind:key="lead.id">
      <router-link
        :to="{
          name: 'leadShow',
          params: { id: lead.id },
        }"
      >
        <div id="name" class="row" style="position: relative">
          <span class="icon big">
            <font-awesome-icon icon="fa-solid fa-user-circle" />
          </span>

          <p
            class="name"
            v-if="
              !lead.editing || (lead.editing && lead.editingField !== 'name')
            "
          >
            {{ lead.name }}
          </p>
          <input
            v-else-if="lead.editing && lead.editingField === 'name'"
            type="text"
            class="form-control"
            v-model="lead.name"
            @keydown.enter="saveLead(lead, 'name')"
            @keydown.esc="cancelEdit(lead)"
          />
          <div
            class="small-menu"
            v-show="lead.showContextMenu && lead.activeField === 'name'"
          >
            <div class="icon-col small-menu-item">
              <span class="icon" @click="startEdit(lead, 'name')">
                <font-awesome-icon icon="fa-solid fa-pencil" />
              </span>
            </div>

            <CopyContentClipboard :data="lead.name" />
          </div>
        </div>

        <div
          id="email"
          class="row"
          style="position: relative"
          @mouseover="showContextMenu(lead, 'email')"
          @mouseleave="hideContextMenu(lead, 'email')"
        >
          <p
            class="email"
            v-if="
              !lead.editing || (lead.editing && lead.editingField !== 'email')
            "
          >
            {{ lead.email }}
          </p>
          <input
            v-else-if="lead.editing && lead.editingField === 'email'"
            type="text"
            class="form-control"
            v-model="lead.email"
            @keydown.enter="saveLead(lead, 'email')"
            @keydown.esc="cancelEdit(lead)"
          />
          <div
            class="small-menu"
            v-show="lead.showContextMenu && lead.activeField === 'email'"
          >
            <div class="icon-col small-menu-item">
              <span class="icon" @click="startEdit(lead, 'email')">
                <font-awesome-icon icon="fa-solid fa-pencil" />
              </span>
            </div>

            <CopyContentClipboard :data="lead.email" />
          </div>
        </div>

        <div
          id="cel_phone"
          class="row"
          style="position: relative"
          @mouseover="showContextMenu(lead, 'cel_phone')"
          @mouseleave="hideContextMenu(lead, 'cel_phone')"
        >
          <p
            class="cel_phone"
            v-if="
              !lead.editing ||
              (lead.editing && lead.editingField !== 'cel_phone')
            "
          >
            {{ lead.cel_phone }}
          </p>
          <input
            v-else-if="lead.editing && lead.editingField === 'cel_phone'"
            type="text"
            class="form-control"
            v-model="lead.cel_phone"
            @keydown.enter="saveLead(lead, 'cel_phone')"
            @keydown.esc="cancelEdit(lead)"
          />
          <div
            class="small-menu"
            v-show="lead.showContextMenu && lead.activeField === 'cel_phone'"
          >
            <div class="icon-col small-menu-item">
              <span class="icon" @click="startEdit(lead, 'cel_phone')">
                <font-awesome-icon icon="fa-solid fa-pencil" />
              </span>
            </div>

            <CopyContentClipboard :data="lead.cel_phone" />
          </div>
        </div>
      </router-link>
    </div>

    <router-view />
  </div>
</template>

<script>
import axios from "axios";
import CopyContentClipboard from "../CopyContentClipboard.vue";

export default {
  name: "LeadsList",
  components: {
    CopyContentClipboard,
  },
  props: ["leads"],
  data() {
    return {
      updatedLead: {
        id: null,
        name: null,
        email: null,
        cel_phone: null,
      },
    };
  },
  methods: {
    showContextMenu(lead, field) {
      lead.showContextMenu = true;
      lead.activeField = field;
    },
    hideContextMenu(lead) {
      lead.showContextMenu = false;
      lead.activeField = "";
    },
    startEdit(lead, field) {
      lead.editing = true;
      lead.editingField = field;
    },
    cancelEdit(lead) {
      lead.editing = false;
      lead.editingField = null;
    },
    cancelEditOnEsc(event) {
      if (event.key === "Escape") {
        const lead = this.leads.find((lead) => lead.editing);
        if (lead) {
          this.cancelEdit(lead);
        }
      }
    },
    saveLead(lead, field) {
      if (lead.activeField === field) {
        lead.editing = false;
        lead.editingField = null;

        this.updatedLead.id = lead.id;
        this.updatedLead.name = lead.name;
        this.updatedLead.email = lead.email;
        this.updatedLead.cel_phone = lead.cel_phone;

        axios
          .put(`http://localhost:8191/api/leads/${lead.id}`, this.updatedLead)
          .then((response) => {
            console.log(response.data);
          });
      }
    },
  },
  mounted() {
    window.addEventListener("keydown", this.cancelEditOnEsc);
    // console.log(leads);
  },
  beforeUnmount() {
    window.removeEventListener("keydown", this.cancelEditOnEsc);
  },
};
</script>

<style scoped>
.name {
  text-align: center;
  font-size: 16px;
  font-weight: 600;
}
.big {
  font-size: 44px;
  color: var(--azul);
}
.card {
  border-style: solid;
  border-width: 2px;
  border-color: var(--azul);
  border-radius: 6px;
  padding: 10px;
  background-color: var(--azul-claro);
  /* height: 15vh; */
}
.email {
  text-align: center;
  font-size: 14px;
  font-weight: 400;
}
.cel_phone {
  text-align: center;
  font-size: 16px;
  font-weight: 400;
}
.icon {
  text-align: center;
  font-weight: 400;
}
.icon:hover {
}
.icon-col {
  font-size: 16px;
  display: inline-block;
  align-items: center; /* Centraliza verticalmente */
  justify-content: center; /* Centraliza horizontalmente */
  width: 35px;
  height: 35px;
  margin-right: 12px;
  margin-top: -8px;
  padding: 10px;
  background-color: #f1f1f1;
  border-radius: 50%;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Reduz a intensidade do sombreamento */
  transition: font-size 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}
.icon-col:hover {
  font-size: 20px;
  background-color: #f6f6f6;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
  transform: perspective(500px) rotateX(10deg);
  transform-origin: center center; /* Inicia a transformação a partir do centro */
}
.comments {
  text-align: left;
  font-size: 14px;
  margin-top: 20px;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: rgb(61, 61, 61);
}
a:link {
  text-decoration: none;
}
a:visited {
  text-decoration: none;
}
a:hover {
  text-decoration: none;
}
a:active {
  text-decoration: none;
}
.list-line {
  margin-top: 3px;
  padding: 0px;
  padding-top: 14px;
  padding-bottom: 0px;
  width: auto;
  height: auto;
  background-color: white;
  text-align: left;
  margin-right: 0;
  border-bottom-style: solid;
  border-bottom-width: 1px;
  border-left-style: solid;
  border-left-width: 12px;
  border-color: #007e8b;
  border-radius: 0px 0 0 0px;
}
.slot {
  display: block;
  text-align: center;
  border-style: solid;
  border-width: 2px;
  border-color: #007e8b;
  border-radius: 0 6px 6px 0;
  padding-top: 6px;
  padding-bottom: 6px;
  background-color: pink;
}
.slot.active {
  border-left-style: none;
  background-color: white;
}
.slot.inactive {
  background-color: lightgray;
}
.slots-column {
  margin-left: -12px;
}
.small-menu {
  position: absolute;
  top: 0;
  right: 0;
  display: block;
  z-index: 1;
  /* box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); */
  padding: 0px;
}
.small-menu.show {
  display: none;
}
</style>
