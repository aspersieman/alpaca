import { update } from '@/api/user'

export const state = {
  userMain: {
    id: null,
    name: null,
    email: null,
    nickname: null,
    avatar: null,
    bio: null,
    main_user_yn: null
  }
}

export const mutations = {
  USER_MAIN: (state, payload) => {
    state.userMain = payload
  }
}

export const actions = {
  update({commit}, payload) {
    return new Promise((resolve, reject) => {
      update(payload)
        .then(({data}) => {
          commit('auth/SET_AUTH_USER', data.user, { root: true })
          resolve(data)
        })
        .catch(error => reject(error))
    })
  },
}

export const getters = {
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
