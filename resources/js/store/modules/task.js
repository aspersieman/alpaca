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
  taskDialogAdd: false,
  taskDialogUpdate: false,
  statuses: [
    { id: 'complete', name: 'Complete' },
    { id: 'inprogress', name: 'In progress' },
    { id: 'todo', name: 'To do' },
  ],
  priorities: [
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
  ADDED: (state, payload) => { state.added = payload },
  UPDATED: (state, payload) => { state.updated = payload },
  DELETED: (state, payload) => { state.deleted = payload },
  TASKS: (state, payload) => { state.tasks = payload },
  TASK_DIALOG_ADD: (state, payload) => { state.taskDialogAdd = payload },
  TASK_DIALOG_UPDATE: (state, payload) => { state.taskDialogUpdate = payload },
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
      payload.status = payload.status.toLowerCase();
      saveTask(payload)
        .then(({data}) => {
          commit('TASK', data.data);
          commit('ADDED', data.data);
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
      payload.status = payload.status.id.toLowerCase();
      updateTask(payload)
        .then(({data}) => {
          console.log('updateTask:');
          console.log(data.data);
          commit('TASK', data.data);
          commit('UPDATED', data.data);
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
      const settings = {
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
      };
      dispatch('dialog/dialogShow', settings, {root: true});
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
      commit('TASK_DIALOG_UPDATE', true);
    } else {
      commit('TASK', getters['taskTemplate']);
      commit('TASK_DIALOG_ADD', true);
    }
  },

  closeTaskDialog({commit, getters}) {
    commit('TASK', getters['taskTemplate']);
    commit('TASK_DIALOG_ADD', false);
    commit('TASK_DIALOG_UPDATE', false);
  },

  changeTaskStatus({dispatch}, payload) {
    const newStatus = payload.newStatus || null;
    const task = payload.task || null;
    if (newStatus && task) {
      task.status = newStatus
      dispatch('updateTask', task);
    }
  },
}

export const getters = {
  task: state => state.task,
  taskTemplate: state => state.taskTemplate,
  tasks : state => state.tasks,
  taskDialogAdd: state => state.taskDialogAdd,
  taskDialogUpdate: state => state.taskDialogUpdate,
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
