<template>
  <v-footer
    class="py-4 main-footer"
    height="auto"
  >
    <v-container class="mx-auto">
      <v-row>
        <v-col cols="9">
          <base-btn
            v-for="(item, i) in items"
            :key="i"
            :href="item.href"
            class="ml-0 mr-3"
            color="primary"
            square
            target="_blank"
          >
            <v-icon v-text="item.icon" />
          </base-btn>
        </v-col>

        <v-spacer />

        <base-btn
          class="mr-0"
          square
          title="Go to top"
          @click="$vuetify.goTo(0)"
        >
          <v-icon>mdi-chevron-up</v-icon>
        </base-btn>
      </v-row>
      <v-row>
        <span class="font-weight-light caption">&copy; aspersieman {{ year }} - v{{ version }}</span>
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
      version: window.APP_GLOBALS.APP_VERSION
    }),
  }
</script>
<style scoped>
.main-footer {
  background: rgba(219, 219, 219, 0.6);
}
</style>
