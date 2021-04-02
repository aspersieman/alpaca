import axios from 'axios'

import setup from '@/helpers/interceptors';

setup();

export function getChartByTimer(params) {
  return axios.post(`/api/v1/charts/bytimer`, params);
}

export function getChartByDay(params) {
  return axios.post(`/api/v1/charts/byday`, params);
}

export function getChartByTag(params) {
  return axios.post(`/api/v1/charts/bytag`, params);
}
