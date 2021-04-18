<template>
  <v-app class="llama-background">
    <core-app-bar />
    <core-content />
    <core-footer />
    <div
      v-if="showLoader"
      class="llama-loader bg-half-transparent"
    >
    </div>
    <vue-progress-bar />
    <confirm-dialog />
    <note-dialog />
    <notifications group="app" />
  </v-app>
</template>
<script>
import CoreAppBar from '@/components/core/AppBar.vue';
import CoreContent from '@/components/core/Content.vue';
import CoreFooter from '@/components/core/Footer.vue';
import ConfirmDialog from '@/components/core/ConfirmDialog.vue';
import NoteDialog from '@/components/notes/NoteDialog.vue';

export default {
  components: {
    CoreAppBar,
    CoreContent,
    CoreFooter,
    ConfirmDialog,
    NoteDialog
  },
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
  }
}
</script>
<style scoped lang="scss">
@import "../../sass/home";
</style>
