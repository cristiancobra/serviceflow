<template>
    <div class="icon-col small-menu-item">
        <span
        class="icon" @click="copyContent(data)">
        <font-awesome-icon icon="fa-solid fa-copy" />
        </span>
  </div>
</template>


<script>
export default {
    name: "CopyContentClipboard",
    props: ["data"],
    methods: {
        copyContent(data) {
            const divConteudo = document.createElement("div");
            divConteudo.innerText = this.data; // dado a ser copiado

            // Copia o conteúdo da div para a área de transferência
            navigator.clipboard.writeText(divConteudo.innerText)
            .then(() => {
                console.log("Conteúdo copiado para a área de transferência:", divConteudo.innerText);
            })
            .catch((error) => {
                console.error("Erro ao copiar o conteúdo:", error);
            });
        },
    }
}
</script>

<style scoped>
.icon {
  text-align: center;
  font-weight: 400;
}
.icon:hover {

}
.icon-col {
  font-size: 16px;
  display: inline-block;
  align-items: center; /* Centraliza verticalmente */
  justify-content: center; /* Centraliza horizontalmente */
  width: 35px;
  height: 35px;
  margin-right: 12px;
  margin-top: 0px;
  padding: 10px;
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
</style>