export const state= {
  title: '',
  text: '',
  color: ''
}

export const mutations = {
  showMessage (state, payload) {
    state.title = payload.title;
    state.text = payload.text;
    state.color = payload.color;
  },
}

export const getters = {
  title: state => state.title,
  text: state => state.text,
  color: state => state.color,
}

export default {
  namespaced: true,
  state,
  mutations,
  getters
}
