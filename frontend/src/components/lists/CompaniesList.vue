<template>
  <div id="line-container" class="row mb-5">
    <div class="card" v-for="company in companies" :key="company.id">
      <router-link
        :to="
          isLinkDisabled(company.id)
            ? ''
            : { name: 'companyShow', params: { id: company.id } }
        "
        class="row router-link"
      >
        <!-- Coluna para a imagem -->
        <div class="col-4 d-flex align-items-top">
          <span class="icon big">
            <font-awesome-icon icon="fa-solid fa-briefcase" />
          </span>
        </div>

        <!-- Coluna para as demais informações -->
        <div class="col-8">
          <div
            class="infos-container"
            @mouseover="showCopyName(company.id)"
            @mouseleave="hideCopyName(company.id)"
          >
          <div v-if="company.business_name">
            <p class="name">
              {{ company.business_name }}
            </p>
            <CopyContentClipboard
              class="CopyContentClipboard"
              :data="company.business_name"
              :key="'name_' + company.id"
              v-show="isMouseOverName[company.id]"
            />
          </div>
          <div v-else>
            <p class="name">
              {{ company.legal_name }}
            </p>
            <CopyContentClipboard
              class="CopyContentClipboard"
              :data="company.legal_name"
              :key="'name_' + company.id"
              v-show="isMouseOverName[company.id]"
            />
          </div>
        </div>

        <div
            v-if="company.cnpj"
            class="infos-container"
            @mouseover="showCopyEmail(company.id)"
            @mouseleave="hideCopyEmail(company.id)"
          >
            <p class="cnpj">
              {{ company.cnpj }}
            </p>
            <CopyContentClipboard
              class="CopyContentClipboard"
              :data="company.cnpj"
              :key="'cnpj_' + company.id"
              v-show="isMouseOverEmail[company.id]"
            />
          </div>

          <div
            v-if="company.email"
            class="infos-container"
            @mouseover="showCopyEmail(company.id)"
            @mouseleave="hideCopyEmail(company.id)"
          >
            <p class="email">
              {{ company.email }}
            </p>
            <CopyContentClipboard
              class="CopyContentClipboard"
              :data="company.email"
              :key="'email_' + company.id"
              v-show="isMouseOverEmail[company.id]"
            />
          </div>

          <div
            v-if="company.cel_phone"
            class="infos-container"
            @mouseover="showCopyCelPhone(company.id)"
            @mouseleave="hideCopyCelPhone(company.id)"
          >
            <p class="cel_phone">
              {{ company.cel_phone }}
            </p>
            <CopyContentClipboard
              class="CopyContentClipboard"
              :data="company.cel_phone"
              :key="'cel_phone_' + company.id"
              v-show="isMouseOverCelPhone[company.id]"
            />
          </div>
        </div>
      </router-link>
    </div>

    <router-view />
  </div>
</template>


<script>
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
      isMouseOverName: {},
      isMouseOverEmail: {},
      isMouseOverCelPhone: {},
      // isLinkDisabled: {},
    };
  },
  methods: {
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
  font-size: 56px;
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
.icon:hover {
}
.icon-col {
  z-index: 3;
  font-size: 16px;
  position: absolute;
  right: 0;
  display: inline-block;
  align-items: center; /* Centraliza verticalmente */
  justify-content: center; /* Centraliza horizontalmente */
  width: 35px;
  height: 35px;
  margin-right: 12px;
  margin-top: -8px;
  padding: 16px;
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
