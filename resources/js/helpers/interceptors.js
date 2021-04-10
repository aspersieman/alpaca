import axios from 'axios';
import store from '@/store/';
import router from '@/router';

export default function setup() {
    axios.interceptors.request.use(function(config) {
        const token = store.getters['auth/getToken'];
        if(token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }
        return config;
    }, function(err) {
        return Promise.reject(err);
    });

    axios.interceptors.response.use(undefined, function (err) {
      const responseStatus = err.response.status || null;
      if (responseStatus === 401 
        && err.response.config 
        && !err.response.config.__isRetryRequest
        && router.name != 'index') {
        store.dispatch('snackbar/show',{
          message: 'Please log in to continue',
          color: 'error',
          duration: 3000
        }, { root: true });
        store.dispatch('auth/logout');
        router.replace('/'); 
      }
    });
}
