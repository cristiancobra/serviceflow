<template>
  <div>
    <div class="login-container" :class="{ 'slide-enter': transitionIn, 'slide-leave': transitionOut }">
      <div class="flex items-center px-8 py-4">
        <img :src="require('@/assets/logo-serviceflow-BRANCO.png')" class="h-16 md:h-8 w-auto" alt="logo-serviceflow" />
      </div>
      <div class="login-box">
        <form @submit.prevent="submit">
          <div class="email-container">
            <label class="label" for="email">email</label>
            <input class="input-login" type="email" id="email" name="email" v-model="form.email" required />
          </div>
          <div class="password-container">
            <label class="label" for="password">password</label>
            <input class="input-login" type="password" id="password" name="password" v-model="form.password" required />
          </div>
          <div class="button-container">
            <button class="button-login" type="submit">ENTRAR</button>
          </div>
        </form>
      </div>
    </div>
    <div class="background-login">
      <div class="flex flex-row gap-4">
        <div class="w-1/6">
          <div class="small-container-fake">
            <font-awesome-icon icon="fa-solid fa-bullseye" class="icon-fake"/>
          </div>
        </div>
        <div class="w-1/6">
          <div class="small-container-fake">
            <font-awesome-icon icon="fa-solid fa-file-invoice" class="icon-fake"/>
          </div>
        </div>
      </div>
      <div class="page-container-fake">

      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import router from '@/router';

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
  methods: {
    async submit() {
      try {
        this.transitionOut = true;
        await this.login(this.form);
        await this.checkOpenJourneys();

        setTimeout(() => {
          router.push({ name: "home" });
          // this.$root.isLogged = true;
        }, 600);
      } catch (error) {
        console.error("Erro de login:", error);
        router.push({ name: "login" });
      }
    },
    ...mapActions([
      'checkOpenJourneys',
      'login',
    ]),
    startTransition() {

      this.transitionIn = true;

    },
  },
  mounted() {
    this.transitionOut = false;
    setTimeout(() => {
      this.startTransition();
    })
  },
};
</script>
