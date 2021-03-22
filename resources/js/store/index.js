import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from 'vuex-persistedstate'

import auth from './modules/auth';
import task from './modules/task';
import chart from './modules/chart';
import note from './modules/note';
import user from './modules/user';

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    auth,
    task,
    chart,
    note,
    user
  },
  plugins: [createPersistedState()]
})
