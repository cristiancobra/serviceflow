<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-tasks" class="icon pe-3 text-white 2xl" />
        <h1>CONTATOS</h1>
      </div>
      <div class="page-action">
            

      </div>
    </div>

    <NoLeadsMessage v-if="!leads" />

    <section class="section-container">
      <search-input v-model="searchTerm" placeholder="Buscar por contato, email ou telefone" />

      <div class="flex w-full border-b border-gray-300 pb-2 items-end" v-for="lead in filteredLeads" v-bind:key="lead.id">
        <!-- Coluna para a imagem -->
        <div class="w-1/10">
          <font-awesome-icon
            icon="fa-solid fa-user-circle"
            class="text-primary text-2xl"
          />
        </div>

        <div class="w-4/10">
          <router-link
            :to="
              isLinkDisabled(lead.id)
                ? ''
                : { name: 'leadShow', params: { id: lead.id } }
            "
          >
            <p class="text-black font-bold">
              {{ lead.name }}
            </p>
            <CopyContentClipboard
              class="CopyContentClipboard"
              :data="lead.name"
              :key="'name_' + lead.id"
              v-show="isMouseOverName[lead.id]"
            />
          </router-link>
        </div>

        <div
          class="w-3/10"
          @mouseover="showCopyEmail(lead.id)"
          @mouseleave="hideCopyEmail(lead.id)"
        >
          <p v-if="lead.email" class="text-black">
            {{ lead.email }}
          </p>
          <CopyContentClipboard
            class="CopyContentClipboard"
            :data="lead.email"
            :key="'email_' + lead.id"
            v-show="isMouseOverEmail[lead.id]"
          />
        </div>

        <div
          class="date-column"
          @mouseover="showCopyCelPhone(lead.id)"
          @mouseleave="hideCopyCelPhone(lead.id)"
        >
          <p v-if="lead.cel_phone" class="text-black text-right">
            {{ lead.cel_phone }}
          </p>
          <CopyContentClipboard
            class="CopyContentClipboard"
            :data="lead.cel_phone"
            :key="'cel_phone_' + lead.id"
            v-show="isMouseOverCelPhone[lead.id]"
          />
        </div>
      </div>
    </section>
    <router-view />
  </div>
</template>


<script>
import { index } from "@/utils/requests/httpUtils";
import CopyContentClipboard from "../CopyContentClipboard.vue";
import SearchInput from "../filters/SearchInput.vue";
// import LeadCreateForm from "@/components/forms/LeadCreateForm.vue";
import NoLeadsMessage from "@/components/messages/NoLeadsMessage.vue";

export default {
  name: "LeadsList",
  components: {
    CopyContentClipboard,
    SearchInput,
    // LeadCreateForm,
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
      isModalVisible: false,
      isMouseOverName: {},
      isMouseOverEmail: {},
      isMouseOverCelPhone: {},
      searchTerm: "",
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
  computed: {
    filteredLeads() {
      if (!this.searchTerm || !this.leads) {
        return this.leads;
      }
      return this.leads.filter(lead => 
        lead.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
        (lead.email && lead.email.toLowerCase().includes(this.searchTerm.toLowerCase())) ||
        (lead.cel_phone && lead.cel_phone.toLowerCase().includes(this.searchTerm.toLowerCase()))
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
