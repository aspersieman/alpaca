import axios from 'axios';

export default function intercept(Vue) {
  axios.interceptors.request.use(function(config) {
    const token = Vue.$store.getters['auth/getToken'];
    if(token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
  }, function(err) {
    return Promise.reject(err);
  });

  axios.interceptors.response.use(undefined, function (err) {
    const responseStatus = err?.response?.status || null;
    if (
      responseStatus === 401
      && (
        err.response.config
        && (
          !err.response.config.__isRetryRequest
          || typeof err.response.config.__isRetryRequest == "undefined"
        )
      )
      && Vue.$router.currentRoute.name != 'index'
    ) {
      Vue.$store.dispatch('auth/logout');
      Vue.$router.replace({ path: '/', query: { loginprompt: 'true' }});
    }
  });
}
