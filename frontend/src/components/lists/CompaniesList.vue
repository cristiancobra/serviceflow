<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-briefcase" class="page-icon" />
        <h1>EMPRESAS</h1>
      </div>
      <div class="page-action">
        <CompanyCreateForm @new-company-event="addCompanyCreated" />
      </div>
    </div>

    <section class="section-container">
      <div class="search-container">
        <input
          type="text"
          class="search-input"
          v-model="searchTerm"
          placeholder="Digite para buscar"
        />
      </div>

      <div
        class="list-line"
        v-for="company in companies"
        v-bind:key="company.id"
      >
        <!-- Coluna para a imagem -->
        <div class="icons-column">
          <font-awesome-icon
            icon="fa-solid fa-briefcase"
            class="primary big-icon"
          />
        </div>

        <!-- Coluna para as demais informações -->

        <div class="task-column">
        <router-link
          :to="
            isLinkDisabled(company.id)
              ? ''
              : { name: 'companyShow', params: { id: company.id } }
          "
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
            <div v-else-if="company.legal_name">
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
          </router-link>
        </div>

        <div
          class="date-column"
          @mouseover="showCopyEmail(company.id)"
          @mouseleave="hideCopyEmail(company.id)"
        >
          <p v-if="company.cnpj" class="cnpj">
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
          class="date-column"
          @mouseover="showCopyEmail(company.id)"
          @mouseleave="hideCopyEmail(company.id)"
        >
          <p v-if="company.email" class="email">
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
          class="date-column"
          @mouseover="showCopyCelPhone(company.id)"
          @mouseleave="hideCopyCelPhone(company.id)"
        >
          <p v-if="company.cel_phone" class="cel_phone">
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

      <router-view />
    </section>
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
