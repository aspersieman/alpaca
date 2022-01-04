import { duration } from '@/formatters';

export default {
  methods: {
    timerDuration(timer) {
      if (timer.stopped_at != null) {
        const from = window.moment(timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
        const to = window.moment(timer.stopped_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
        const seconds = parseInt(window.moment.duration(to.diff(from)).asSeconds())
        return duration(seconds);
      }
      return '';
    },
  },
}
