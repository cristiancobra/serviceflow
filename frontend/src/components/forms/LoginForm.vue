<template>
  <div
    class="login-container"
    :class="{'slide-enter': transitionIn, 'slide-leave': transitionOut }"
  >
    <div class="d-flex p-4">
      <img
        :src="require('@/assets/logo-serviceflow-BRANCO.png')"
        class="logo"
        alt="logo-serviceflow"
      />
    </div>

    <div class="login-box">
      <form @submit.prevent="login">
        <div class="d-flex align-items-center justify-content-end">
          <label class="label" for="email">email</label>
          <input
            class="input"
            type="email"
            id="email"
            name="email"
            v-model="form.email"
            required
          />
        </div>
        <div class="d-flex align-items-center justify-content-end mt-3">
          <label class="label" for="password">password</label>
          <input
            class="input"
            type="password"
            id="password"
            name="password"
            v-model="form.password"
            required
          />
        </div>
        <button class="button-login mt-4" type="submit">ENTRAR</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { API_BASE_URL, LOGIN_URL } from "@/config/apiConfig";

export default {
  data() {
    return {
      responseData: null,
      transitionIn: false,
      transitionOut: false, 
      form: {
        email: null,
        password: null,
      },
    };
  },
  // computed: {
  //   transitionStyles() {
  //     return {
  //       transform: this.transitionIn ? "translateX(-130%)" : "translateX(0)",
  //       transition: "transform 2s",
  //     };
  //   },
  // },
  methods: {
    async login() {
      try {
        this.transitionOut = true;

        axios.defaults.withCredentials = true;

        await axios.get("http://localhost:8191/sanctum/csrf-cookie");

        const response = await axios.post(
          `${API_BASE_URL}${LOGIN_URL}`,
          this.form
        );

        const token = response.data.access_token;
        console.log(token);
        localStorage.setItem("access_token", token);

        setTimeout(() => {
          this.$router.push({ name: "home" });
          this.$root.isLogged = true;
        }, 600);
      } catch (error) {
        // Trate erros de login, por exemplo, exibindo uma mensagem de erro
        console.error("Erro de login:", error);
      }
    },
    startTransition() {
      
      if (this.$root.isLogged !== true) {
        this.transitionIn = true;
        console.log(this.transitionIn);
      } else {
        this.transitionOut = true;
        console.log(this.transitionOut);
      }
    },
  },
  mounted() {
    // Defina transitionIn como true ao montar o componente
    // this.transitionOut = true;
    setTimeout(() => {
    this.startTransition();
  })
  },
};
</script>

<style scoped>
.label {
  color: white;
  padding-top: 1.8%;
  padding-bottom: 1.8%;
  font-size: 1.4rem;
  font-weight: 400;
  padding-right: 24px;
  text-align: left;
}
.button-login {
  border-radius: 20px;
  border-color: white;
  border-style: solid;
  background-color: var(--purple);
  color: white;
  /* width: 30%; */
  padding-top: 1.8%;
  padding-bottom: 1.8%;
  padding-left: 5%;
  padding-right: 5%;
  font-weight: 800;
  font-size: 1.2rem;
}
.button-login:hover {
  border-color: var(--orange);
  /* background-color: var(--purple-light); */
  color: var(--orange);
  /* width: 30%; */
}
.input {
  background-color: var(--purple);
  color: white;
  border-radius: 20px;
  padding: 3%;
  font-size: 1.2rem;
  border-style: solid;
}
.input:hover,
.input:focus {
  background-color: var(--purple);
  color: var(--orange);  /* Se desejar alterar a cor do texto ao focar */
}
.login-box {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  height: 60vh;
  border-style: none;
  border-color: gray;
  border-radius: 20px;
  border-color: white;
  background-color: var(--purple);
  color: white;
  z-index: 1001;
}

.login-container {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: var(--purple);
  padding-bottom: 20%;
  z-index: 1000;
  transform: translateX(-130%);
}

/* slides  */

.slide-enter {
  transform: translateX(0);
  transition: transform 2s;
}

/* Sua classe de transição de saída */
.slide-leave {
  transform: translateX(-130%);
  transition: transform 2s;
}
</style>