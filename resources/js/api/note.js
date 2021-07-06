import axios from 'axios'

export function saveNote(payload) {
  return axios.post('/api/v1/notes', payload);
}

/*
Laravel does not support formData PUT request
So _method: PUT has to be added to the form data
and send your PUT request as POST request
*/
export function updateNote(payload) {
  payload._method = "PUT"
  return axios.post(`/api/v1/notes/${payload.id}`, payload);
}

export function getNote(payload) {
  return axios.get(`/api/v1/notes/${payload}`);
}

export function deleteNote(payload) {
  return axios.delete(`/api/v1/notes/${payload.id}`);
}

export function getNotes(payload) {
  return axios.get('/api/v1/notes', { params: payload });
}
