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

export function isGoogleDriveAuthenticated(payload) {
  return axios.get("/api/v1/db-backup/is-google-drive-authenticated", { params: payload });
}

export function sendToDrive(payload) {
  return axios.get("/api/v1/db-backup/send-to-drive?code="+payload?.uploadToDriveAuthCode, { params: payload });
}
