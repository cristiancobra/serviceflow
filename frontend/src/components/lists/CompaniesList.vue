<template>
  <div id="line-container" class="row">
    <div class="col-3 m-3 card" v-for="company in companies" v-bind:key="company.id">
      <router-link
        :to="{
          name: 'companyShow',
          params: { id: company.id },
        }"
      >
        <div id="name" class="row" style="position: relative">
          <span class="icon big">
            <font-awesome-icon icon="fa-solid fa-user-circle" />
          </span>

          <p
            class="name"
            v-if="
              !company.editing || (company.editing && company.editingField !== 'name')
            "
          >
            {{ company.name }}
          </p>
          <input
            v-else-if="company.editing && company.editingField === 'name'"
            type="text"
            class="form-control"
            v-model="company.name"
            @keydown.enter="saveLead(company, 'name')"
            @keydown.esc="cancelEdit(company)"
          />
          <div
            class="small-menu"
            v-show="company.showContextMenu && company.activeField === 'name'"
          >
            <div class="icon-col small-menu-item">
              <span class="icon" @click="startEdit(company, 'name')">
                <font-awesome-icon icon="fa-solid fa-pencil" />
              </span>
            </div>

            <CopyContentClipboard :data="company.name" />
          </div>
        </div>

        <div
          id="email"
          class="row"
          style="position: relative"
          @mouseover="showContextMenu(company, 'email')"
          @mouseleave="hideContextMenu(company, 'email')"
        >
          <p
            class="email"
            v-if="
              !company.editing || (company.editing && company.editingField !== 'email')
            "
          >
            {{ company.email }}
          </p>
          <input
            v-else-if="company.editing && company.editingField === 'email'"
            type="text"
            class="form-control"
            v-model="company.email"
            @keydown.enter="saveLead(company, 'email')"
            @keydown.esc="cancelEdit(company)"
          />
          <div
            class="small-menu"
            v-show="company.showContextMenu && company.activeField === 'email'"
          >
            <div class="icon-col small-menu-item">
              <span class="icon" @click="startEdit(company, 'email')">
                <font-awesome-icon icon="fa-solid fa-pencil" />
              </span>
            </div>

            <CopyContentClipboard :data="company.email" />
          </div>
        </div>

        <div
          id="cel_phone"
          class="row"
          style="position: relative"
          @mouseover="showContextMenu(company, 'cel_phone')"
          @mouseleave="hideContextMenu(company, 'cel_phone')"
        >
          <p
            class="cel_phone"
            v-if="
              !company.editing ||
              (company.editing && company.editingField !== 'cel_phone')
            "
          >
            {{ company.cel_phone }}
          </p>
          <input
            v-else-if="company.editing && company.editingField === 'cel_phone'"
            type="text"
            class="form-control"
            v-model="company.cel_phone"
            @keydown.enter="saveLead(company, 'cel_phone')"
            @keydown.esc="cancelEdit(company)"
          />
          <div
            class="small-menu"
            v-show="company.showContextMenu && company.activeField === 'cel_phone'"
          >
            <div class="icon-col small-menu-item">
              <span class="icon" @click="startEdit(company, 'cel_phone')">
                <font-awesome-icon icon="fa-solid fa-pencil" />
              </span>
            </div>

            <CopyContentClipboard :data="company.cel_phone" />
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
  name: "CompaniesList",
  components: {
    CopyContentClipboard,
  },
  props: ["companies"],
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
    showContextMenu(company, field) {
      company.showContextMenu = true;
      company.activeField = field;
    },
    hideContextMenu(company) {
      company.showContextMenu = false;
      company.activeField = "";
    },
    startEdit(company, field) {
      company.editing = true;
      company.editingField = field;
    },
    cancelEdit(company) {
      company.editing = false;
      company.editingField = null;
    },
    cancelEditOnEsc(event) {
      if (event.key === "Escape") {
        const company = this.companies.find((company) => company.editing);
        if (company) {
          this.cancelEdit(company);
        }
      }
    },
    saveLead(company, field) {
      if (company.activeField === field) {
        company.editing = false;
        company.editingField = null;

        this.updatedLead.id = company.id;
        this.updatedLead.name = company.name;
        this.updatedLead.email = company.email;
        this.updatedLead.cel_phone = company.cel_phone;

        axios
          .put(`http://localhost:8191/api/companies/${company.id}`, this.updatedLead)
          .then((response) => {
            console.log(response.data);
          });
      }
    },
  },
  mounted() {
    window.addEventListener("keydown", this.cancelEditOnEsc);
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
  color: var(--blue);
}
.card {
  border-style: solid;
  border-width: 2px;
  border-color: var(--blue);
  border-radius: 6px;
  padding: 10px;
  background-color: var(--blue-light);
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
