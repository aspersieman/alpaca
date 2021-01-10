export default {
  clickLogin: function(loginUrl) {
      window.location.href = loginUrl;
  },
  clickLogout: function(logoutUrl, afterLogoutRedirectUrl) {
    window.axios.post(logoutUrl).then(()=>{
      window.location.href = afterLogoutRedirectUrl;
    }).catch(err => {
      window.location.href = afterLogoutRedirectUrl;
      console.log(err);
    })
  },
  getBreakpoint: function(width) {
    if (width <= 600) {
      return "xs";
    } else if(width >= 600 && width <= 960) {
      return "sm";
    } else if(width >= 960 && width <= 1264) {
      return "md";
    } else if(width > 1264) {
      return "lg";
    } else {
      return "md";
    }
  },
  debug: function(message) {
    if (process.env.NODE_ENV === 'development') {
      console.log(message);
    }
  },
}
