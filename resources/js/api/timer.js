import axios from 'axios'

import setup from '@/helpers/interceptors';

setup();

export function stopTimer(id) {
  const tz = {
    tz: Intl.DateTimeFormat().resolvedOptions().timeZone
  };
  return axios.post(`/api/v1/tasks/${id}/timers/stop`, tz);
}

export function getRunningTimer() {
  return axios.get('/api/v1/tasks/timers/running');
}

export function deleteTimer({taskId, timerId}) {
  return axios.delete(`/api/v1/tasks/${taskId}/timers/${timerId}`);
}

export function saveTimer({taskId, timer}) {
  return axios.post(`/api/v1/tasks/${taskId}/timers`, timer);
}

export function updateTimer({taskId, timer}) {
  return axios.put(`/api/v1/tasks/${taskId}/timers/${timer.id}`, timer);
}
