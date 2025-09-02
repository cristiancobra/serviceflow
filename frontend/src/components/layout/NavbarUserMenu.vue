<template>
  <div class="user-menu" @mouseleave="closeDropdown">
    <div class="user-info" @click="toggleDropdown">
      <img
        v-if="userData && userData.photo"
        :src="urlImagePhoto"
        alt="User Image"
        class="user-image"
      />
      <font-awesome-icon v-else icon="fas fa-user" class="user-faicon" />
    </div>
    <div class="play-container">
      <font-awesome-icon v-if="openJourney" icon="fas fa-play" class="play" />
      <font-awesome-icon v-else icon="fas fa-pause" class="off" />
    </div>
    <div v-if="dropdownVisible" class="dropdown-menu">
      <router-link
        v-if="openJourney"
        
  :to="{ name: 'opportunityShow', params: { id: openJourney.opportunity_id }, query: { scrollTo: 'tasks' } }"
        class="dropdown-item"
      >
        <font-awesome-icon icon="fas fa-play" class="play" />
        {{ openJourney.name }}
      </router-link>
      <router-link :to="`/users/${userData.id}`" class="dropdown-item">
        CONTA
      </router-link>
      <router-link to="/journeys" class="dropdown-item"> JORNADAS </router-link>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
import { IMAGES_PATH } from "@/config/apiConfig";

export default {
  propos: {
    openJourney: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      dropdownVisible: false,
    };
  },
  computed: {
    ...mapState({
      userData: (state) => state.userData,
      openJourney: (state) => state.openJourney,
    }),
    urlImagePhoto() {
      return `${IMAGES_PATH}${this.userData.photo}`;
    },
  },
  methods: {
    toggleDropdown() {
      this.dropdownVisible = !this.dropdownVisible;
    },
    closeDropdown() {
      this.dropdownVisible = false;
    },
    handleClickOutside(event) {
      if (!this.$el.contains(event.target)) {
        this.closeDropdown();
      }
    },
  },
  mounted() {
    document.addEventListener("click", this.handleClickOutside);
    console.log("openJourney", this.openJourney);
  },
  beforeUnmount() {
    document.removeEventListener("click", this.handleClickOutside);
  },
};
</script>

<style scoped>
.user-menu {
  position: absolute;
  /* display: inline-block; */
  /* margin-right: 20px; */
  top: 1%;
  right: 1%;
  z-index: 10;
  /* padding-top: 0.5rem;
    padding-bottom: 0.5rem; */
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  color: white;
}

.user-image {
  width: 48px;
  height: 50px;
  border-style: solid;
  border-color: var(--primary);
  border-width: 5px;
  border-radius: 50%;
  margin-right: 0px;
}

.user-faicon {
  border-style: solid;
  border-color: white;
  border-width: 3px;
  border-radius: 50%;
  font-size: 1.8rem;
  padding: 0.6rem;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background-color: var(--primary);
  border: 1px solid #ccc;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  display: block;
  min-width: 230px;
}

.dropdown-item {
  display: block;
  padding: 10px;
  text-decoration: none;
  color: white;
}

.dropdown-item:hover {
  background-color: #f0f0f0;
  color: var(--primary);
}

.play-container {
  position: absolute;
  font-size: 1.2rem;
  bottom: -3px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1;
}

.play {
  color: var(--green); /* Cor inicial */
  animation: blink 4s infinite; /* Define a animação */
}

@keyframes blink {
  0%,
  100% {
    color: var(--green); /* Cor inicial e final */
  }
  50% {
    color: var(--green-light); /* Cor intermediária */
  }
}
</style>