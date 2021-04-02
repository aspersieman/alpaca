import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from 'vuex-persistedstate'

import auth from './modules/auth';
import chart from './modules/chart';
import menu from './modules/menu';
import note from './modules/note';
import task from './modules/task';
import user from './modules/user';

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    auth,
    chart,
    menu,
    note,
    task,
    user
  },
  plugins: [createPersistedState()]
})
