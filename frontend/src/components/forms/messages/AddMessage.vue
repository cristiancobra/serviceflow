<template>
  <div class="container-message" :class="[messageStatus, 'disappear']" v-if="messageStatus">
    <p class="icon" :class="messageStatus">
      <font-awesome-icon :icon="icon" />
    </p>
    <span>{{ messageText }}</span>
    <button @click="closeMessage" class="close-button">x</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      messageTimeout: null,
    };
  },
  props: [
    "messageStatus",
    "messageText"
  ],
  computed: {
    icon() {
      if (this.messageStatus === 'success') {
        return 'fa-solid fa-circle-check';
      } else if (this.messageStatus === 'error') {
        return 'fa-solid fa-circle-exclamation';
      } else {
        return 'fa-solid fa-info-circle';
      }
    }
  },
  watch: {
    messageStatus(newStatus) {
      if (newStatus) {
        this.clearMessageTimeout();
        this.messageTimeout = setTimeout(() => {
          this.$emit('update:messageStatus', '');
        }, 3500);
      }
    }
  },
  methods: {
    clearMessageTimeout() {
      if (this.messageTimeout) {
        clearTimeout(this.messageTimeout);
      }
    },
    closeMessage() {
      this.clearMessageTimeout();
      this.$emit('update:messageStatus', '');
    }
  },
  beforeUnmount() {
    this.clearMessageTimeout();
  }
};
</script>

<style scoped>
li {
  margin-bottom: -16px;
}

.container-message {
  position: fixed;
  top: 40px;
  right: 40px;
  display: flex;
  text-align: left;
  font-weight: 600;
  border-style: solid;
  border-width: 2px;
  border-radius: 6px;
  padding: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
  z-index: 9999;
  transition: opacity 6s ease;
}

.container-message.success {
  color: var(--green);
  border-color: var(--green);
  background-color: var(--green-light);
}

.container-message.error {
  color: var(--red);
  border-color: var(--red);
  background-color: var(--red-light);
}

.icon {
  margin-right: 10px;
  margin-left: 10px;
}

.icon.success {
  color: var(--green);
}

.icon.error {
  color: var(--red);
}

.close-button {
  background: none;
  border: none;
  font-size: 16px;
  cursor: pointer;
  margin-left: auto;
  color: inherit; 
}

.disappear {
  animation-name: fadeOut;
  animation-duration: 3000ms;
  animation-fill-mode: forwards;
}

@keyframes fadeOut {
  0% {
    opacity: 1;
    transform: rotateX(90deg);
  }

  50% {
    opacity: 1;
    transform: rotateX(0deg);
  }

  100% {
    display: none;
    opacity: 0;
  }
}
</style>