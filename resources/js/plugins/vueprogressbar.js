import Vue from 'vue';
import VueProgressBar from 'vue-progressbar'

// this is the progress bar settings, you
// can change colors here to fit on your needs or match
// your theming above
Vue.use(VueProgressBar,{
  color: '#3f51b5',
  failedColor: '#b71c1c',
  thickness: '5px',
  transition: {
    speed: '0.2s',
    opacity: '0.6s',
    termination: 300
  },
  autoRevert: true,
  inverse: false
});
