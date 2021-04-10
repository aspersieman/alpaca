export const state = {
  loaderShow: false,
}

export const mutations = {
  LOADER_SHOW: (state, payload) => {
    state.loaderShow = payload
  },
}

export const actions = {
  showLoader({commit}) {
    commit('LOADER_SHOW', true);
  },

  hideLoader({commit}) {
    commit('LOADER_SHOW', false);
  },
}

export const getters = {
  loaderShow: state => {
    return state.loaderShow;
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}

