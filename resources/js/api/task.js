import axios from 'axios'

export function saveTask(payload) {
  return axios.post('/api/v1/tasks', payload);
}

/*
Laravel does not support formData PUT request
So _method: PUT has to be added to the form data
and send your PUT request as POST request
*/
export function updateTask(payload) {
  payload._method = "PUT"
  return axios.post(`/api/v1/tasks/${payload.id}`, payload);
}

export function getTask(payload) {
  return axios.get(`/api/v1/tasks/${payload.id}`);
}

export function deleteTask(payload) {
  return axios.delete(`/api/v1/tasks/${payload.id}`);
}

export function getTasks(payload) {
  return axios.get('/api/v1/tasks', { params: payload });
}
