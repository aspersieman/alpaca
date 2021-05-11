import axios from 'axios'

export function register(payload) {
    return axios.post('/api/v1/register', payload)
}

export function login(payload) {
    return axios.post('/api/v1/login', payload)
}

export function logout() {
    return axios.get('/api/v1/logout')
}

export function sendPasswordResetLink(payload) {
    return axios.post('/api/v1/password/email', payload)
}

export function resetPassword(payload) {
    return axios.post('/api/v1/password/reset', payload)
}
