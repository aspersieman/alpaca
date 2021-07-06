import {
  saveNote,
  updateNote,
  deleteNote,
  getNotes,
  getNote,
} from '@/api/note'

export const state = {
  note: [],
  added: null,
  updated: null,
  deleted: null,
  noteTemplate: {
    id: null,
    name: null,
    content: null
  },
  notes: [],
  noteDialogAdd: false,
  noteDialogUpdate: false,
}

export const mutations = {
  NOTE: (state, payload) => {
    state.note = payload?.note || payload;
    let notes = state.notes;
    var index = notes.map((element) => {
      return element.id;
    }).indexOf(state.note?.id);
    const remove = payload?.remove || false;
    if (remove === true) {
      notes.splice(index, 1);
      state.note = state.noteTemplate;
    } else {
      notes[index] = state.note;
    }
    state.notes = notes;
  },
  NOTES: (state, payload) => { state.notes = payload },
  ADDED: (state, payload) => { state.added = payload },
  UPDATED: (state, payload) => { state.updated = payload },
  DELETED: (state, payload) => { state.deleted = payload },
  NOTE_DIALOG_ADD: (state, payload) => { state.noteDialogAdd = payload },
  NOTE_DIALOG_UPDATE: (state, payload) => { state.noteDialogUpdate = payload },
}

export const actions = {
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

  saveNote({commit, dispatch}, payload) {
    return new Promise((resolve, reject) => {
      saveNote(payload)
        .then(({data}) => {
          dispatch('task/getTask', data.data.task_id, {root: true})
            .then(() => {
              commit(
                'flash/showMessage',
                {text: 'Note added successfully', color: 'green'},
                {root: true}
              );
              commit('NOTE', data);
              commit('ADDED', data);
            });
          resolve(data)
        })
        .catch((error) => {
          console.error(error);
          commit(
            'flash/showMessage',
            {text: 'Error adding note', color: 'red'},
            {root: true}
          );
          reject(error);
        });
    })
  },

  updateNote({commit, dispatch}, payload) {
    return new Promise((resolve, reject) => {
      updateNote(payload)
        .then(({data}) => {
          dispatch('task/getTask', data.data.task_id, {root: true})
            .then(() => {
              commit(
                'flash/showMessage',
                {text: 'Note updated successfully', color: 'green'},
                {root: true}
              );
              commit('NOTE', data);
              commit('UPDATED', data);
            });
          resolve(data)
        })
        .catch((error) => {
          console.error(error);
          commit(
            'flash/showMessage',
            {text: 'Error updating note', color: 'red'},
            {root: true}
          );
          reject(error);
        });
    })
  },

  deleteNote({commit, dispatch}, payload) {
    return new Promise((resolve, reject) => {
      const settings = {
        type: 'confirm',
        icon: 'warning',
        title: 'Confirm deletion',
        message: 'Are you sure you want to delete this note?',
        okCb: ()=>{
          dispatch('dialog/dialogHide', null, {root: true});
          deleteNote(payload)
            .then(({data}) => {
              dispatch('task/getTask', payload.task_id, {root: true}).
                then(() => {
                  commit(
                    'flash/showMessage',
                    {text: 'Note deleted successfully', color: 'green'},
                    {root: true}
                  );
                  commit('NOTE', { note: payload, remove: true });
                  commit('DELETED', data);
                });
              resolve(data)
            }).catch((error) => {
              console.error(error)
              commit(
                'flash/showMessage',
                {text: 'Error occurred deleting note', color: 'red'},
                {root: true}
              );
              reject(error);
            })
        },
        cancelCb: ()=>{
          dispatch('dialog/dialogHide', null, {root: true});
        }
      };
      dispatch('dialog/dialogShow', settings, {root: true});
    });
  },

  openNoteDialog({commit, getters}, note) {
    if (note) {
      commit('NOTE', note);
      commit('NOTE_DIALOG_UPDATE', true);
    } else {
      commit('NOTE', getters['noteTemplate']);
      commit('NOTE_DIALOG_ADD', true);
    }
  },

  closeNoteDialog({commit, getters}) {
    commit('NOTE', getters['noteTemplate']);
    commit('NOTE_DIALOG_ADD', false);
    commit('NOTE_DIALOG_UPDATE', false);
  },
}

export const getters = {
  note: state => state.note,
  notes : state => state.notes,
  noteDialogAdd: state => state.noteDialogAdd,
  noteDialogUpdate: state => state.noteDialogUpdate,
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
