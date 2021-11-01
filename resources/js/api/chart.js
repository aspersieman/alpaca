import axios from 'axios'

export function getChartByTimer(params) {
  return axios.post(`/api/v1/charts/bytimer`, params);
}

export function getChartByDay(params) {
  return axios.post(`/api/v1/charts/byday`, params);
}

export function getChartByTag(params) {
  return axios.post(`/api/v1/charts/bytag`, params);
}

export function getChartByCompleted(params) {
  return axios.post(`/api/v1/charts/bycompleted`, params);
}

export function getChartStats(params) {
  return axios.post(`/api/v1/charts/stats`, params);
}
