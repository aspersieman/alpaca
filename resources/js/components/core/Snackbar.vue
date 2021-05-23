<template>
  <v-snackbars
    :objects="messages"
    @update:objects="val => objects = val"
  >
    <template #action="{ close }">
      <v-btn
        icon
        @click="close()"
      >
        <v-icon>
          close
        </v-icon>
      </v-btn>
    </template>
  </v-snackbars>
</template>

<script>
import VSnackbars from "v-snackbars"

export default {
  components: {
    VSnackbars
  },
  data () {
    return {
      messages: [],
      title: '',
    }
  },
  created () {
    this.$store.subscribe((mutation) => {
      if (mutation.type === 'flash/showMessage') {
        this.messages.push({
          top: true,
          title: this.$store.getters['flash/title'],
          message: this.$store.getters['flash/text'],
          color: this.$store.getters['flash/color'],
          transition: 'fade-transition'
        });
        setTimeout(this.removeMessage, 2000);
      }
    });
  },
  methods: {
    removeMessage() {
      this.messages.splice(0, 1);
    },
  }
}
</script>
