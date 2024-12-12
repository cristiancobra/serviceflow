<template>
    <div class="user-menu">
        <div class="user-info" @click="toggleDropdown">
            <img v-if="urlImagePhoto" :src="urlImagePhoto" alt="User Image" class="user-image" />
            <font-awesome-icon v-else icon="fas fa-user" class="user-image" />
        </div>
        <div v-if="dropdownVisible" class="dropdown-menu">
            <router-link :to="`/users/${userData.id}`" class="dropdown-item">CONTA</router-link>
            <router-link to="/journeys" class="dropdown-item">JORNADAS</router-link>
        </div>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import { IMAGES_PATH } from '@/config/apiConfig';

export default {
    data() {
        return {
            dropdownVisible: false
        };
    },
    computed: {
        ...mapState({
            userData: state => state.userData
        }),
        urlImagePhoto() {
            return `${IMAGES_PATH}${this.userData.photo}`;
        }
    },
    methods: {
        toggleDropdown() {
            this.dropdownVisible = !this.dropdownVisible;
        }
    },
    mounted() {
      console.log("userData", this.userData);
    },
};
</script>

<style scoped>
.user-menu {
    position: relative;
    display: inline-block;
    margin-right: 20px;
}

.user-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    color: white;
}

.user-image {
    width: 60px;
    height: 60px;
    border-style: solid;
    border-color: white;
    border-width: 3px;
    border-radius: 50%;
    margin-right: 0px;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background-color: var(--primary);
    border: 1px solid #ccc;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    display: block; 
}

.dropdown-item {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: white;
}

.dropdown-item:hover {
    background-color: #f0f0f0;
}
</style>