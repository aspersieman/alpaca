<template>
  <div>
    <v-snackbar
       :timeout="duration"
       :color="color"
       top
       v-model="show">
      {{ message }}
    </v-snackbar>
  </div>
</template>
<script>
export default {
  name: 'SnackBar',
  computed: {
    show: {
      get() {
        return this.$store.getters['snackbar/show'];
      },
      set(val) {
        if(!val) this.$store.dispatch('snackbar/hide');
      }
    },
    message() {
      return this.$store.getters['snackbar/message'];
    },
    color() {
      return this.$store.getters['snackbar/color'];
    },
    duration() {
      return this.$store.getters['snackbar/duration'];
    },
  }
}
</script>

