import {
  saveNote,
  updateNote,
  deleteNote,
  getNotes,
  getNote,
} from '@/api/note'

export const state = {
  note: [],
  notes: []
}

export const mutations = {
  NOTE: (state, payload) => state.note = payload,
  NOTES: (state, payload) => state.notes = payload,
}

export const actions = {
  saveNote({commit}, payload) {
    return new Promise((resolve, reject) => {
      saveNote(payload)
        .then(({data}) => {
          commit('NOTE', data);
          resolve(data)
        })
        .catch(error => reject(error))

    })
  },

  updateNote({commit}, payload) {
    return new Promise((resolve, reject) => {
      updateNote(payload)
        .then(({data}) => {
          commit('NOTE', data);
          resolve(data)
        })
        .catch(error => reject(error))

    })
  },

  deleteNote({commit}, payload) {
    return new Promise((resolve, reject) => {
      deleteNote(payload)
        .then(({data}) => {
          commit('NOTE', null);
          resolve(data)
        })
        .catch(error => reject(error))
    })
  },
  getNotes({commit}, payload) {
    return new Promise((resolve, reject) => {
      getNotes(payload)
        .then(({data}) => {
          commit('NOTES', data.data);
          resolve(data)
        })
        .catch(error => reject(error))
    })
  },

  getNote({commit}, payload) {
    return new Promise((resolve, reject) => {
      getNote(payload)
        .then(({data}) => {
          commit('NOTE', data)
          resolve(data)
        })
        .catch(error => reject(error))
    })
  },
}

export const getters = {
  note: state => state.note,
  notes : state => state.notes,
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
