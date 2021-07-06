import axios from 'axios'

/*
Laravel does not support formData PUT request
So _method: PUT has to be added to the form data
and send your PUT request as POST request
 */
export function update({id, formData}) {
    return axios.post(`/v1/users/${id}`, formData)
}

export function saveUser(payload) {
  return axios.post('/api/v1/users', payload);
}

/*
Laravel does not support formData PUT request
So _method: PUT has to be added to the form data
and send your PUT request as POST request
*/
export function updateUser(payload) {
  payload._method = "PUT"
  return axios.post(`/api/v1/users/${payload.id}`, payload);
}

export function getUser(payload) {
  return axios.get(`/api/v1/users/${payload.id}`);
}

export function deleteUser(payload) {
  return axios.delete(`/api/v1/users/${payload.id}`);
}

export function getUsers(payload) {
  return axios.get('/api/v1/users', { params: payload });
}
