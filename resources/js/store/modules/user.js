import { saveUser, updateUser, updateAvatar, deleteUser, getUsers, getUser, } from '@/api/user'

export const state = {
  userMain: {
    id: null,
    name: null,
    email: null,
    nickname: null,
    avatar: null,
    bio: null,
    main_user_yn: null
  },
  user: null,
  added: null,
  updated: null,
  deleted: null,
  users: [],
  usersTotal: 0,
}

export const mutations = {
  USER_MAIN: (state, payload) => {
    state.userMain = payload
  },
  USER: (state, payload) => {
    state.user = payload?.user || payload;
    let users = state.users;
    var index = users.map((element) => {
      return element.id;
    }).indexOf(state.user.id);
    const remove = payload?.remove || false;
    if (remove === true) {
      users.splice(index, 1);
      state.user = state.userTemplate;
    } else {
      users[index] = state.user;
    }
    state.users = users;
  },
  ADDED: (state, payload) => { state.added = payload },
  UPDATED: (state, payload) => { state.updated = payload },
  DELETED: (state, payload) => { state.deleted = payload },
  USERS: (state, payload) => { state.users = payload },
  USERS_TOTAL: (state, payload) => { state.usersTotal = payload },
}

export const actions = {
  getUsers({commit}, payload) {
    return new Promise((resolve, reject) => {
      getUsers(payload)
        .then(({data}) => {
          commit('USERS', data.data.data);
          commit('USERS_TOTAL', data.data.total);
          resolve(data);
        })
        .catch(error => reject(error));
    });
  },

  getUser({commit}, payload) {
    return new Promise((resolve, reject) => {
      getUser(payload)
        .then(({data}) => {
          commit('USER', data.data)
          resolve(data)
        })
        .catch(error => reject(error))
    })
  },

  saveUser({commit}, payload) {
    return new Promise((resolve, reject) => {
      saveUser(payload)
        .then(({data}) => {
          commit('USER', data);
          commit('ADDED', data);
          commit(
            'flash/showMessage',
            {text: 'User added successfully', color: 'green'},
            {root: true}
          );
          resolve(data)
        })
        .catch((error) => {
          console.error(error);
          commit(
            'flash/showMessage',
            {text: 'Error adding user', color: 'red'},
            {root: true}
          );
          reject(error);
        });
    });
  },

  updateUser({commit}, payload) {
    return new Promise((resolve, reject) => {
      updateUser(payload)
        .then(({data}) => {
          commit('USER', data);
          commit('UPDATED', data);
          commit(
            'flash/showMessage',
            {text: 'User updated successfully', color: 'green'},
            {root: true}
          );
          resolve(data)
        })
        .catch((error) => {
          console.error(error);
          commit(
            'flash/showMessage',
            {text: 'Error updating user', color: 'red'},
            {root: true}
          );
          reject(error);
        });
    });
  },

  updateAvatar({commit}, payload) {
    return new Promise((resolve, reject) => {
      updateAvatar(payload.id, payload.payload)
        .then(({data}) => {
          commit('USER', data);
          commit('UPDATED', data);
          commit(
            'flash/showMessage',
            {text: 'User updated successfully', color: 'green'},
            {root: true}
          );
          resolve(data)
        })
        .catch((error) => {
          console.error(error);
          commit(
            'flash/showMessage',
            {text: 'Error updating user', color: 'red'},
            {root: true}
          );
          reject(error);
        });
    });
  },

  updateActive({commit}, payload) {
    return new Promise((resolve, reject) => {
      updateUser(payload)
        .then(({data}) => {
          console.log('updateActive store')
          console.log(data)
          commit('auth/SET_AUTH_USER', data.data, { root: true })
          resolve(data)
        })
        .catch(error => reject(error))
    })
  },

  deleteUser({commit, dispatch}, payload) {
    return new Promise((resolve, reject) => {
      const settings = {
        type: 'confirm',
        icon: 'warning',
        title: 'Confirm deletion',
        message: 'Are you sure you want to delete this user?',
        okCb: () => {
          dispatch('dialog/hide', null, {root: true});
          deleteUser(payload)
            .then(({data}) => {
              commit('USER', { user: payload, remove: true });
              commit('DELETED', data);
              commit(
                'flash/showMessage',
                {text: 'User deleted successfully', color: 'green'},
                {root: true}
              );
              resolve(data)
            })
            .catch(error => {
              console.error(error)
              commit(
                'flash/showMessage',
                {text: 'Error occurred deleting user', color: 'red'},
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
}

export const getters = {
  user: state => state.user,
  users : state => state.users,
  usersTotal : state => state.usersTotal,
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
