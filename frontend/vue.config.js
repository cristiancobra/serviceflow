module.exports = {
    productionSourceMap: false,
    configureWebpack: {
        performance: {
            hints: process.env.NODE_ENV === 'production' ? 'warning' : false
        }
    }
};