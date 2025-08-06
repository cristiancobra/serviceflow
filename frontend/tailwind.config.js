/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './public/index.html', // Inclua o HTML principal
    './src/**/*.{vue,js,ts,jsx,tsx}', // Inclua todos os arquivos Vue e JS
  ],
  theme: {
    extend: {
      colors: {
        primary: 'rgb(var(--primary) / <alpha-value>)', // Substitua pela sua variável CSS
        secondary: 'rgb(var(--secondary) / <alpha-value>)', // Outra cor personalizada
      },
    },
  },
  plugins: [
    require('daisyui'), // Inclua o DaisyUI se estiver usando
  ],
  daisyui: {
    themes: [
      {
        mytheme: {
          primary: '#FF0000', // Vermelho intenso como exemplo
          secondary: '#f59e0b',
          accent: '#10b981',
          neutral: '#374151',
          'base-100': '#ffffff',
        },
      },
    ],
  },
};