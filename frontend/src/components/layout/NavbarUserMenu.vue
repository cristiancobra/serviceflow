<template>
    <div class="user-menu" @mouseleave="closeDropdown">
        <div class="user-info" @click="toggleDropdown">
            <img v-if="userData && userData.photo" :src="urlImagePhoto" alt="User Image" class="user-image" />
            <font-awesome-icon v-else icon="fas fa-user" class="user-faicon" />
        </div>
        <div v-if="dropdownVisible" class="dropdown-menu">
            <router-link v-if="openJourney" :to="{ name: 'taskShow', params: { id: openJourney.task_id } }" class="dropdown-item">
                <font-awesome-icon icon="fas fa-play" class="play" />
                {{ openJourney.name }}
            </router-link>
            <router-link :to="`/users/${userData.id}`" class="dropdown-item">
                CONTA
            </router-link>
            <router-link to="/journeys" class="dropdown-item">
                JORNADAS
            </router-link>
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
            userData: state => state.userData,
            openJourney: state => state.openJourney,

        }),
        urlImagePhoto() {
            return `${IMAGES_PATH}${this.userData.photo}`;
        }
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
        document.addEventListener('click', this.handleClickOutside);
        console.log('openJourney', this.openJourney);
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
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
    width: 48px;
    height: 50px;
    border-style: solid;
    border-color: white;
    border-width: 2px;
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
</style>