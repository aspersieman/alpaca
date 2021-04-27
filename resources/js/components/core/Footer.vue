<template>
  <v-footer
    class="py-4 main-footer"
    height="auto"
  >
    <v-container class="mx-auto">
      <v-row>
        <v-col cols="9">
          <base-btn-square
            v-for="(item, i) in items"
            :key="i"
            :href="item.href"
            class="ml-0 mr-3"
            color="primary"
            square
            target="_blank"
          >
            <v-icon v-text="item.icon" />
          </base-btn-square>
        </v-col>
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
      </v-row>
      <v-row>
        <span class="font-weight-light caption">&copy; aspersieman {{ year }} - {{ version }}</span>
      </v-row>
    </v-container>
  </v-footer>
</template>

<script>
  export default {
    name: 'CoreFooter',

    data: () => ({
      items: [
        {
          href: 'https://www.instagram.com/aspersieman/',
          icon: 'mdi-instagram',
        },
      ],
      year: window.moment().format('YYYY'),
      version: window.APP_GLOBALS.APP_VERSION,
      buttonScrollTop: false,
      buttonScrollBottom: false,
    }),
    computed: {
      pageHeight () {
        return document.body.scrollHeight
      }
    },
    methods: {
      onScrollTop (e) {
        if (typeof window === 'undefined') return
        const top = window.pageYOffset || e.target.scrollTop || 0
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
.main-footer {
  background: rgba(219, 219, 219, 0.6);
}
.button-scroll {
  opacity: 0.6;
}
.button-scroll:hover {
  opacity: 1;
}
</style>
