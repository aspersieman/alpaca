import { saveTask, updateTask, deleteTask, getTasks, getTask, } from '@/api/task'

export const state = {
  task: {
    id: null,
    name: null,
    due: null,
    status: null,
    priority: null,
    order: null,
    timers: []
  },
  taskTemplate: {
    id: null,
    name: null,
    due: null,
    status: null,
    priority: null,
    order: null,
    timers: []
  },
  tasks: [],
  loading: false,
  dialogAdd: false,
  dialogUpdate: false,
  statuses: [
    'complete',
    'ongoing',
    'overdue'
  ],
  priorities: [
    { id: 0, name: '' },
    { id: 1, name: 'High' },
    { id: 2, name: 'Medium' },
    { id: 3, name: 'Low' },
  ],
}

export const mutations = {
  TASK: (state, payload) => {
    state.task = payload?.task || payload;
    let tasks = state.tasks;
    var index = tasks.map((element) => {
      return element.id;
    }).indexOf(state.task.id);
    const remove = payload?.remove || false;
    if (remove === true) {
      tasks.splice(index, 1);
      state.task = state.taskTemplate;
    } else {
      tasks[index] = state.task;
    }
    state.tasks = tasks;
  },
  TASKS: (state, payload) => state.tasks = payload,
  DIALOG_ADD: (state, payload) => state.dialogAdd = payload,
  DIALOG_UPDATE: (state, payload) => state.dialogUpdate = payload,
}

export const actions = {
  saveTask({commit}, payload) {
    return new Promise((resolve, reject) => {
      saveTask(payload)
        .then(({data}) => {
          commit('TASK', data);
          resolve(data)
        })
        .catch((error) => {
          reject(error);
        });
    });
  },

  updateTask({commit}, payload) {
    return new Promise((resolve, reject) => {
      updateTask(payload)
        .then(({data}) => {
          commit('TASK', data);
          resolve(data)
        })
        .catch((error) => {
          reject(error);
        });
    });
  },

  deleteTask({commit}, payload) {
    return new Promise((resolve, reject) => {
      deleteTask(payload.id)
        .then(({data}) => {
          commit('TASK', { task: payload, remove: true });
          resolve(data)
        })
        .catch(error => {
          reject(error);
        });
    });
  },

  getTasks({commit}, payload) {
    return new Promise((resolve, reject) => {
      getTasks(payload)
        .then(({data}) => {
          commit('TASKS', data.data);
          resolve(data)
        })
        .catch(error => reject(error));
    });
  },

  getTask({commit}, payload) {
    return new Promise((resolve, reject) => {
      getTask(payload)
        .then(({data}) => {
          commit('TASK', data.data)
          resolve(data)
        })
        .catch(error => reject(error))
    })
  },

  getTaskPriorityById({getters}, priorityId) {
    let priority = getters['priorities']
      .filter(function(element) {
        return element.id == priorityId;
      })[0];
    priority = priority || { id: 0, name: '' };
    return priority;
  },

  openTaskDialog({commit, getters}, task) {
    if (task) {
      commit('TASK', task);
      commit('DIALOG_UPDATE', true);
    } else {
      commit('TASK', getters['taskTemplate']);
      commit('DIALOG_ADD', true);
    }
  },

  closeTaskDialog({commit, getters}) {
    commit('TASK', getters['taskTemplate']);
    commit('DIALOG_ADD', false);
    commit('DIALOG_UPDATE', false);
  },
}

export const getters = {
  task: state => state.task,
  taskTemplate: state => state.taskTemplate,
  tasks : state => state.tasks,
  dialogAdd: state => state.dialogAdd,
  dialogUpdate: state => state.dialogUpdate,
  statuses: state => state.statuses,
  priorities: state => state.priorities,
  loading: state => state.loading,
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
