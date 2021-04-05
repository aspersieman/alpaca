import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from 'vuex-persistedstate'

import auth from './modules/auth';
import chart from './modules/chart';
import dialog from './modules/dialog';
import drawer from './modules/drawer';
import menu from './modules/menu';
import note from './modules/note';
import snackbar from './modules/snackbar';
import task from './modules/task';
import timer from './modules/timer';
import user from './modules/user';

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    auth,
    chart,
    dialog,
    drawer,
    menu,
    note,
    snackbar,
    task,
    timer,
    user
  },
  plugins: [createPersistedState()]
})
