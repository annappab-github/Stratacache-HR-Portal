// module.exports = {
//   "publicPath": "",
//   "css": {
//     "loaderOptions": {
//       "sass": {
//         "data": "\n          @import \"@/styles/_variables.scss\";\n        "
//       }
//     }
//   },
//   "transpileDependencies": [
//     "vuetify"
//   ]
// }

module.exports = {
  publicPath: '/',

  css: {
    loaderOptions: {
      scss: {
        prependData: '\n          @import "@/styles/_variables.scss";\n        '
      }
    }
  },

  transpileDependencies: [
    'vuetify'
  ],

  pluginOptions: {
    i18n: {
      locale: 'en',
      fallbackLocale: 'en',
      localeDir: 'locales',
      enableInSFC: false,
      enableBridge: false,
      enableLegacy: false,
      runtimeOnly: false,
      compositionOnly: false,
      fullInstall: true
    }
  }
};
