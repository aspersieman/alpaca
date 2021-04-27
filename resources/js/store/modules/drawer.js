export const state= {
  navigationDrawer: false,
  noteDrawer: false,
}

// Mutation for when you use it as state property
export const mutations = {
  NAVIGATION_DRAWER: (state, payload) => {
    state.navigationDrawer = payload;
  },

  NOTE_DRAWER: (state, payload) => {
    state.noteDrawer = payload;
  },
}

export const actions= {
  toggleNavigationDrawer({commit, getters}) {
    const navigationDrawerState = !getters['navigationDrawer'];
    commit('NAVIGATION_DRAWER', navigationDrawerState);
  }
}

// You can use it as a state getter function (probably the best solution)
export const getters = {
  navigationDrawer: state => state.navigationDrawer,
  noteDrawer: state => state.noteDrawer,
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
