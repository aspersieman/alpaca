import {
  getChartByTimer,
  getChartByDay,
  getChartByTag
} from '@/api/chart'

import {
  dateTimeFormat,
  duration,
  secondsToMinutes
} from '@/common/formatters';

export const state = {
  chartByTimerLoading: false,
  chartByTimerData: [],
  chartByTimerTimers: [],
  chartByTimerTotalTime: [ "00", "00", "00" ],
  chartByDayLoading: false,
  chartByDayData: [],
  chartByDayTotalTime: [ "00", "00", "00" ],
  chartByTagLoading: false,
  chartByTagData: [],
  chartByTagTotalTags: 0,
}

export const mutations = {
  CHART_BY_TIMER_LOADING: (state, payload) => {
    state.chartByTimerLoading = payload;
  },
  CHART_BY_TIMER_DATA: (state, payload) => {
    state.chartByTimerData = payload.data;
  },
  CHART_BY_TIMER_TIMERS: (state, payload) => {
    state.chartByTimerTimers = payload;
  },
  CHART_BY_TIMER_TOTAL_TIME: (state, payload) => {
    state.chartByTimerTotalTime = payload;
  },

  CHART_BY_DAY_LOADING: (state, payload) => {
    state.chartByDayLoading = payload;
  },
  CHART_BY_DAY_DATA: (state, payload) => {
    state.chartByDayData = payload;
  },
  CHART_BY_DAY_TOTAL_TIME: (state, payload) => {
    state.chartByDayTotalTime = payload;
  },

  CHART_BY_TAG_LOADING: (state, payload) => {
    state.chartByTagLoading = payload;
  },
  CHART_BY_TAG_DATA: (state, payload) => {
    state.chartByTagData = payload;
  },
  CHART_BY_TAG_TOTAL_TAGS: (state, payload) => {
    state.chartByTagTotalTags = payload;
  },
}

export const actions = {
  getChartByTimerData({commit}, payload) {
    return new Promise((resolve, reject) => {
      commit('CHART_BY_TIMER_LOADING', true);
      getChartByTimer(payload)
        .then(({data}) => {
          commit('CHART_BY_TIMER_DATA', data);
          let totalDuration = 0;
          let totalTime = [ "00", "00", "00" ];
          let days = [];
          let timers = data;
          timers.forEach(timer => {
            let dateStarted = dateTimeFormat(timer.started_at, "ddd, D MMM")
            timer.dateStarted = null;
            if (!days.includes(dateStarted)) {
              days.push(dateStarted);
              timer.dateStarted = dateStarted;
            }
            totalDuration += timer.duration || 0;
            timers.push(timer);
          });
          totalTime = duration(totalDuration, 'seconds').split(':');
          commit('CHART_BY_TIMER_TIMERS', timers);
          commit('CHART_BY_TIMER_TOTAL_TIME', totalTime);
          commit('CHART_BY_TIMER_LOADING', false);
          resolve(data)
        })
        .catch(error => reject(error))
    })
  },
  getChartByDayData({commit}, payload) {
    return new Promise((resolve, reject) => {
      commit('CHART_BY_DAY_LOADING', true);
      getChartByDay(payload)
        .then(({data}) => {
          let totalDuration = 0;
          let chartData = [];
          let days = data;
          days.forEach(day => {
            let formattedDay = dateTimeFormat(day[0], "ddd, D MMM")
            totalDuration += parseInt(day[1]);
            chartData.push([formattedDay, secondsToMinutes(day[1])]);
          });
          let totalTime = duration(totalDuration, 'seconds').split(':');
          commit('CHART_BY_DAY_DATA', chartData);
          commit('CHART_BY_DAY_TOTAL_TIME', totalTime);
          commit('CHART_BY_DAY_LOADING', false);
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
          let chartData = [];
          let tags = data;
          tags.forEach(tag => {
            chartData.push([tag[0], secondsToMinutes(tag[1])]);
          });
          let totalTags = chartData.length;
          commit('CHART_BY_TAG_DATA', chartData);
          commit('CHART_BY_TAG_TOTAL_TAGS', totalTags);
          commit('CHART_BY_TAG_LOADING', false);
          resolve(data)
        })
        .catch(error => reject(error))
    })
  }
}

export const getters = {
  chartByTimerLoading : state => {
    return state.chartByTimerLoading
  },
  chartByTimerData : state => {
    return state.chartByTimerData
  },
  chartByTimerTimers : state => {
    return state.chartByTimerTimers
  },
  chartByTimerTotalTime : state => {
    return state.chartByTimerTotalTime
  },

  chartByDayLoading : state => {
    return state.chartByDayLoading
  },
  chartByDayData : state => {
    return state.chartByDayData
  },
  chartByDayTotalTime : state => {
    return state.chartByDayTotalTime
  },

  chartByTagLoading : state => {
    return state.chartByTagLoading
  },
  chartByTagData : state => {
    return state.chartByTagData
  },
  chartByTagTotalTags : state => {
    return state.chartByTagTotalTags
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
