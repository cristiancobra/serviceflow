module.exports = {
  productionSourceMap: false,
  transpileDependencies: [], // Adicione esta linha
  configureWebpack: {
    performance: {
      hints: process.env.NODE_ENV === 'production' ? 'warning' : false
    }
  },
  chainWebpack: config => {
    config
      .plugin('html')
      .tap(args => {
        args[0].title = process.env.VUE_APP_TITLE || 'ServiceFlow'; // Altere esta linha
        return args;
      });
  }
};