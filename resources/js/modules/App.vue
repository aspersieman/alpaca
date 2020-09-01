<template>
  <v-app>
    <core-drawer></core-drawer>
    <core-app-bar></core-app-bar>
    <core-content></core-content>
    <core-footer></core-footer>
    <div v-if="showLoader" class="wask_loader bg_half_transparent">
        <moon-loader color="#ff4f4f"></moon-loader>
    </div>
    <vue-progress-bar></vue-progress-bar>
    <home-snack-bar></home-snack-bar>
    <confirm-dialog></confirm-dialog>
  </v-app>
</template>
<script>
import CoreDrawer from '../components/core/Drawer.vue';
import CoreAppBar from '../components/core/AppBar.vue';
import CoreContent from '../components/core/Content.vue';
import CoreFooter from '../components/core/Footer.vue';
import ConfirmDialog from '../components/core/ConfirmDialog.vue';
import SnackBar from './front/home/SnackBar.vue';
export default {
  components: {
    'core-drawer': CoreDrawer,
    'core-app-bar': CoreAppBar,
    'core-content': CoreContent,
    'core-footer': CoreFooter,
    'home-snack-bar': SnackBar,
    'confirm-dialog': ConfirmDialog
  },
  mounted() {
    const self = this;
    self.$store.commit('setAuthUser', APP_GLOBALS.AUTH_USER);
    self.$store.dispatch('loadMenu');
  },
  computed: {
    showLoader() {
      return this.$store.getters.showLoader;
    },
  },
  methods: { }
}
</script>
<style scoped>
#app {
  background: linear-gradient(rgba(255,255,255,.5), rgba(255,255,255,.5)), url("/img/logo/llama-jen-transparent-sepia.png");
  background-repeat: no-repeat, no-repeat;
  background-size: 491px 911px;
  background-position: center 20px;
}
</style>
