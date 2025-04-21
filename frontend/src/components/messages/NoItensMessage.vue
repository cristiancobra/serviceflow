<template>
  <div class="image-container">
    <img :src="require('@/assets/caixa_vazia-PB.png')" alt="logo-serviceflow" />

    <p class="bold">Nenhum item cadastrado ainda!</p>
    <p class="message">Adicione no botão acima</p>

    
  </div>
</template>
  
  <script>
export default {
  components: {

  },
  data() {
    return {
    
    };
  },
};
</script>
  
  <style>
.image-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-top: 110px;
}
.bold {
  font-size: 26px;
  font-weight: 800;
  margin-top: 40px;
}
.message {
  font-size: 22px;
  font-weight: 400;
  margin-top: 20px;
}
</style>