export const state= {
  overlay: false,
}

export const mutations = {
  showOverlay (state, payload) {
    state.overlay = payload.show;
  }
}

export const getters = {
  overlay: state => state.overlay,
}

export default {
  namespaced: true,
  state,
  mutations,
  getters
}
