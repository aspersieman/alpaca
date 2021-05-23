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
        <core-to-top-bottom />
      </v-row>
      <v-row>
        <span class="font-weight-light caption">&copy; aspersieman {{ year }} - {{ version }}</span>
      </v-row>
    </v-container>
  </v-footer>
</template>

<script>
import CoreToTopBottom from '@/components/core/ToTopBottom';

export default {
  name: 'CoreFooter',
  components: {
    CoreToTopBottom
  },
  data: () => ({
    items: [
      {
        href: 'mailto:aspersieman@gmail.com',
        icon: 'mdi-email',
      },
      {
        href: 'https://www.instagram.com/aspersieman/',
        icon: 'mdi-instagram',
      },
      {
        href: 'https://www.github.com/aspersieman/',
        icon: 'mdi-github',
      },
      {
        href: 'https://www.twitter.com/aspersieman/',
        icon: 'mdi-twitter',
      },
    ],
    year: window.moment().format('YYYY'),
    version: window.APP_GLOBALS.APP_VERSION,
  }),
}
</script>
<style scoped>
.main-footer {
  background: rgba(219, 219, 219, 0.6);
}
</style>
