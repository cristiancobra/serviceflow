/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './public/index.html', // Inclua o HTML principal
    './src/**/*.{vue,js,ts,jsx,tsx}', // Inclua todos os arquivos Vue e JS
  ],
  plugins: [
    require('daisyui'), // Inclua o DaisyUI se estiver usando
  ],
  daisyui: {
    themes: [
      {
        mytheme: {
          primary: '#B1388D', // Cor primária atualizada
          secondary: '#f59e0b',
          accent: '#10b981',
          neutral: '#374151',
          'base-100': '#ffffff',
        },
      },
    ],
    theme: "mytheme", // Define o tema padrão
  },
};