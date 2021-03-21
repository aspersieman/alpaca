import axios from 'axios'

// import setup from '@/helpers/interceptors';
//
// setup();

export function saveTask(payload) {
    return axios.post('/api/tasks', payload)
}

/*
Laravel does not support formData PUT request
So _method: PUT has to be added to the form data
and send your PUT request as POST request
 */
export function updateTask({taskId, formData}) {
    return axios.post(`/api/tasks/${taskId}`, formData)
}

export function getTask({taskId}) {
    return axios.get(`/api/tasks/${taskId}`)
}

export function deleteTask({taskId}) {
    return axios.delete(`/api/tasks/${taskId}`)
}

export function getTasks(payload) {
    return axios.get('/v1/tasks', { params: payload})
}
