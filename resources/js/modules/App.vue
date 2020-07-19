<template>
  <v-app>
    <core-drawer></core-drawer>
    <core-app-bar></core-app-bar>
    <core-content></core-content>
    <core-footer></core-footer>
    <div v-if="showLoader" class="wask_loader bg_half_transparent">
        <moon-loader color="blue"></moon-loader>
    </div>
    <vue-progress-bar></vue-progress-bar>
    <home-snack-bar></home-snack-bar>
  </v-app>
</template>
<script>
import CoreDrawer from '../components/core/Drawer.vue';
import CoreAppBar from '../components/core/AppBar.vue';
import CoreContent from '../components/core/Content.vue';
import CoreFooter from '../components/core/Footer.vue';
import HomeSnackBar from './front/home/SnackBar.vue';
export default {
  components: {
    CoreDrawer,
    CoreAppBar,
    CoreContent,
    CoreFooter,
    HomeSnackBar
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
  methods: {
    dialogOk() {
      this.$store.commit('dialogOk');
    },
    dialogCancel() {
      this.$store.commit('dialogCancel');
    }
  }
}
</script>
