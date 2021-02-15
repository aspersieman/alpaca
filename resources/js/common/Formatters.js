export default {
  install(Vue) {
    Vue.prototype.$appFormatters = {
      formatDate: function(dateString,format) {
        if (dateString) {
          return window.moment(dateString).format(format ? format : 'MMMM DD, YYYY');
        } else {
          return "";
        }
      },
      formatByteToMB(sizeInBytes) {
        return (sizeInBytes / (1024*1024)).toFixed(2);
      },
      formatMbToBytes(mb) {
        return (mb * 1048576).toFixed(2);
      },
      dateTimeFormat(dateTimeValue, format) {
        let tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        const date = window.moment(dateTimeValue, 'YYYY-MM-DDTHH:mm:ss.SSS');
        return window.moment.tz(date, tz).format(format);
      },
      duration(seconds, format) {
        if (format === 'seconds') {
          return window.moment().startOf('day').seconds(seconds).format('HH:mm:ss');
        } else {
          return '';
        }
      },
      secondsToMinutes(seconds) {
        const minutes = (parseInt(seconds) / 60) || 0;
        return minutes;
      },
      dateOnly(dateString) {
        let date = new Date(dateString);
        date.setHours(0); 
        date.setMinutes(0); 
        date.setSeconds(0)
        date.setMilliseconds(0);
        return date;
      }
    }
  }
}
