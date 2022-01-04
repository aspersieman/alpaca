import Vue from 'vue'

export function debug(message, type = "info") {
  let style = "";
  switch (type) {
    case 'info':
      style = "color: black;"
      break;
    case 'error':
      style = "color: red; font-weight: bold;"
      break;
  }
  if (process.env.NODE_ENV === 'development') {
    if (typeof message === 'object' || message === null) {
      console.log(message);
    } else {
      console.log('%c'+message, style);
    }
  }
};

const debugging = {
  install(Vue) {
    Vue.prototype.$debug = {
      info(message) {
        return debug(message, "info");
      },
      error(message) {
        return debug(message, "error");
      },
    }
  }
}

Vue.use(debugging);
