import axios from 'axios'

import setup from '@/helpers/interceptors';

setup();

export function saveTimer(taskId, timer) {
  return axios.post(`/api/v1/tasks/${taskId}/timers`, timer);
}

/*
Laravel does not support formData PUT request
So _method: PUT has to be added to the form data
and send your PUT request as POST request
*/
export function updateTimer({taskId, timer}) {
  task._method = "PUT"
  return axios.put(`/api/v1/tasks/${taskId}/timers/${timer.id}`, timer);
}

export function stopTimer(id) {
  const tz = {
    tz: Intl.DateTimeFormat().resolvedOptions().timeZone
  };
  return axios.post(`/api/v1/tasks/${id}/timers/stop`, tz);
}

export function getTimerRunning() {
  return axios.get('/api/v1/tasks/timers/running');
}

export function deleteTimer({taskId, timerId}) {
  return axios.delete(`/api/v1/tasks/${taskId}/timers/${timerId}`);
}
