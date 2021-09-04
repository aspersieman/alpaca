import axios from 'axios'

export function saveBackup(payload) {
  return axios.post('/api/v1/db-backup', payload);
}

export function getBackup(payload) {
  return axios.get(`/api/v1/db-backup/${payload}`);
}

export function deleteBackup(payload) {
  return axios.delete(`/api/v1/db-backup/${payload}`);
}

export function getBackups(payload) {
  return axios.get("/api/v1/db-backup", { params: payload });
}
