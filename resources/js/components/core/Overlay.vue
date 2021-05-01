<template>
  <v-overlay :value="overlay">
    <div class="llama-loader" />
  </v-overlay>
</template>

<script>
export default {
  computed: {
    overlay() {
      return this.$store.getters['overlay/overlay'];
    },
  },
}
</script>
<style scoped>
.llama-loader::after {
  content: "Loading...";
  display: block;
  width: 100%;
  height: 200px;
  top: 100px;
  text-align: center;
}
</style>
