import Vue from 'vue';
import Vuetify from 'vuetify/lib';
import colors from 'vuetify/lib/util/colors';

Vue.use(Vuetify);

export default new Vuetify({
  theme: {
    themes: {
      light: {
        primary: colors.green.accent4,
        secondary: colors.grey.darken4,
        subtle: colors.grey.lighten2,
        accent: '#8c9eff',
        error: colors.red.accent3
      }
    },
    dark: false
  }
});
