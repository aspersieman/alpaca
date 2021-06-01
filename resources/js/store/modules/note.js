import {
  saveNote,
  updateNote,
  deleteNote,
  getNotes,
  getNote,
} from '@/api/note'

export const state = {
  note: [],
  noteTemplate: {
    id: null,
    name: null,
    content: null
  },
  notes: []
}

export const mutations = {
  NOTE: (state, payload) => {
    state.note = payload?.note || payload;
    let notes = state.notes;
    var index = notes.map((element) => {
      return element.id;
    }).indexOf(state.note.id);
    const remove = payload?.remove || false;
    if (remove === true) {
      notes.splice(index, 1);
      state.note = state.noteTemplate;
    } else {
      notes[index] = state.note;
    }
    state.notes = notes;
  },
  NOTES: (state, payload) => state.notes = payload,
}

export const actions = {
  saveNote({commit, dispatch}, payload) {
    return new Promise((resolve, reject) => {
      saveNote(payload)
        .then(({data}) => {
          dispatch('task/getTask', data.data.task_id, {root: true})
            .then(({data}) => {
              if (data.task_id) {
                dispatch('task/getTask', data.data.task_id, {root: true})
              }
            });
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
      deleteNote(payload.id)
        .then(({data}) => {
          commit('NOTE', { note: payload, remove: true });
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
      getNote(payload.id)
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
