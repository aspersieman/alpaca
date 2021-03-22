import { update, getUserMain } from '@/api/user'

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

    getUserMain({commit}, payload) {
        return new Promise((resolve, reject) => {
            getUserMain(payload)
                .then(({data}) => {
                    commit('USER_MAIN', data.user)
                    resolve(data)
                })
                .catch(error => reject(error))
        })
    }
}

export const getters = {
    userMain : state => {
        return state.userMain
    }
}

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
