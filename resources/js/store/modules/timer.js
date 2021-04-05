import { stopTimer, getRunningTimer, deleteTimer, saveTimer, updateTimer } from '@/api/timer';
import TimerMixin from '@/mixins/TimerMixin';

export const state = {
  timer: null,
  timerRunning: null,
  timerRunningCounter: {
    seconds: 0,
    timer: {
      id: null,
      name: null,
      task_id: null, 
      user_id: null,
      started_at: null,
      stopped_at: null, 
      duration: 0,
      created_at: null,
      updated_at: null,
    },
    title: 'Timing...',
    task: {
      id: null,
      name: null,
      due: null,
      status: null,
      order: null,
    }
  },
  timerStopped: null,
  timerStoppedValue: "",
  timerDialogAdd: false,
  timerDialogUpdate: false
}

export const mutations = {
  TIMER: (state, payload) => {
    state.timer = payload
  },
  TIMER_RUNNING: (state, payload) => {
    state.timerRunning = payload
  },
  TIMER_RUNNING_COUNTER: (state, payload) => {
    state.timerRunningCounter = payload
  },
  TIMER_STOPPED: (state, payload) => {
    state.timerStopped = payload
  },
  TIMER_STOPPED_VALUE: (state, payload) => {
    state.timerStoppedValue = payload
  },
  TIMER_DIALOG_ADD: (state, payload) => {
    state.timerDialogAdd = payload
  },
  TIMER_DIALOG_UPDATE: (state, payload) => {
    state.timerDialogUpdate = payload
  },
}

export const actions = {
  /**
   * Sets active timer and opens the timer add or update dialog
   */
  openTimerDialog({commit}, timer) {
    commit('TIMER', timer);
    if (timer) {
      commit('TIMER_DIALOG_UPDATE', true);
    } else {
      commit('TIMER_DIALOG_ADD', true);
    }
  },

  /**
   * Determines if there is a running timer and whether it belongs to the task
   * passed into the function.
   */
  showTimerForTask({getters}, task, timer) {
    return getters['timerRunningCounter'].timer &&
      getters['timerRunningCounter'].timer.id === timer.id &&
      getters['timerRunningCounter'].timer.task.id === task.id
  },

  /**
   * Checks if there is a running timer
   */
  isRunningTimer({getters}) {
    return typeof getters['timerRunningCounter'].timer.id != 'undefined'
      && getters['timerRunningCounter'].timer.id != null
      && getters['timerRunningCounter'].timer.id != 0;
  },

  /**
   * Start counting the timer. Tick tock.
   */
  startTimer({commit, getters}) {
    if (getters['timerRunningCounter'].timer.id === null || getters['timerRunningCounter'].timer.id != getters['timerRunning'].id) {
      // remove 'z' from timezone, moment doesn't like it
      // when doing maths
      const started = window.moment(getters['timerRunning'].started_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
      const now = window.moment();
      commit('TIMER_RUNNING_COUNTER', {
        seconds: parseInt(window.moment.duration(now.diff(started)).asSeconds()), 
        timer: getters['timerRunning'],
        title: 'Timing...',
        task: getters['timerRunning'].task
      });
      getters['timerRunningCounter'].ticker = setInterval(() => {
        const from = window.moment(getters['timerRunning'].timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
        const to = window.moment();
        const time = TimerMixin.methods.readableTimeFromSeconds(from, to);
        let timerRunningCounter = getters['timerRunningCounter'];
        timerRunningCounter.title = `${time.hours}:${time.minutes}:${time.seconds}`; 
        commit('TIMER_RUNNING_COUNTER', timerRunningCounter);
      }, 1000);
    }
  },

  /**
   * Sets the currently running timer
   */
  setRunningTimer({commit, dispatch}, timer = null) {
    if (timer) {
      commit('TIMER_RUNNING', timer);
    } else {
      return new Promise((resolve, reject) => {
        getRunningTimer()
          .then(({data}) => {
            commit('TIMER_RUNNING', data.data);
            resolve(data);
          }).then(() => {
            dispatch('startTimer');
          }).catch(error => {
            reject(error);
          })
      });
    }
  },

  /**
   * Stop the timer from the API and then from the local timerRunningCounter.
   */
  stopTimer({commit, getters, dispatch}) {
    if (getters['timerRunning'] != null) {
      const timerId = getters['timerRunningCounter'].timer.id;
      return new Promise((resolve, reject) => {
        stopTimer(timerId)
          .then(({data}) => {
            clearInterval(getters['timerRunningCounter'].ticker)
            commit('TIMER_STOPPED', data);
            commit('TIMER_RUNNING', null);
            commit('TIMER_STOPPED_VALUE', TimerMixin.methods.calculateTimeSpent(data));
            const timerRunningCounter = {
              seconds: 0,
              timer: {
                id: null,
                name: null,
                task_id: null, 
                user_id: null,
                started_at: null,
                stopped_at: null, 
                duration: 0,
                created_at: null,
                updated_at: null,
              },
              title: 'Timing...',
              task: {
                id: null,
                name: null,
                due: null,
                status: null,
                order: null,
              }
            };
            commit('TIMER_RUNNING_COUNTER', timerRunningCounter);
            dispatch('task/getTasks');
            resolve(data)
          }).catch(error => {
            reject(error)
          })
      });
    }
  },

  /**
   * Delete a timer.
   */
  deleteTimer({commit, dispatch}, task, timer) {
    const settings = {
      type: 'confirm',
      icon: 'warning',
      title: 'Confirm deletion',
      message: 'Are you sure you want to delete this timer?',
      okCb: ()=>{
        new Promise((resolve, reject) => {
          deleteTimer(task.id, timer.id)
            .then(({data}) => {
              task.timers = task.timers.filter(function(element) {
                return element.id !== timer.id;
              });
              commit('TASK', task);
              dispatch('snackbar/snackbarShow', {
                message: response.data.message,
                color: 'success',
                duration: 3000
              });
              dispatch('task/getTasks');
              resolve(data);
            }).catch(error => {
              reject(error);
            })
        });
      },
      cancelCb: ()=>{
        console.log('CANCEL');
      }
    };
    dispatch('dialog/dialogShow', settings);
  },

  saveTimer({getters, dispatch}, timer) {
    const task = getters['task/task'];
    if(timer.name.length > 0) {
      return new Promise((resolve, reject) => {
        const timer = {
          name: timer.name,
          task_id: timer.task_id,
          tz: Intl.DateTimeFormat().resolvedOptions().timeZone,
          tags: timer.tags
        };
        saveTimer(task.id, timer)
          .then(({data}) => {
            dispatch('setRunningTimer', data.data);
            dispatch('task/getTasks');
            dispatch('snackbar/snackbarShow', {
              message: 'Timer added',
              color: 'success',
              duration: 1000
            });
            resolve(data);
          }).then(() => {
            dispatch('startTimer');
          }).catch(error => {
            reject(error);
          })
      });
    }
  },

  updateTimer({commit, dispatch}, payload) {
    const started_at = window.moment(payload.startDate + ' ' + payload.startTime, 'YYYY-MM-DD HH:mm:ss');
    const timer = {
      name: payload.name,
      started_at: started_at.format('YYYY-MM-DD HH:mm:ss'),
      stopped_at: started_at.add(payload.duration, 'minutes').format('YYYY-MM-DD HH:mm:ss'),
      tags: payload.tags
    };

    return new Promise((resolve, reject) => {
      updateTimer(taskId, timer)
        .then(() => {
          commit('snackbar/snackbarShow',{
            message: 'Timer updated',
            color: 'success',
            duration: 1000
          });
          dispatch('task/getTasks');
          resolve(data);
        }).catch(error => {
          console.log(error);
          commit('snackbar/snackbarShow',{
            message: error.response.data.message,
            color: 'error',
            duration: 3000
          });
          reject(error);
        });
    });
  },
}

export const getters = {
  timer : state => {
    return state.timer
  },
  timerRunning : state => {
    return state.timerRunning
  },
  timerRunningCounter : state => {
    return state.timerRunningCounter
  },
  timerStopped : state => {
    return state.timerStopped
  },
  timerStoppedValue : state => {
    return state.timerStoppedValue
  },
  timerDialogAdd : state => {
    return state.timerDialogAdd
  },
  timerDialogUpdate : state => {
    return state.timerDialogUpdate
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
