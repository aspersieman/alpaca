import {
  getChartByTimer,
  getChartByDay,
  getChartByTag,
  getChartByCompleted,
  getChartStats
} from '@/api/chart'

import {
  dateTimeFormat,
  duration,
  secondsToMinutes,
  secondsToHours
} from '@/formatters';

export const state = {
  chartByDayLoading: false,
  chartByDayHigh: 200,
  chartByDayData: { 'labels': [], 'series': [[]] },
  chartByDayAge: '',

  chartByTagLoading: false,
  chartByTagData: [],
  chartByTagAge: '',

  chartByCompletedLoading: false,
  chartByCompletedHigh: 15,
  chartByCompletedData: { 'labels': [], 'series': [[]] },
  chartByCompletedAge: '',

  chartStatsTotalTasksCount: 0,
  chartStatsDueTasksCount: 0,
  chartStatsTotalTimerDuration: 0,
  chartStatsTotalNotesCount: 0,
}

export const mutations = {
  CHART_BY_DAY_LOADING: (state, payload) => {
    state.chartByDayLoading = payload;
  },
  CHART_BY_DAY_HIGH: (state, payload) => {
    state.chartByDayHigh = payload;
  },
  CHART_BY_DAY_DATA: (state, payload) => {
    state.chartByDayData = payload;
  },
  CHART_BY_DAY_AGE: (state, payload) => {
    state.chartByDayAge = payload;
  },

  CHART_BY_TAG_LOADING: (state, payload) => {
    state.chartByTagLoading = payload;
  },
  CHART_BY_TAG_DATA: (state, payload) => {
    state.chartByTagData = payload;
  },
  CHART_BY_TAG_AGE: (state, payload) => {
    state.chartByTagAge = payload;
  },

  CHART_BY_COMPLETED_LOADING: (state, payload) => {
    state.chartByCompletedLoading = payload;
  },
  CHART_BY_COMPLETED_HIGH: (state, payload) => {
    state.chartByCompletedHigh = payload;
  },
  CHART_BY_COMPLETED_DATA: (state, payload) => {
    state.chartByCompletedData = payload;
  },
  CHART_BY_COMPLETED_AGE: (state, payload) => {
    state.chartByCompletedAge = payload;
  },

  CHART_STATS_TOTAL_TASKS_COUNT: (state, payload) => {
    state.chartStatsTotalTasksCount = payload;
  },
  CHART_STATS_DUE_TASKS_COUNT: (state, payload) => {
    state.chartStatsDueTasksCount = payload;
  },
  CHART_STATS_TOTAL_TIMER_DURATION: (state, payload) => {
    state.chartStatsTotalTimerDuration = payload;
  },
  CHART_STATS_TOTAL_NOTES_COUNT: (state, payload) => {
    state.chartStatsTotalNotesCount = payload;
  },
}

export const actions = {
  getChartByDayData({commit, state}, payload) {
    return new Promise((resolve, reject) => {
      commit('CHART_BY_DAY_LOADING', true);
      getChartByDay(payload)
        .then(({data}) => {
          let days = data.data;
          let labels = [];
          let series = [];
          let high = state.chartByDayHigh;
          days.forEach(day => {
            let formattedDay = dateTimeFormat(day[0], "ddd");
            let seconds = secondsToMinutes(day[1]);
            high = seconds > high ? seconds : high;
            labels.push(formattedDay);
            series.push({
              meta: dateTimeFormat(day[0], "ddd, D MMM") + ': ',
              value: seconds
            });
          });
          commit('CHART_BY_DAY_DATA', { 'labels': labels, 'series': [series] });
          commit('CHART_BY_DAY_LOADING', false);
          commit('CHART_BY_DAY_AGE', new Date());
          commit('CHART_BY_DAY_HIGH', high);
          resolve(data)
        })
        .catch(error => reject(error))
    })
  },

  getChartByTagData({commit}, payload) {
    return new Promise((resolve, reject) => {
      commit('CHART_BY_TAG_LOADING', true);
      getChartByTag(payload)
        .then(({data}) => {
          let tags = data.data;
          let labels = [];
          let series = [];
          tags.forEach(tag => {
            labels.push(tag[0]);
            series.push({
              meta: tag[0] + ': ',
              value: secondsToMinutes(tag[1])
            });
          });
          commit('CHART_BY_TAG_DATA', { 'labels': labels, 'series': series });
          commit('CHART_BY_TAG_LOADING', false);
          commit('CHART_BY_TAG_AGE', new Date());
          resolve(data)
        })
        .catch(error => reject(error))
    });
  },

  getChartByCompletedData({commit, state}, payload) {
    let dates = []
    const startDate = new Date(payload.start);
    const endDate = new Date(payload.end);
    while (startDate < endDate) {
      dates = [...dates, new Date(startDate)];
      startDate.setDate(startDate.getDate() + 1);
    }
    return new Promise((resolve, reject) => {
      commit('CHART_BY_COMPLETED_LOADING', true);
      getChartByCompleted(payload)
        .then(({data}) => {
          let taskData = data.data;
          let labels = [];
          let series = [];
          dates.forEach(date => {
            let formatted = dateTimeFormat(date, "YYYY-MM-DD");
            let index = taskData.findIndex(item => item.completed_at == formatted);
            if (index === -1) {
              taskData.push({ 'completed_at': formatted, total: 0 });
            }
          });
          taskData.sort((a,b) => (a.completed_at > b.completed_at) ? 1 : ((b.completed_at > a.completed_at) ? -1 : 0))
          taskData.forEach(task => {
            labels.push(dateTimeFormat(task.completed_at, "ddd"));
            series.push({
              meta: dateTimeFormat(task.completed_at, "ddd, D MMM") + ': ',
              value: task.total
            });
          });
          let max = Math.max.apply(Math, taskData.map((task) => { return task.total; }));
          let high = max > state.chartByCompletedHigh ? max : state.chartByCompletedHigh;
          commit('CHART_BY_COMPLETED_DATA', { 'labels': labels, 'series': [series] });
          commit('CHART_BY_COMPLETED_HIGH', high);
          commit('CHART_BY_COMPLETED_AGE', new Date());
          commit('CHART_BY_COMPLETED_LOADING', false);
          resolve(data)
        })
        .catch(error => reject(error))
    });
  },

  getChartByTimerData({commit}, payload) {
    return new Promise((resolve, reject) => {
      commit('CHART_BY_TASK_LOADING', true);
      getChartByTimer(payload)
        .then(({data}) => {
          let totalDuration = 0;
          let totalTime = [ "00", "00", "00" ];
          let days = [];
          let timers = data.data;
          timers.forEach(timer => {
            let dateStarted = dateTimeFormat(timer.started_at, "ddd, D MMM");
            timer.dateStarted = null;
            if (!days.includes(dateStarted)) {
              days.push(dateStarted);
              timer.dateStarted = dateStarted;
            }
            totalDuration += timer.duration || 0;
          });
          totalTime = duration(totalDuration);
          commit('CHART_BY_TASK_TIMERS', timers);
          commit('CHART_BY_TASK_LOADING', false);
          resolve(data)
        })
        .catch(error => reject(error))
    })
  },

  getChartStats({commit}, payload) {
    return new Promise((resolve, reject) => {
      getChartStats(payload)
        .then(({data}) => {
          let stats = data.data
          commit('CHART_STATS_TOTAL_TASKS_COUNT', stats.total_task_count || 0);
          commit('CHART_STATS_DUE_TASKS_COUNT', stats.due_task_count || 0);
          commit('CHART_STATS_TOTAL_TIMER_DURATION', secondsToHours(stats.total_timer_duration) || 0);
          commit('CHART_STATS_TOTAL_NOTES_COUNT', stats.total_notes_count || 0);
          resolve(data)
        })
        .catch(error => reject(error))
    })
  },
}

export const getters = {
  chartByDayLoading : state => {
    return state.chartByDayLoading
  },
  chartByDayHigh : state => {
    return state.chartByDayHigh
  },
  chartByDayData : state => {
    return state.chartByDayData
  },
  chartByDayAge : state => {
    return state.chartByDayAge
  },

  chartByTagLoading : state => {
    return state.chartByTagLoading
  },
  chartByTagData : state => {
    return state.chartByTagData
  },
  chartByTagAge : state => {
    return state.chartByTagAge
  },

  chartByCompletedLoading : state => {
    return state.chartByCompletedLoading
  },
  chartByCompletedHigh : state => {
    return state.chartByCompletedHigh
  },
  chartByCompletedData : state => {
    return state.chartByCompletedData
  },
  chartByCompletedAge : state => {
    return state.chartByCompletedAge
  },

  chartStatsTotalTasksCount : state => {
    return state.chartStatsTotalTasksCount
  },
  chartStatsDueTasksCount : state => {
    return state.chartStatsDueTasksCount
  },
  chartStatsTotalTimerDuration : state => {
    return state.chartStatsTotalTimerDuration
  },
  chartStatsTotalNotesCount : state => {
    return state.chartStatsTotalNotesCount
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
