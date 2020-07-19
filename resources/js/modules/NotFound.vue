<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card
          class="pa-2"
          flat
          tile>
          <div class="heading">Page not found.</div>
        </v-card>
      </v-col>
      <v-col cols="12">
        <v-card
          class="pa-2"
          flat
          tile>
          <div class="grey--text">
            The page you are trying to get to never existed in this reality,
            or has migrated to a parallel universe.
          </div>
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
    <div class="llama-full"></div>
  </v-container>
</template>

<style>
.heading {
  font-size: 35px;
}
</style>
