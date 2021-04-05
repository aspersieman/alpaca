import { getMenu } from '@/api/menu';

export const state = {
  menu: []
}

export const mutations = {
  MENU: (state, payload) => {
    state.menu = payload.data
  }
}

export const actions = {
  getMenu({commit}, payload) {
    return new Promise((resolve, reject) => {
      getMenu(payload)
        .then(({data}) => {
          commit('MENU', data)
          resolve(data)
        })
        .catch(error => reject(error))
    })
  },
}

export const getters = {
  menu : state => {
    return state.menu
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}

