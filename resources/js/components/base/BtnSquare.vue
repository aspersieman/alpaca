<template>
  <v-btn
    :style="styles"
    color="primary"
    fab
    small
    v-bind="$attrs"
    v-on="$listeners"
  >
    <slot />
  </v-btn>
</template>

<script>
  export default {
    name: 'BaseBtnSquare',

    props: {
      square: Boolean,
    },

    computed: {
      styles () {
        return {
          borderRadius: this.square ? '2px' : undefined,
        }
      },
    },
  }
</script>
