<template>
  <span>
    <v-btn
      v-show="buttonScrollTop"
      v-scroll="onScrollTop"
      fab
      small
      dark
      fixed
      bottom
      right
      color="grey"
      class="button-scroll mb-12"
      @click="scrollTo(0)"
    >
      <v-icon>keyboard_arrow_up</v-icon>
    </v-btn>
    <v-btn
      v-show="buttonScrollBottom"
      v-scroll="onScrollBottom"
      fab
      small
      dark
      fixed
      bottom
      right
      color="grey"
      class="button-scroll"
      @click="scrollTo(pageHeight)"
    >
      <v-icon>keyboard_arrow_down</v-icon>
    </v-btn>
  </span>
</template>

<script>
  export default {
    name: 'CoreToTopBottom',
    data: () => ({
      buttonScrollTop: false,
      buttonScrollBottom: false,
    }),
    computed: {
      pageHeight () {
        return document.body.scrollHeight
      }
    },
    mounted() {
      this.$nextTick(() => {
        this.onScrollTop();
        this.onScrollBottom();
      });
    },
    methods: {
      onScrollTop () {
        if (typeof window === 'undefined') return
        const top = window.pageYOffset;
        this.buttonScrollTop = top > 20;
      },
      onScrollBottom () {
        if (typeof window === 'undefined') return
        const scrollTop = (document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop;
        const scrollHeight = (document.documentElement && document.documentElement.scrollHeight) || document.body.scrollHeight;
        this.buttonScrollBottom = (scrollTop + window.innerHeight) < scrollHeight;
      },
      scrollTo(position) {
        this.$vuetify.goTo(position)
      }
    }
  }
</script>
<style scoped>
.button-scroll {
  opacity: 0.6;
}
.button-scroll:hover {
  opacity: 1;
}
</style>
