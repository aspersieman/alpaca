<template>
  <v-app class="llama-background">
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
    <snack-bar />
    <confirm-dialog />
    <note-dialog />
  </v-app>
</template>
<script>
import CoreAppBar from '@/components/core/AppBar.vue';
import CoreContent from '@/components/core/Content.vue';
import CoreFooter from '@/components/core/Footer.vue';
import ConfirmDialog from '@/components/core/ConfirmDialog.vue';
import NoteDialog from '@/components/notes/NoteDialog.vue';
import SnackBar from '@/modules/front/home/SnackBar.vue';
import MoonLoader from 'vue-spinner/src/MoonLoader.vue'

export default {
  components: {
    CoreAppBar,
    CoreContent,
    CoreFooter,
    SnackBar,
    ConfirmDialog,
    NoteDialog,
    MoonLoader
  },
  computed: {
    showLoader() {
      return this.$store.getters.showLoader;
    },
  },
  created() {
    const self = this;
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
  mounted() { },
  methods: { }
}
</script>
<style scoped lang="scss">
@import "../../sass/home";
</style>
