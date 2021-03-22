import {
    saveTask,
    updateTask,
    deleteTask,
    getTasks,
    getTask,
} from '@/api/task'

export const state = {
    task: [],
    tasks: []
}

export const mutations = {
    TASKS: (state, payload) => {
        let tasks = state.tasks
        tasks.push(...payload.data)
        state.tasks = tasks
    },

    TASK: (state, payload) => state.task = payload
}

export const actions = {
    saveTask({commit}, payload) {
        return new Promise((resolve, reject) => {
            saveTask(payload)
                .then(({data}) => {
                    resolve(data)
                })
                .catch(error => reject(error))

        })
    },

    updateTask({commit}, payload) {
        return new Promise((resolve, reject) => {
            updateTask(payload)
                .then(({data}) => {
                    resolve(data)
                })
                .catch(error => reject(error))

        })
    },

    deleteTask({commit}, payload) {
        return new Promise((resolve, reject) => {
            deleteTask(payload)
                .then(({data}) => {
                    resolve(data)
                })
                .catch(error => reject(error))
        })
    },
    getTasks({commit}, payload) {
        return new Promise((resolve, reject) => {
            getTasks(payload)
                .then(({data}) => {
                    commit('TASKS', data)
                    resolve(data)
                })
                .catch(error => reject(error))
        })
    },

    getTask({commit}, payload) {
        return new Promise((resolve, reject) => {
            getTask(payload)
                .then(({data}) => {
                    commit('TASK', data)
                    resolve(data)
                })
                .catch(error => reject(error))
        })
    },
}

export const getters = {
    tasks : state => {
        return state.tasks
    },

    task: state => state.task
}

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}


