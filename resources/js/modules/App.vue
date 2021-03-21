<template>
  <v-app>
    <core-drawer />
    <core-app-bar />
    <core-content />
    <core-footer />
    <div
      v-if="showLoader"
      class="wask_loader bg_half_transparent"
    >
      <moon-loader color="#ff4f4f" />
    </div>
    <vue-progress-bar />
    <home-snack-bar />
    <confirm-dialog />
    <note-dialog />
  </v-app>
</template>
<script>
import CoreDrawer from '../components/core/Drawer.vue';
import CoreAppBar from '../components/core/AppBar.vue';
import CoreContent from '../components/core/Content.vue';
import CoreFooter from '../components/core/Footer.vue';
import ConfirmDialog from '../components/core/ConfirmDialog.vue';
import NoteDialog from '../components/notes/NoteDialog.vue';
import SnackBar from './front/home/SnackBar.vue';
import MoonLoader from 'vue-spinner/src/MoonLoader.vue'
export default {
  components: {
    'core-drawer': CoreDrawer,
    'core-app-bar': CoreAppBar,
    'core-content': CoreContent,
    'core-footer': CoreFooter,
    'home-snack-bar': SnackBar,
    'confirm-dialog': ConfirmDialog,
    'note-dialog': NoteDialog,
    'moon-loader': MoonLoader
  },
  computed: {
    showLoader() {
      return this.$store.getters.showLoader;
    },
  },
  created() {
    const self = this;
    self.$store.commit('setAuthUser', window.APP_GLOBALS.AUTH_USER);

    window.axios.interceptors.response.use(undefined, function (err) {
      return new Promise(function () {
        if (err.response.status === 401 
          && err.response.config 
          && !err.response.config.__isRetryRequest
          && self.$route.name != 'index') {
          self.$router.push('/'); 
        }
      });
    });
  },
  mounted() {
    const self = this;
    self.$store.dispatch('loadMenu');
  },
  methods: { }
}
</script>
<style scoped>
#app {
  background: linear-gradient(rgba(255,255,255,.8), rgba(255,255,255, .9)), url("/img/logo/llama-jen-transparent-sepia.png");
  background-repeat: no-repeat, no-repeat;
  background-size: 491px 911px;
  background-position: center 20px;
  background-attachment: fixed;
}
</style>
