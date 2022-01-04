import Vue from 'vue';
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';
import '@mdi/font/css/materialdesignicons.css';
import 'vuetify/dist/vuetify.min.css';

Vue.use(Vuetify);

export default new Vuetify({
  theme: {
    themes: {
      light: {
        primary: '#ff4f4f',
        info: '#4c86b5',
        success: '#17b535',
        secondary: '#cacaca',
        accent: '#8c9eff',
        error: '#b71c1c',
      }
    },
  },
  icons: {
    iconfont: 'mdi'
  }
});

