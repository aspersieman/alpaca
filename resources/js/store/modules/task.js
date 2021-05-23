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
  added: null,
  updated: null,
  deleted: null,
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
  ADDED: (state, payload) => state.added = payload,
  UPDATED: (state, payload) => state.updated = payload,
  DELETED: (state, payload) => state.deleted = payload,
  TASKS: (state, payload) => state.tasks = payload,
  DIALOG_ADD: (state, payload) => state.dialogAdd = payload,
  DIALOG_UPDATE: (state, payload) => state.dialogUpdate = payload,
}

export const actions = {
  getTasks({commit}, payload) {
    return new Promise((resolve, reject) => {
      getTasks(payload)
        .then(({data}) => {
          commit('TASKS', data.data);
          resolve(data);
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

  saveTask({commit}, payload) {
    return new Promise((resolve, reject) => {
      saveTask(payload)
        .then(({data}) => {
          commit('TASK', data);
          commit('ADDED', data);
          commit(
            'flash/showMessage',
            {text: 'Task added successfully', color: 'green'},
            {root: true}
          );
          resolve(data)
        })
        .catch((error) => {
          console.error(error);
          commit(
            'flash/showMessage',
            {text: 'Error adding task', color: 'red'},
            {root: true}
          );
          reject(error);
        });
    });
  },

  updateTask({commit}, payload) {
    return new Promise((resolve, reject) => {
      updateTask(payload)
        .then(({data}) => {
          commit('TASK', data);
          commit('UPDATED', data);
          commit(
            'flash/showMessage',
            {text: 'Task updated successfully', color: 'green'},
            {root: true}
          );
          resolve(data)
        })
        .catch((error) => {
          console.error(error);
          commit(
            'flash/showMessage',
            {text: 'Error updating task', color: 'red'},
            {root: true}
          );
          reject(error);
        });
    });
  },

  deleteTask({commit, dispatch}, payload) {
    return new Promise((resolve, reject) => {
      dispatch('dialog/dialogShow',{
        type: 'confirm',
        icon: 'warning',
        title: 'Confirm deletion',
        message: 'Are you sure you want to delete this task?',
        okCb: () => {
          dispatch('dialog/dialogHide', null, {root: true});
          deleteTask(payload)
            .then(({data}) => {
              commit('TASK', { task: payload, remove: true });
              commit('DELETED', data);
              commit(
                'flash/showMessage',
                {text: 'Task deleted successfully', color: 'green'},
                {root: true}
              );
              dispatch('dialog/dialogHide', null, {root: true});
              resolve(data)
            })
            .catch(error => {
              console.error(error)
              commit(
                'flash/showMessage',
                {text: 'Error occurred deleting task', color: 'red'},
                {root: true}
              );
              reject(error);
            });
        },
        cancelCb: ()=>{
          dispatch('dialog/dialogHide', null, {root: true});
        }
      },
      {root: true});
    });
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

  changeTaskStatus({dispatch}, task) {
    var newStatus = null;
    switch (task.status) {
      case 'ongoing':
        newStatus = 'complete';
        break;
      case 'complete':
        newStatus = 'overdue';
        break;
      case 'overdue':
        newStatus = 'ongoing';
        break;
      default:
        break;
    }
    if (newStatus) {
      task.status = newStatus
      dispatch('updateTask', task);
    }
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
