<template>
  <div class="container-message disappear" :class="messageStatus">

      <p class="icon">
        <font-awesome-icon icon="fa-solid fa-circle-check" />
      </p>
      {{ messageText }}
  </div>
</template>

<script>
export default {
  props: [
    "messageStatus",
    "messageText"
  ],
  methods: {
    closeMessage() {
      this.$refs.errorMessage.classList.add("highlight");

      // Remove a classe de destaque e adiciona a classe para desaparecer suavemente após 5 segundos
      setTimeout(() => {
        this.$refs.errorMessage.classList.remove("highlight");
        this.$refs.errorMessage.classList.add("fade-out");
      }, 5000);
    },
  },
};
</script>

<style scoped>
li {
  margin-bottom: -16px;
}

.container-message {
  position: fixed;
  top: 40px;
  /* ajuste conforme necessário */
  right: 40px;
  display: flex;
  text-align: left;
  font-weight: 600;
  border-style: solid;
  border-width: 2px;
  border-radius: 6px;
  padding: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
  /* Reduz a intensidade do sombreamento */
  z-index: 9999;
  transition: opacity 5s ease;
  /* Transição de 5 segundos */
}

.container-message.highlight {
  opacity: 1;
  /* Destaque o elemento */
}

.container-message.fade-out {
  opacity: 0;
  /* Faz o elemento desaparecer */
}

.deleted {
  color: var(--red);
  border-color: var(--red);
  background-color: var(--red-light);
}

.success {
  color: var(--green);
  border-color: var(--green);
  background-color: var(--green-light);

}

.disappear {
  animation-name: fadeOut;
  animation-duration: 5000ms;
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
    transform: rotateX(90deg);
  }
}


.icon {
  font-size: 2rem;
  text-align: center;
  padding-right: 1rem;
}
</style>