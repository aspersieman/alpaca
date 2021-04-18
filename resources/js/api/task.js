import axios from 'axios'

import setup from '@/helpers/interceptors';

setup();

export function saveTask(payload) {
  return axios.post('/api/v1/tasks', payload);
}

/*
Laravel does not support formData PUT request
So _method: PUT has to be added to the form data
and send your PUT request as POST request
*/
export function updateTask(task) {
  task._method = "PUT"
  return axios.post(`/api/v1/tasks/${task.id}`, task);
}

export function getTask(taskId) {
  return axios.get(`/api/v1/tasks/${taskId}`);
}

export function deleteTask(taskId) {
  return axios.delete(`/api/v1/tasks/${taskId}`);
}

export function getTasks(payload) {
  return axios.get('/api/v1/tasks', { params: payload });
}
