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
  TASK: (state, payload) => state.task = payload,
  TASKS: (state, payload) => state.tasks = payload,
  DIALOG_ADD: (state, payload) => state.dialogAdd = payload,
  DIALOG_UPDATE: (state, payload) => state.dialogUpdate = payload,
}

export const actions = {
  saveTask({commit, dispatch}, payload) {
    return new Promise((resolve, reject) => {
      const task = {
        name: payload.name,
        due: moment(payload.due).format('YYYY-MM-DD'),
        priority: payload.priority,
        status: 'ongoing'
      };
      saveTask(task)
        .then(({data}) => {
          dispatch('snackbar/show',{
            message: 'Task added',
            color: 'success',
            duration: 1000
          }, { root: true });
          resolve(data)
        })
        .catch((error) => {
          const message = error.response.data.message || 'ERROR';
          dispatch('snackbar/show',{
            message: message,
            color: 'error',
            duration: 3000
          }, { root: true });
          reject(error);
        });
    });
  },

  updateTask({commit, dispatch}, payload) {
    updateTask(payload)
      .then(({data}) => {
        commit('TASK', data);
        dispatch('getTasks');
        resolve(data)
      })
      .catch((error) => {
        dispatch('snackbar/show',{
          message: error.response.data.message,
          color: 'error',
          duration: 3000
        }, { root: true });
        reject(error);
      });
  },

  deleteTask({commit, dispatch}, payload) {
    commit('dialog/dialogShow',{
      type: 'confirm',
      icon: 'warning',
      title: 'Confirm deletion',
      message: 'Are you sure you want to delete this task?',
      okCb: ()=>{
        new Promise((resolve, reject) => {
          deleteTask(payload)
            .then(({data}) => {
              commit('TASK', null);
              dispatch('getTasks');
              dispatch('snackbar/show',{
                message: payload.data.message,
                color: 'success',
                duration: 3000
              }, { root: true });
              resolve(data)
            })
            .catch(error => {
              if (error.response) {
                dispatch('snackbar/show',{
                  message: error.response.data.data.message,
                  color: 'error',
                  duration: 10000
                }, { root: true });
              }
              reject(error);
            })
        });
      },
      cancelCb: ()=>{
        console.log('CANCEL');
      }
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
          commit('TASK', data)
          resolve(data)
        })
        .catch(error => reject(error))
    })
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

  getActiveTask({getters}) {
    const task_id = getters['task'].id == 0 || typeof getters['task'].id == 'undefined' ? null : getters['task'].id;
    let task = getters['tasks']
      .filter(function(element) {
        return element.id === task_id;
      });
    return task.length > 0 ? task[0] : null;
  },

  getTimers({getters}, task, onlyActive) {
    if (onlyActive) {
      let timers = task.timers
        .filter(function(element) {
          if (getters['timer/timerRunningCounter'].timer) {
            return element.id === getters['timer/timerRunningCounter'].timer.id;
          }
        });
      return timers.length > 0 ? [timer[0]] : [];
    }
    return task.timers;
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
