<template>
  <div>
    <label class="show-label mb-3" :for="name">{{ label }}</label>
    <div class="editor-container">
      <div v-if="!editing && !modelValue" @click="startEditing" class="placeholder-text">
        Clique para adicionar uma descrição...
      </div>
      <div v-else-if="!editing" @click="startEditing" v-html="modelValue" class="preview-content"></div>
      <div v-else class="quill-wrapper">
        <div class="editor-hint mb-2 text-muted small">
          💡 Dicas: Link (selecione texto + 🔗) | Imagem (📷 faz upload automático) | Vídeo (🎬 URL do YouTube/Vimeo)
        </div>
        <QuillEditor
          ref="quillEditor"
          v-model="content"
          contentType="html"
          :placeholder="'Descrição detalhada da tarefa'"
          :toolbar="toolbarOptions"
          :options="editorOptions"
          @textChange="handleTextChange"
          theme="snow"
        />
        <div class="editor-actions mt-2">
          <button @click="handleSave" class="btn btn-primary btn-sm me-2">
            Salvar
          </button>
          <button @click="handleCancel" class="btn btn-secondary btn-sm">
            Cancelar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import axios from 'axios'
import { BACKEND_URL } from '@/config/apiConfig'

export default {
  components: {
    QuillEditor,
  },
  props: {
    label: String,
    name: String,
    modelValue: String,
  },
  data() {
    return {
      editing: false,
      content: this.modelValue || '',
      originalContent: '', // Guarda o conteúdo original para comparação
      toolbarOptions: [
        [{ 'size': ['small', false, 'large', 'huge'] }], // Tamanhos de fonte
        [{ 'header': [1, 2, 3, false] }], // Títulos
        ['bold', 'italic', 'underline', 'strike'], // Formatação básica
        [{ 'color': [] }, { 'background': [] }], // Cores
        [{ 'list': 'ordered'}, { 'list': 'bullet' }], // Listas
        [{ 'align': [] }], // Alinhamento
        ['blockquote', 'code-block'], // Citação e código
        ['link', 'image', 'video'], // Link, Imagem e Vídeo
        ['clean'] // Limpar formatação
      ],
      editorOptions: {
        modules: {
          toolbar: true,
        },
      }
    };
  },
  methods: {
    cancelEditing() {
      this.editing = false;
    },
    startEditing() {
      this.editing = true;
      this.content = this.modelValue || '';
      this.originalContent = this.modelValue || '';
      this.$nextTick(() => {
        const editor = this.$refs.quillEditor?.getQuill();
        if (editor) {
          // Seta o conteúdo diretamente no editor
          editor.root.innerHTML = this.content;
          
          // Adiciona handler customizado para imagens
          const toolbar = editor.getModule('toolbar');
          if (toolbar) {
            toolbar.addHandler('image', this.imageHandler);
          }
        }
        this.$refs.quillEditor?.focus();
      });
    },
    imageHandler() {
      const input = document.createElement('input');
      input.setAttribute('type', 'file');
      input.setAttribute('accept', 'image/*');
      input.click();

      input.onchange = async () => {
        const file = input.files[0];
        if (file) {
          // Limita tamanho: 2MB
          if (file.size > 2 * 1024 * 1024) {
            alert('Imagem muito grande! Por favor, escolha uma imagem menor que 2MB.');
            return;
          }

          // Cria FormData para upload
          const formData = new FormData();
          formData.append('image', file);

          try {
            const editor = this.$refs.quillEditor?.getQuill();
            const range = editor?.getSelection(true);

            // Mostra indicador de carregamento
            if (editor && range) {
              editor.insertText(range.index, '⏳ Carregando imagem...');
            }

            // Faz upload da imagem usando axios
            const response = await axios.post(`${BACKEND_URL}upload/editor-image`, formData, {
              headers: {
                'Content-Type': 'multipart/form-data',
              },
            });

            const data = response.data;

            if (data.success && editor && range) {
              // Remove o texto de carregamento
              editor.deleteText(range.index, 23);
              // Insere a imagem com a URL retornada
              editor.insertEmbed(range.index, 'image', data.url);
              editor.setSelection(range.index + 1);
            } else {
              throw new Error('Falha no upload');
            }
          } catch (error) {
            console.error('Erro ao fazer upload da imagem:', error);
            alert('Erro ao fazer upload da imagem. Tente novamente.');
            
            // Remove o texto de carregamento em caso de erro
            const editor = this.$refs.quillEditor?.getQuill();
            const range = editor?.getSelection();
            if (editor && range) {
              editor.deleteText(range.index - 23, 23);
            }
          }
        }
      };
    },
    handleTextChange() {
      // Atualiza o conteúdo quando o texto muda
      const editor = this.$refs.quillEditor?.getQuill();
      if (editor) {
        this.content = editor.root.innerHTML;
      }
    },
    handleSave() {
      const editor = this.$refs.quillEditor?.getQuill();
      let sanitizedContent = '';
      
      if (editor) {
        sanitizedContent = editor.root.innerHTML;
      } else {
        sanitizedContent = this.content;
      }

      // Limpa conteúdo vazio do Quill
      if (sanitizedContent === "<p><br></p>" || sanitizedContent === "<p></p>" || sanitizedContent === "") {
        sanitizedContent = "";
      }

      // Só salva se o conteúdo mudou
      if (sanitizedContent !== this.originalContent) {
        console.log('TextEditor - salvando:', sanitizedContent);
        this.$emit("save", sanitizedContent);
      } else {
        console.log('TextEditor - sem alterações, não salvando');
      }
      
      this.editing = false;
    },
    handleCancel() {
      // Restaura o conteúdo original e fecha sem salvar
      this.content = this.originalContent;
      this.editing = false;
    },
  },
  watch: {
    modelValue(value) {
      if (this.content !== value) {
        this.content = value || '';
      }
    },
  },
};
</script>

<style scoped>
.editor-container {
  border-style: solid;
  border-width: 1px;
  border-color: #e0e0e0;
  border-radius: 8px;
  padding: 1rem;
  min-height: 100px;
  cursor: pointer;
}

.placeholder-text {
  color: #999;
  font-style: italic;
}

.preview-content {
  text-align: left;
  min-height: 50px;
}

/* Estilos para conteúdo HTML renderizado */
.preview-content :deep(ul),
.preview-content :deep(ol) {
  padding-left: 1.5rem;
  margin: 0.5rem 0;
}

.preview-content :deep(ul) {
  list-style-type: disc;
}

.preview-content :deep(ol) {
  list-style-type: decimal;
}

.preview-content :deep(li) {
  margin: 0.25rem 0;
}

.preview-content :deep(p) {
  margin: 0.5rem 0;
}

.preview-content :deep(strong) {
  font-weight: bold;
}

.preview-content :deep(em) {
  font-style: italic;
}

.preview-content :deep(u) {
  text-decoration: underline;
}

.preview-content :deep(s) {
  text-decoration: line-through;
}

/* Tamanhos de fonte do Quill */
.preview-content :deep(.ql-size-small) {
  font-size: 0.75em;
}

.preview-content :deep(.ql-size-large) {
  font-size: 1.5em;
}

.preview-content :deep(.ql-size-huge) {
  font-size: 2.5em;
}

/* Alinhamento */
.preview-content :deep(.ql-align-center) {
  text-align: center;
}

.preview-content :deep(.ql-align-right) {
  text-align: right;
}

.preview-content :deep(.ql-align-justify) {
  text-align: justify;
}

.preview-content :deep(h1) {
  font-size: 2em;
  font-weight: bold;
  margin: 0.67em 0;
}

.preview-content :deep(h2) {
  font-size: 1.5em;
  font-weight: bold;
  margin: 0.75em 0;
}

.preview-content :deep(h3) {
  font-size: 1.17em;
  font-weight: bold;
  margin: 0.83em 0;
}

.preview-content :deep(blockquote) {
  border-left: 4px solid #ccc;
  margin: 1em 0;
  padding-left: 1em;
  color: #666;
}

.preview-content :deep(pre) {
  background-color: #f5f5f5;
  padding: 1em;
  border-radius: 4px;
  overflow-x: auto;
}

.preview-content :deep(code) {
  background-color: #f5f5f5;
  padding: 0.2em 0.4em;
  border-radius: 3px;
  font-family: monospace;
}

.preview-content :deep(a) {
  color: #0066cc;
  text-decoration: underline;
}

.preview-content :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 4px;
  margin: 0.5rem 0;
}

.preview-content :deep(iframe) {
  max-width: 100%;
  aspect-ratio: 16 / 9;
  border-radius: 4px;
  margin: 0.5rem 0;
}

.quill-wrapper {
  cursor: text;
}

.editor-hint {
  font-size: 0.875rem;
  color: #6c757d;
  font-style: italic;
}

.editor-actions {
  display: flex;
  justify-content: flex-start;
  gap: 0.5rem;
}

.show-label {
  display: block;
  text-align: left;
  font-size: 20px;
  font-weight: 800;
  margin-bottom: 10px;
}
</style>