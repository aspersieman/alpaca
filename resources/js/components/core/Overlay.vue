<template>
  <v-overlay :value="overlay">
    <v-progress-circular
      indeterminate
      size="64"
    />
  </v-overlay>
</template>

<script>
export default {
  data () {
    return {
      overlay: false,
    }
  },
  created () {
    this.$store.subscribe((mutation) => {
      if (mutation.type === 'overlay/showOverlay') {
        this.overlay = this.$store.getters['overlay/overlay'];
      }
    });
  },
}
</script>
