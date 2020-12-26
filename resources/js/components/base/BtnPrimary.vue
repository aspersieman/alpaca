<template>
  <v-btn
    class="primary lighten-1"
    dark
    :title="title"
    color="primary"
    v-bind="$attrs"
    v-on="$listeners"
  >
    <slot />
    <v-icon
      v-if="icon != false"
    >
      {{ icon }}
    </v-icon>
  </v-btn>
</template>

<script>
export default {
  name: 'BaseBtnPrimary',
  props: {
    title: {
      required: false,
      type: String,
      default: function() {
        return "";
      }
    },
    icon: {
      required: false,
      type: String,
      default: function() {
        return "";
      }
    }
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
