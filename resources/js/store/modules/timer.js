import { stopTimer, getTimerRunning, deleteTimer, saveTimer, updateTimer } from '@/api/timer';
import TimerMixin from '@/mixins/TimerMixin';
import { duration } from '@/plugins/formatters';

export const state = {
  timer: null,
  timerRunning: {
    seconds: 0,
    title: 'Timing...',
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
  },
  timerStopped: null,
  timerStoppedValue: "",
  timerDialogAdd: false,
  timerDialogUpdate: false,
  ticker: null,
}

export const mutations = {
  TIMER: (state, payload) => { state.timer = payload; },
  TIMER_RUNNING: (state, payload) => { state.timerRunning = payload; },
  TIMER_STOPPED: (state, payload) => { state.timerStopped = payload; },
  TIMER_STOPPED_VALUE: (state, payload) => { state.timerStoppedValue = payload; },
  TIMER_DIALOG_ADD: (state, payload) => { state.timerDialogAdd = payload; },
  TIMER_DIALOG_UPDATE: (state, payload) => { state.timerDialogUpdate = payload; },
  TICKER: (state, payload) => { state.ticker = payload; },
}

export const actions = {
  saveTimer({commit}, payload) {
    return new Promise((resolve, reject) => {
      saveTimer(payload)
        .then((data) => {
          commit('TIMER', data);
          resolve(data);
        }).catch(error => {
          reject(error);
        })
    });
  },

  updateTimer({commit}, payload) {
    return new Promise((resolve, reject) => {
      updateTimer(payload)
        .then((data) => {
          commit('TIMER', data);
          resolve(data);
        }).catch(error => {
          reject(error);
        });
    });
  },

  deleteTimer({commit}, payload) {
    deleteTimer(payload)
      .then(({data}) => {
        commit('TIMER', null);
        resolve(data)
      })
      .catch(error => {
        reject(error);
      });
  },

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
   * Start counting the timer. Tick tock.
   */
  startTimer({commit, getters, dispatch}, timer) {
    if (getters['ticker'] === null) {
      dispatch('getTimerRunning', timer)
        .then(() => {
          if(getters['isTimerRunning']) {
            // remove 'z' from timezone, moment doesn't like it
            // when doing maths
            const started = window.moment(getters['timerRunning'].timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
            const now = window.moment();
            commit('TIMER_RUNNING', {
              seconds: parseInt(window.moment.duration(now.diff(started)).asSeconds()),
              title: 'Timing...',
              timer: getters['timerRunning'].timer,
            });
            commit('TICKER', setInterval(() => {
              const from = window.moment(getters['timerRunning'].timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
              const to = window.moment();
              const seconds = parseInt(window.moment.duration(to.diff(from)).asSeconds())
              const timer = getters['timerRunning'].timer;
              commit('TIMER_RUNNING', {
                seconds: seconds,
                title: duration(seconds),
                timer: timer
              });
            }, 1000));
          }
        });
    }
  },

  getTimerRunning({commit}, timer) {
    return new Promise((resolve, reject) => {
      if (timer != null && typeof timer != 'undefined') {
        commit('TIMER_RUNNING', {
          seconds: 0,
          title: 'Timing...',
          timer: getters['timerRunning'].timer,
        });
        resolve();
      } else {
        getTimerRunning()
          .then(({data}) => {
            if (typeof data?.data != 'undefined') {
              commit('TIMER_RUNNING', {
                seconds: 0,
                title: 'Timing...',
                timer: data.data,
              });
            }
            resolve(data);
          }).catch(error => {
            reject(error);
          })
      }
    });
  },

  /**
   * Stop the timer from the API and then from the local timerRunning.
   */
  stopTimer({state, commit, getters, dispatch}) {
    if (getters['isTimerRunning']) {
      clearInterval(state.ticker);
      return new Promise((resolve, reject) => {
        const timerId = getters['timerRunning'].timer.id;
        stopTimer(timerId)
          .then(({data}) => {
            dispatch('resetTimerRunning');
            commit('TICKER', null);
            commit('TIMER_STOPPED', data);
            commit('TIMER_STOPPED_VALUE', TimerMixin.methods.timerDuration(data));
            resolve(data)
          }).catch(error => {
            reject(error)
          })
      });
    }
  },

  resetTimerRunning({commit}) {
    const timerRunning = {
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
      },
    };
    commit('TIMER_RUNNING', timerRunning);
    commit('TICKER', null);
  }
}

export const getters = {
  timer : state => { return state.timer },
  timerRunning : state => { return state.timerRunning },
  timerStopped : state => { return state.timerStopped },
  timerStoppedValue : state => { return state.timerStoppedValue },
  timerDialogAdd : state => { return state.timerDialogAdd },
  timerDialogUpdate : state => { return state.timerDialogUpdate },
  ticker : state => { return state.ticker },
  isTimerRunning : state => {
    return state.timerRunning?.timer?.id != null
      && state.timerRunning?.timer?.id != 0;
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
