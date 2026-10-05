<template>
  <ModalCard
    title="Usuário"
    :subtitle="user?.name || ''"
    :subtitle-editable="!!user"
    subtitle-placeholder="Nome do usuário"
    icon="fa-solid fa-user"
    size="md"
    :compact="compact"
    compact-size="max-w-3xl"
    @close="$emit('close')"
    @save-subtitle="updateUserField('name', $event)"
  >
    <div v-if="!user" class="p-5 text-center text-base-content/60">
      Carregando usuário...
    </div>

    <template v-else>
      <error-message v-if="validationErrors" :formResponse="validationErrors" />

      <div
        v-if="message"
        class="flex items-center justify-between gap-4 rounded-lg p-3 mb-6 text-sm font-medium"
        :class="message.status === 'success' ? 'bg-success/10 text-success border border-success/30' : 'bg-error/10 text-error border border-error/30'"
      >
        <span>
          <font-awesome-icon
            :icon="message.status === 'success' ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-exclamation'"
            class="me-2"
          />
          {{ message.text }}
        </span>
        <button type="button" class="opacity-70 hover:opacity-100" @click="message = null">
          <font-awesome-icon icon="fa-solid fa-times" />
        </button>
      </div>

      <!-- Foto + Email -->
      <div class="flex flex-wrap items-center gap-6">
        <div class="relative flex-shrink-0">
          <button
            type="button"
            class="group relative w-24 h-24 rounded-full border-2 border-primary overflow-hidden bg-base-200 flex items-center justify-center cursor-pointer"
            title="Trocar foto (JPG, PNG, GIF ou WEBP, até 2MB)"
            @click="$refs.photo.click()"
          >
            <img v-if="user.photo" class="w-full h-full object-cover" :src="urlImagePhoto" alt="Foto do usuário" />
            <font-awesome-icon v-else icon="fas fa-user" class="text-4xl text-base-content/50" />
            <span class="absolute inset-0 flex items-center justify-center text-xl text-white bg-black/45 opacity-0 group-hover:opacity-100 transition-opacity">
              <font-awesome-icon icon="fa-solid fa-camera" />
            </span>
          </button>
          <input ref="photo" type="file" accept="image/*" class="hidden" @change="handlePhotoUpload" />
        </div>

        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 text-sm text-base-content/80 min-w-0">
            <font-awesome-icon icon="fas fa-envelope" class="text-primary w-4 flex-shrink-0" />
            <span class="font-semibold whitespace-nowrap">Email:</span>
            <text-editable-field
              name="email"
              :modelValue="user.email"
              @save="(value) => updateUserField('email', value)"
              placeholder="email@exemplo.com"
            />
          </div>
        </div>
      </div>
    </template>

    <template v-if="user" #footer>
      <div class="flex w-full justify-end">
        <button type="button" class="btn" title="Fechar" @click="$emit('close')">
          <font-awesome-icon v-if="compact" icon="fa-solid fa-times" />
          <template v-else>Fechar</template>
        </button>
      </div>
    </template>
  </ModalCard>
</template>

<script>
import axios from "axios";
import { show, updateField } from "@/utils/requests/httpUtils";
import { BACKEND_URL, USER_URL, IMAGES_PATH } from "@/config/apiConfig";
import ModalCard from "@/components/modals/ModalCard.vue";
import TextEditableField from "@/components/fields/text/TextEditableField.vue";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";

const MAX_PHOTO_SIZE = 2 * 1024 * 1024;
const ALLOWED_PHOTO_TYPES = ["image/jpeg", "image/png", "image/jpg", "image/gif", "image/webp"];

export default {
  name: "UserDetailModal",
  components: {
    ModalCard,
    TextEditableField,
    ErrorMessage,
  },
  props: {
    userId: {
      type: [Number, String],
      required: true,
    },
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
  },
  // user-updated: qualquer alteração no usuário (payload: usuário atualizado)
  emits: ["close", "user-updated"],
  data() {
    return {
      user: null,
      validationErrors: null,
      message: null,
    };
  },
  computed: {
    urlImagePhoto() {
      return `${IMAGES_PATH}${this.user.photo}`;
    },
  },
  methods: {
    async getUser() {
      this.user = await show("users", this.userId);
    },
    // Mantém o usuário logado (avatar do menu etc.) em dia quando ele edita o próprio perfil
    syncLoggedUser() {
      const loggedUser = this.$store.state.userData;
      if (loggedUser && loggedUser.id === this.user.id) {
        this.$store.commit("setUserData", { ...loggedUser, ...this.user });
      }
    },
    async updateUserField(fieldName, newValue) {
      try {
        this.validationErrors = null;
        const updatedUser = await updateField("users", this.userId, fieldName, newValue);
        this.user[fieldName] = updatedUser[fieldName];
        this.syncLoggedUser();
        this.$emit("user-updated", this.user);
      } catch (error) {
        console.error(`Erro ao atualizar ${fieldName}:`, error);
        if (error.response?.status === 422) {
          this.validationErrors = { errors: error.response.data.errors || {} };
        } else {
          this.message = { status: "error", text: "Erro ao atualizar o usuário. Tente novamente." };
        }
      }
    },
    async handlePhotoUpload() {
      const photo = this.$refs.photo.files[0];
      this.$refs.photo.value = "";
      if (!photo) return;

      if (photo.size > MAX_PHOTO_SIZE) {
        this.message = { status: "error", text: "Arquivo muito grande! O tamanho máximo é 2MB." };
        return;
      }
      if (!ALLOWED_PHOTO_TYPES.includes(photo.type)) {
        this.message = { status: "error", text: "Formato inválido! Use: JPG, PNG, GIF ou WEBP." };
        return;
      }

      const formData = new FormData();
      formData.append("photo", photo);
      try {
        const response = await axios.post(`${BACKEND_URL}${USER_URL}/${this.userId}/photo`, formData);
        this.user.photo = response.data.data.photo;
        this.syncLoggedUser();
        this.message = { status: "success", text: "Foto enviada com sucesso!" };
        this.$emit("user-updated", this.user);
      } catch (error) {
        console.error("Erro ao fazer upload da foto:", error);
        let text = "Erro ao fazer upload da foto. Tente novamente.";
        if (error.response?.status === 422) {
          text = error.response.data.errors?.photo?.[0] || "Erro de validação. Verifique o arquivo.";
        } else if (error.response?.status === 413) {
          text = "Arquivo muito grande! Máximo 2MB.";
        }
        this.message = { status: "error", text };
      }
    },
  },
  watch: {
    userId() {
      this.getUser();
    },
  },
  mounted() {
    this.getUser();
  },
};
</script>
