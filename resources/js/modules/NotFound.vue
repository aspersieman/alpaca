<template>
  <div>
    <div id="ui-canvas" style="margin: 0;">
      <core-home-header />
    </div>
    <v-container class="my-2">
      <v-row>
        <v-col 
          class="mb-3" 
        >
          <v-card
            class="pa-2"
            flat
            tile>
            <div class="heading">Page not found.</div>
          </v-card>
        </v-col>
        <v-col
          class="mb-2"
        >
          <v-card
            class="pa-2"
            flat
            tile>
            <div class="grey--text">
              The page you are trying to get to never existed in this reality,
              or has migrated to a parallel universe.
            </div>
            <div class="llama-full"></div>
          </v-card>
        </v-col>
        <v-col cols="12">
          <v-card
            color="transparent"
            class="pa-2"
            flat>
            <v-btn outlined large to="/">Back to safety</v-btn>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import CoreHomeHeader from '@/components/core/HomeHeader.vue';

export default {
  components: { 
    CoreHomeHeader,
  },
}
</script>
<style scoped lang="scss">
@import "../../sass/home";
</style>

<style>
.heading {
  font-size: 35px;
}
</style>
