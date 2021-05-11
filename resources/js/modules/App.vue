<template>
  <v-app class="llama-background">
    <core-app-bar />
    <core-content />
    <core-footer />
    <div
      v-if="showLoader"
      class="llama-loader bg-half-transparent"
    />
    <vue-progress-bar />
    <confirm-dialog />
    <note-dialog />
    <core-snackbar />
    <core-overlay />
  </v-app>
</template>
<script>
import CoreAppBar from '@/components/core/AppBar.vue';
import CoreContent from '@/components/core/Content.vue';
import CoreFooter from '@/components/core/Footer.vue';
import CoreSnackbar from '@/components/core/Snackbar.vue';
import CoreOverlay from '@/components/core/Overlay.vue';

import ConfirmDialog from '@/components/core/ConfirmDialog.vue';
import NoteDialog from '@/modules/front/notes/components/NoteDialog.vue';
import TaskMixin from '@/mixins/TaskMixin';
import NoteMixin from '@/mixins/NoteMixin';

export default {
  components: {
    CoreAppBar,
    CoreContent,
    CoreFooter,
    CoreSnackbar,
    CoreOverlay,
    ConfirmDialog,
    NoteDialog
  },
  mixins: [
    TaskMixin,
    NoteMixin
  ],
  computed: {
    showLoader() {
      return this.$store.getters['loader/loaderShow'];
    },
  },
  created() {
    this.$store.dispatch('timer/resetTimerRunning')
      .then(() => {
        this.$store.dispatch('timer/startTimer');
      });
    this.reset();
  },
  methods: {
    reset() {
      this.$store.commit('drawer/NOTE_DRAWER', false);
    },
  }
}
</script>
<style scoped lang="scss">
@import "../../sass/home";
@import "../../sass/index";
</style>
<style>
.main-logo-text {
    display: none;
}
</style>
