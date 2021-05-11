import axios from 'axios'

export function saveNote(payload) {
  return axios.post('/api/v1/notes', payload);
}

/*
Laravel does not support formData PUT request
So _method: PUT has to be added to the form data
and send your PUT request as POST request
*/
export function updateNote(note) {
  note._method = "PUT"
  return axios.post(`/api/v1/notes/${note.id}`, note);
}

export function getNote(noteId) {
  return axios.get(`/api/v1/notes/${noteId}`);
}

export function deleteNote(noteId) {
  return axios.delete(`/api/v1/notes/${noteId}`);
}

export function getNotes(payload) {
  return axios.get('/api/v1/notes', { params: payload });
}
