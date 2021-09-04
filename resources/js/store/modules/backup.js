import { getBackups, deleteBackup, saveBackup } from '@/api/backup';

export const state = {
  backups: [],
  added: null,
  deleted: null,
  backupsTotal: 0,
}

export const mutations = {
  BACKUPS: (state, payload) => {
    state.backups = payload
  },
  BACKUPS_TOTAL: (state, payload) => {
    state.backupsTotal = payload
  },
  ADDED: (state, payload) => { state.added = payload },
  DELETED: (state, payload) => { state.deleted = payload },
}

export const actions = {
  getBackups({commit}, payload) {
    return new Promise((resolve, reject) => {
      getBackups(payload)
        .then(({data}) => {
          commit('BACKUPS', data.data);
          commit('BACKUPS_TOTAL', data.length);
          resolve(data)
        })
        .catch(error => reject(error))
    })
  },

  saveBackup({commit}, payload = {}) {
    return new Promise((resolve, reject) => {
      saveBackup(payload)
        .then(({data}) => {
          commit('ADDED', data);
          commit(
            'flash/showMessage',
            {text: 'Backup added successfully', color: 'green'},
            {root: true}
          );
          resolve(data)
        })
        .catch((error) => {
          console.error(error);
          commit(
            'flash/showMessage',
            {text: 'Error adding backup', color: 'red'},
            {root: true}
          );
          reject(error);
        });
    });
  },

  deleteBackup({commit, dispatch}, payload) {
    return new Promise((resolve, reject) => {
      const settings = {
        type: 'confirm',
        icon: 'warning',
        title: 'Confirm deletion',
        message: 'Are you sure you want to delete this backup?',
        okCb: () => {
          dispatch('dialog/dialogHide', null, {root: true});
          deleteBackup(payload)
            .then(() => {
              commit('DELETED', payload);
              commit(
                'flash/showMessage',
                {text: 'Backup deleted successfully', color: 'green'},
                {root: true}
              );
              resolve()
            })
            .catch(error => {
              console.error(error)
              commit(
                'flash/showMessage',
                {text: 'Error occurred deleting backup', color: 'red'},
                {root: true}
              );
              reject(error);
            });
        },
        cancelCb: ()=>{
          dispatch('dialog/dialogHide', null, {root: true});
        }
      };
      dispatch('dialog/dialogShow', settings, {root: true});
    });
  },
}

export const getters = {
  backups : state => {
    return state.backups
  },
  backupsTotal : state => {
    return state.backupsTotal
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}

