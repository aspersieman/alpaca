import Vue from 'vue'

export function formatDate(dateString, format) {
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

export function padNumber(number) {
  return (number > 9) ? number : "0" + number;
}

export function duration(seconds) {
  const hours = Math.floor(seconds / 3600);
  const minutes = Math.floor(seconds % 3600 / 60);
  let time = [];
  seconds = Math.floor(seconds % 60);
  time.push(padNumber(hours));
  time.push(padNumber(minutes));
  time.push(padNumber(seconds));
  return time.join(':');
};

export function secondsToMinutes(seconds, round = true) {
  let minutes = (parseInt(seconds) / 60) || 0;
  minutes = round ? Math.floor(minutes) : minutes;
  return minutes;
}

export function secondsToHours(seconds, round = true) {
  let hours = ((parseInt(seconds) / 60 ) / 60) || 0;
  hours = round ? Math.floor(hours) : hours;
  return hours;
}

export function dateOnly(dateString) {
  let date = new Date(dateString);
  date.setHours(0);
  date.setMinutes(0);
  date.setSeconds(0)
  date.setMilliseconds(0);
  return date;
}

/**
 * Format a datetime value for display in a timer
 */
export function timerFormat(dateValue, time = false) {
  let tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
  if (time) {
    const date = window.moment(dateValue, 'YYYY-MM-DDTHH:mm:ss.SSS');
    return window.moment.tz(date, tz).format('YYYY-MM-DD @ HH:mm:SS');
  } else {
    return window.moment.tz(dateValue, tz).format('YYYY-MM-DD');
  }
}

export function findCeiling(value) {
  let ceiling = value;
  let absoluteValue = Math.abs(parseInt(value));
  if (aboluteValue > 10 && aboluteValue <= 200) {
      ceilint = Math.ceil(aboluteValue / 10) * 10;
  }
  if (aboluteValue > 10 && aboluteValue <= 200) {
      ceilint = Math.ceil(aboluteValue / 20) * 20;
  }
  if (aboluteValue > 500 && aboluteValue <= 1000) {
      ceilint = Math.ceil(aboluteValue / 100) * 100;
  }
  if (aboluteValue > 1000 && aboluteValue <= 2000) {
      ceilint = Math.ceil(aboluteValue / 200) * 200;
  }
  if (aboluteValue > 2000 && aboluteValue <= 10000) {
      ceilint = Math.ceil(aboluteValue / 1000) * 1000;
  }
  return parseInt(value) < 0 ? ceiling * -1 : ceiling;
}

const formatters = {
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
      },
      timerFormat(dateValue, time) {
        return timerFormat(dateValue, time);
      }
    }
  }
}

Vue.use(formatters);
