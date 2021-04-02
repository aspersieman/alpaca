export function formatDate(dateString,format) {
  if (dateString) {
    return window.moment(dateString).format(format ? format : 'MMMM DD, YYYY');
  } else {
    return "";
  }
}

export function exportformatByteToMB(sizeInBytes) {
  return (sizeInBytes / (1024*1024)).toFixed(2);
}

export function formatMbToBytes(mb) {
  return (mb * 1048576).toFixed(2);
}

export function dateTimeFormat(dateTimeValue, format) {
  let tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
  const date = window.moment(dateTimeValue, 'YYYY-MM-DDTHH:mm:ss.SSS');
  return window.moment.tz(date, tz).format(format);
}

export function duration(seconds, format) {
  if (format === 'seconds') {
    return window.moment().startOf('day').seconds(seconds).format('HH:mm:ss');
  } else {
    return '';
  }
};

export function secondsToMinutes(seconds) {
  const minutes = (parseInt(seconds) / 60) || 0;
  return minutes;
}

export function dateOnly(dateString) {
  let date = new Date(dateString);
  date.setHours(0); 
  date.setMinutes(0); 
  date.setSeconds(0)
  date.setMilliseconds(0);
  return date;
}

export default {
  install(Vue) {
    Vue.prototype.$appFormatters = {
      formatDate(dateString,format) {
        return formatDate(dateString,format);
      },
      formatByteToMB(sizeInBytes) {
        return formatByteToMB(sizeInBytes);
      },
      formatMbToBytes(mb) {
        return formatMbToBytes(mb);
      },
      dateTimeFormat(dateTimeValue, format) {
        return dateTimeFormat(dateTimeValue, format);
      },
      duration(seconds, format) {
        return duration(seconds, format);
      },
      secondsToMinutes(seconds) {
        return secondsToMinutes(seconds);
      },
      dateOnly(dateString) {
        return dateOnly(dateString);
      }
    }
  }
}
