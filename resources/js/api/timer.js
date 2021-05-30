import axios from 'axios'

export function saveTimer(payload) {
  return axios.post(`/api/v1/tasks/${payload.task_id}/timers`, payload);
}

/*
Laravel does not support formData PUT request
So _method: PUT has to be added to the form data
and send your PUT request as POST request
*/
export function updateTimer(payload) {
  payload._method = "PUT"
  return axios.put(`/api/v1/tasks/${payload.task_id}/timers/${payload.id}`, payload);
}

export function stopTimer(payload) {
  const tz = {
    tz: Intl.DateTimeFormat().resolvedOptions().timeZone
  };
  return axios.post(`/api/v1/tasks/${payload.id}/timers/stop`, tz);
}

export function getTimerRunning() {
  return axios.get('/api/v1/tasks/timers/running');
}

export function deleteTimer(payload) {
  return axios.delete(`/api/v1/tasks/${payload.task_id}/timers/${payload.id}`);
}
