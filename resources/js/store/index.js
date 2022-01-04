import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from 'vuex-persistedstate'

import auth from './modules/auth';
import backup from './modules/backup';
import chart from './modules/chart';
import dialog from './modules/dialog';
import drawer from './modules/drawer';
import flash from './modules/flash';
import menu from './modules/menu';
import note from './modules/note';
import overlay from './modules/overlay';
import task from './modules/task';
import timer from './modules/timer';
import user from './modules/user';

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    auth,
    backup,
    chart,
    dialog,
    drawer,
    flash,
    menu,
    note,
    overlay,
    task,
    timer,
    user,
  },
  plugins: [createPersistedState()]
})
