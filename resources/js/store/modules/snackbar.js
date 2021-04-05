export const state = {
  show: false,
  message: "",
  color: "",
  duration: 3000,
}

export const mutations = {
  SHOW: (state, payload) => {
    state.show = payload
  },
  MESSAGE: (state, payload) => {
    state.message = payload
  },
  COLOR: (state, payload) => {
    state.color = payload
  },
  DURATION: (state, payload) => {
    state.duration = payload
  },
}

export const actions = {
  reset({commit}) {
    commit('MESSAGE', "");
    commit('COLOR', "");
    commit('DURATION', 3000);
  },

  show({commit, dispatch}, payload) {
    dispatch('reset');
    commit('DURATION', payload.duration || 3000);
    commit('MESSAGE', payload.message || "No message.");
    commit('COLOR', payload.color || "info");
    commit('SHOW', true);
  },

  hide({commit, dispatch}) {
    dispatch('reset');
    commit('SHOW', false);
  },
}

export const getters = {
  show: state => {
    return state.show;
  },
  message: state => {
    return state.message;
  },
  color: state => {
    return state.color;
  },
  duration: state => {
    return state.duration;
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}

