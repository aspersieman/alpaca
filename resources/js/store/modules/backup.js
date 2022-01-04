import { getBackups, deleteBackup, saveBackup, isGoogleDriveAuthenticated, sendToDrive } from '@/api/backup';

export const state = {
  backups: [],
  added: null,
  deleted: null,
  backupsTotal: 0,
  isGoogleDriveAuthenticated: false,
  googleDriveLoginURL: false,
  sendingToDrive: false,
  uploadToDriveAuthCode: null,
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
  IS_GOOGLE_DRIVE_AUTHENTICATED: (state, payload) => { state.isGoogleDriveAuthenticated = payload },
  GOOGLE_DRIVE_LOGIN_URL: (state, payload) => { state.googleDriveLoginURL = payload },
  SENDING_TO_DRIVE: (state, payload) => { state.sendingToDrive = payload },
  UPLOAD_TO_DRIVE_AUTH_CODE: (state, payload) => { state.uploadToDriveAuthCode = payload },
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
          dispatch('dialog/hide', null, {root: true});
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
          dispatch('dialog/hide', null, {root: true});
        }
      };
      dispatch('dialog/show', settings, {root: true});
    });
  },

  isGoogleDriveAuthenticated({commit}, payload) {
    return new Promise((resolve, reject) => {
      isGoogleDriveAuthenticated(payload)
        .then(({data}) => {
          commit('IS_GOOGLE_DRIVE_AUTHENTICATED', data.data?.is_authenticated ?? false);
          commit('GOOGLE_DRIVE_LOGIN_URL', data.data?.drive_login_url ?? false);
          commit('UPLOAD_TO_DRIVE_AUTH_CODE', data.data?.upload_to_drive_auth_code ?? false);
          resolve(data)
        })
        .catch(error => reject(error))
    })
  },

  sendToDrive({commit}, payload) {
    console.log('sendToDrive');
    commit('SENDING_TO_DRIVE', true);
    return new Promise((resolve, reject) => {
      sendToDrive(payload)
        .then(({data}) => {
          commit(
            'flash/showMessage',
            {text: 'Upload to Google Drive successful', color: 'green'},
            {root: true}
          );
          commit('SENDING_TO_DRIVE', false);
          resolve(data)
        })
        .catch(error => {
          commit(
            'flash/showMessage',
            {text: 'Error occurred during upload to Google Drive', color: 'red'},
            {root: true}
          );
          commit('SENDING_TO_DRIVE', false);
          reject(error);
        })
    })
  },
}

export const getters = {
  backups: state => {
    return state.backups;
  },
  backupsTotal: state => {
    return state.backupsTotal;
  },
  isGoogleDriveAuthenticated: state => {
    return state.isGoogleDriveAuthenticated;
  },
  googleDriveLoginURL: state => {
    return state.googleDriveLoginURL;
  },
  sendingToDrive: state => {
    return state.sendingToDrive;
  },
  uploadToDriveAuthCode: state => {
    return state.uploadToDriveAuthCode;
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}

