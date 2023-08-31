<template>
  <div id="line-container">
    <div class="row list-line" v-for="service in services" v-bind:key="service.id">
      <div
        id="name"
        class="col-3"
        style="position:relative"
        @mouseover="showContextMenu(service, 'name')"
        @mouseleave="hideContextMenu(service, 'name')"
      >
        <p class="name" v-if="!service.editing || (service.editing && service.editingField !== 'name')">
          {{ service.name }}
        </p>
        <input
          v-else-if="service.editing && service.editingField === 'name'"
          type="text"
          class="form-control"
          v-model="service.name"
          @keydown.enter="saveservice(service, 'name')"
          @keydown.esc="cancelEdit(service)"
        />
        <div
          class="small-menu"
          v-show="service.showContextMenu && service.activeField === 'name'"
        >
          <div class="icon-col small-menu-item">
            <span class="icon" @click="startEdit(service, 'name')">
              <font-awesome-icon icon="fa-solid fa-pencil" />
            </span>
          </div>

          <CopyContentClipboard :data="service.name" />

        </div>
      </div>

      <div
        id="labor_hours"
        class="col-1"
        style="position:relative"
        @mouseover="showContextMenu(service, 'labor_hours')"
        @mouseleave="hideContextMenu(service, 'labor_hours')"
      >
        <p class="labor_hours" v-if="!service.editing || (service.editing && service.editingField !== 'labor_hours')">
          {{ service.labor_hours }}
        </p>
        <input
          v-else-if="service.editing && service.editingField === 'labor_hours'"
          type="text"
          class="form-control"
          v-model="service.labor_hours"
          @keydown.enter="saveservice(service, 'labor_hours')"
          @keydown.esc="cancelEdit(service)"
        />
        <div
          class="small-menu"
          v-show="service.showContextMenu && service.activeField === 'labor_hours'"
        >
          <div class="icon-col small-menu-item">
            <span class="icon" @click="startEdit(service, 'labor_hours')">
              <font-awesome-icon icon="fa-solid fa-pencil" />
            </span>
          </div>

          <CopyContentClipboard :data="service.labor_hours" />

        </div>
      </div>

      <div
        id="labor_hourly_rate"
        class="col-2"
        style="position:relative"
        @mouseover="showContextMenu(service, 'labor_hourly_rate')"
        @mouseleave="hideContextMenu(service, 'labor_hourly_rate')"
      >
        <p class="labor_hourly_rate" v-if="!service.editing || (service.editing && service.editingField !== 'labor_hourly_rate')">
          R$ {{ service.labor_hourly_rate }}
        </p>
        <input
          v-else-if="service.editing && service.editingField === 'labor_hourly_rate'"
          type="text"
          class="form-control"
          v-model="service.labor_hourly_rate"
          @keydown.enter="saveservice(service, 'labor_hourly_rate')"
          @keydown.esc="cancelEdit(service)"
        />
        <div
          class="small-menu"
          v-show="service.showContextMenu && service.activeField === 'labor_hourly_rate'"
        >
          <div class="icon-col small-menu-item">
            <span class="icon" @click="startEdit(service, 'labor_hourly_rate')">
              <font-awesome-icon icon="fa-solid fa-pencil" />
            </span>
          </div>

          <CopyContentClipboard :data="service.labor_hourly_rate" />

        </div>
      </div>

      <div
        id="labor_hourly_rate_total"
        class="col-2"
        style="position:relative"
        @mouseover="showContextMenu(service, 'labor_hourly_rate_total')"
        @mouseleave="hideContextMenu(service, 'labor_hourly_rate_total')"
      >
        <p class="labor_hourly_rate_total" v-if="!service.editing || (service.editing && service.editingField !== 'labor_hourly_rate_total')">
          R$ {{ service.labor_hourly_rate_total }}
        </p>
        <input
          v-else-if="service.editing && service.editingField === 'labor_hourly_rate_total'"
          type="text"
          class="form-control"
          v-model="service.labor_hourly_rate_total"
          @keydown.enter="saveservice(service, 'labor_hourly_rate_total')"
          @keydown.esc="cancelEdit(service)"
        />
        <div
          class="small-menu"
          v-show="service.showContextMenu && service.activeField === 'labor_hourly_rate_total'"
        >
          <div class="icon-col small-menu-item">
            <span class="icon" @click="startEdit(service, 'labor_hourly_rate_total')">
              <font-awesome-icon icon="fa-solid fa-pencil" />
            </span>
          </div>

          <CopyContentClipboard :data="service.labor_hourly_rate_total" />

        </div>
      </div>

      <div
        id="profit_percentage"
        class="col-1"
        style="position:relative"
        @mouseover="showContextMenu(service, 'profit_percentage')"
        @mouseleave="hideContextMenu(service, 'profit_percentage')"
      >
        <p class="profit_percentage" v-if="!service.editing || (service.editing && service.editingField !== 'profit_percentage')">
          {{ service.profit_percentage }} %
        </p>
        <input
          v-else-if="service.editing && service.editingField === 'profit_percentage'"
          type="text"
          class="form-control"
          v-model="service.profit_percentage"
          @keydown.enter="saveservice(service, 'profit_percentage')"
          @keydown.esc="cancelEdit(service)"
        />
        <div
          class="small-menu"
          v-show="service.showContextMenu && service.activeField === 'profit_percentage'"
        >
          <div class="icon-col small-menu-item">
            <span class="icon" @click="startEdit(service, 'profit_percentage')">
              <font-awesome-icon icon="fa-solid fa-pencil" />
            </span>
          </div>

          <CopyContentClipboard :data="service.profit_percentage" />

        </div>
      </div>

      <div
        id="price"
        class="col-2"
        style="position:relative"
        @mouseover="showContextMenu(service, 'price')"
        @mouseleave="hideContextMenu(service, 'price')"
      >
        <p class="price" v-if="!service.editing || (service.editing && service.editingField !== 'price')">
          R$ {{ service.price }}
        </p>
        <input
          v-else-if="service.editing && service.editingField === 'price'"
          type="text"
          class="form-control"
          v-model="service.price"
          @keydown.enter="saveservice(service, 'price')"
          @keydown.esc="cancelEdit(service)"
        />
        <div
          class="small-menu"
          v-show="service.showContextMenu && service.activeField === 'price'"
        >
          <div class="icon-col small-menu-item">
            <span class="icon" @click="startEdit(service, 'price')">
              <font-awesome-icon icon="fa-solid fa-pencil" />
            </span>
          </div>

          <CopyContentClipboard :data="service.price" />

        </div>
      </div>

      <div id="icon-show" class="col-0 icon-col">
        <router-link
          :to="{
            name: 'serviceShow',
            params: { id: service.id },
          }"
        >
          <span class="icon">
            <font-awesome-icon icon="fa-solid fa-eye" />
          </span>
        </router-link>
      </div>

      <router-view />
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import CopyContentClipboard from './CopyContentClipboard.vue';

export default {
  name: "ServicesList",
  components: {
    CopyContentClipboard,
  },
  props: ["services"],
  data() {
    return {
      updatedservice: {
        id: null,
        name: null,
        labor_hours: null,
        labor_hourly_rate: null,
        labor_hourly_rate_total: null,
        profit_percentage: null,
        price: null,
      },
    };
  },
  methods: {
    showContextMenu(service, field) {
      service.showContextMenu = true;
      service.activeField = field;
    },
    hideContextMenu(service) {
      service.showContextMenu = false;
      service.activeField = '';
    },
    startEdit(service, field) {
      service.editing = true;
      service.editingField = field;
    },
    cancelEdit(service) {
      service.editing = false;
      service.editingField = null;
    },
    cancelEditOnEsc(event) {
      if (event.key === "Escape") {
        const service = this.services.find((service) => service.editing);
        if (service) {
          this.cancelEdit(service);
        }
      }
    },
    saveservice(service, field) {
      if (service.activeField === field) {
        service.editing = false;
        service.editingField = null;

        this.updatedservice.id = service.id;
        this.updatedservice.name = service.name;
        this.updatedservice.labor_hours = service.labor_hours;
        this.updatedservice.labor_hourly_rate = service.labor_hourly_rate;
        this.updatedservice.labor_hourly_rate_total = service.labor_hourly_rate_total;
        this.updatedservice.profit_percentage = service.profit_percentage;
        this.updatedservice.price = service.price;

        axios
          .put(`http://localhost:8191/api/services/${service.id}`, this.updatedservice)
          .then((response) => {
            console.log(response.data);
          });
      }
    },
  },
  mounted() {
    window.addEventListener("keydown", this.cancelEditOnEsc);
    // console.log(services);
  },
  beforeUnmount() {
    window.removeEventListener("keydown", this.cancelEditOnEsc);
  },
};
</script>

<style scoped>
.name {
  text-align: left;
  font-size: 16px;
  font-weight: 600;
}
.labor_hours {
  text-align: center;
  font-size: 16px;
  font-weight: 400;
}
.labor_hourly_rate {
  text-align: center;
  font-size: 16px;
  font-weight: 400;
}
.labor_hourly_rate_total {
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
