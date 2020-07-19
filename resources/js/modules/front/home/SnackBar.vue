<template>
  <div>
    <v-snackbar
       :timeout="snackbarDuration"
       :color="snackbarColor"
       top
       v-model="showSnackbar">
      {{ snackbarMessage }}
    </v-snackbar>
  </div>
</template>
<script>
// Utilities
import {
  mapGetters,
  mapMutations,
} from 'vuex'

export default {
  name: 'HomeSnackBar',
  computed: {
    showSnackbar: {
      get() {
        return this.$store.getters.showSnackbar;
      },
      set(val) {
        if(!val) this.$store.commit('hideSnackbar');
      }
    },
    snackbarMessage() {
      return this.$store.getters.snackbarMessage;
    },
    snackbarColor() {
      return this.$store.getters.snackbarColor;
    },
    snackbarDuration() {
      return this.$store.getters.snackbarDuration;
    },
  }
}
</script>

