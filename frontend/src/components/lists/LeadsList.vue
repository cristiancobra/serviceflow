<template>
  <div class="list-container mb-5 mt-0">
    <div class="row">
      <div class="col-9 d-flex justify-content-left">
        <font-awesome-icon icon="fa-solid fa-tasks" class="icon pe-3 primary" />
        <h2 class="title">CONTATOS</h2>
      </div>
      <div class="col-3">
        <div class="d-flex justify-content-end">
          concluidas {{ completedTasks }} de {{ totalTasks }}
          <font-awesome-icon icon="fa-solid fa-folder" class="ps-3 pe-3 primary" />
        </div>
        <div class="progress">
          <div class="progress-bar" :style="{ width: percentage + '%' }" role="progressbar" :aria-valuenow="percentage"
            aria-valuemin="0" aria-valuemax="100">
            {{ percentage }}%
          </div>
        </div>
      </div>
    </div>
    <LeadCreateForm template="index" @new-lead-event="addLeadCreated($event)" />
    <NoLeadsMessage v-if="!leads"/>
    <div v-else id="line-container" class="row mb-5">
      <div class="card" v-for="lead in leads" :key="lead.id">
        <router-link :to="isLinkDisabled(lead.id)
        ? ''
        : { name: 'leadShow', params: { id: lead.id } }
      " class="row router-link">
          <!-- Coluna para a imagem -->
          <div class="col-4 d-flex align-items-top">
            <span class="icon big">
              <font-awesome-icon icon="fa-solid fa-user-circle" />
            </span>
          </div>

          <!-- Coluna para as demais informações -->
          <div class="col-8">
            <div class="infos-container" @mouseover="showCopyName(lead.id)" @mouseleave="hideCopyName(lead.id)">
              <p class="name">
                {{ lead.name }}
              </p>
              <CopyContentClipboard class="CopyContentClipboard" :data="lead.name" :key="'name_' + lead.id"
                v-show="isMouseOverName[lead.id]" />
            </div>

            <div v-if="lead.email" class="infos-container" @mouseover="showCopyEmail(lead.id)"
              @mouseleave="hideCopyEmail(lead.id)">
              <p class="email">
                {{ lead.email }}
              </p>
              <CopyContentClipboard class="CopyContentClipboard" :data="lead.email" :key="'email_' + lead.id"
                v-show="isMouseOverEmail[lead.id]" />
            </div>

            <div v-if="lead.cel_phone" class="infos-container" @mouseover="showCopyCelPhone(lead.id)"
              @mouseleave="hideCopyCelPhone(lead.id)">
              <p class="cel_phone">
                {{ lead.cel_phone }}
              </p>
              <CopyContentClipboard class="CopyContentClipboard" :data="lead.cel_phone" :key="'cel_phone_' + lead.id"
                v-show="isMouseOverCelPhone[lead.id]" />
            </div>
          </div>
        </router-link>
      </div>
      <router-view />
    </div>
  </div>
</template>


<script>
import { index } from "@/utils/requests/httpUtils";
import CopyContentClipboard from "../CopyContentClipboard.vue";
import LeadCreateForm from "@/components/forms/LeadCreateForm.vue";
import NoLeadsMessage from '@/components/messages/NoLeadsMessage.vue';

export default {
  name: "LeadsList",
  components: {
    CopyContentClipboard,
    LeadCreateForm,
    NoLeadsMessage,
  },
  data() {
    return {
      updatedLead: {
        id: null,
        name: null,
        email: null,
        cel_phone: null,
      },
      isMouseOverName: {},
      isMouseOverEmail: {},
      isMouseOverCelPhone: {},
      // isLinkDisabled: {},
      leads: [],
    };
  },
  methods: {
    addLeadCreated(newLead) {
      this.leads.unshift(newLead);
    },
    async getLeads() {
      this.leads = await index("leads");
    },
    hideCopyName(id) {
      this.isMouseOverName[id] = false;
    },
    hideCopyEmail(id) {
      this.isMouseOverEmail[id] = false;
    },
    hideCopyCelPhone(id) {
      this.isMouseOverCelPhone[id] = false;
    },
    showCopyName(id) {
      this.isMouseOverName[id] = true;
    },
    showCopyEmail(id) {
      this.isMouseOverEmail[id] = true;
    },
    showCopyCelPhone(id) {
      this.isMouseOverCelPhone[id] = true;
    },
    isLinkDisabled(id) {
      return (
        this.isMouseOverName[id] ||
        this.isMouseOverEmail[id] ||
        this.isMouseOverCelPhone[id]
      );
    },
  },
  mounted() {
    this.getLeads();
  },
};
</script>

<style scoped>
.router-link {
  z-index: 1;
  /* pointer-events: none; */
}

.infos-container {
  display: flex;
  align-items: center;
  position: relative;
  z-index: 2;
}

.name {
  text-align: left;
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 0px;
}

.big {
  font-size: 76px;
  color: var(--blue);
}

.card {
  border-style: solid;
  border-width: 2px;
  border-color: var(--blue);
  border-radius: 6px;
  padding: 10px;
  margin-right: 1%;
  margin-top: 2%;
  background-color: var(--blue-light);
  width: 24%;
}

.card:hover {
  border-width: 5px;
  border-radius: 14px;
}

.copyContentClipbord {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
}

.email {
  text-align: left;
  font-size: 14px;
  font-weight: 400;
}

.cel_phone {
  text-align: left;
  font-size: 14px;
  font-weight: 400;
}

.icon {
  text-align: center;
  font-weight: 400;
}

.icon:hover {}

.icon-col {
  z-index: 3;
  font-size: 16px;
  position: absolute;
  right: 0;
  display: inline-block;
  align-items: center;
  /* Centraliza verticalmente */
  justify-content: center;
  /* Centraliza horizontalmente */
  width: 35px;
  height: 35px;
  margin-right: 12px;
  margin-top: -8px;
  padding: 16px;
  background-color: #f1f1f1;
  border-radius: 50%;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  /* Reduz a intensidade do sombreamento */
  transition: font-size 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.icon-col:hover {
  font-size: 20px;
  background-color: #f6f6f6;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
  transform: perspective(500px) rotateX(10deg);
  transform-origin: center center;
  /* Inicia a transformação a partir do centro */
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
