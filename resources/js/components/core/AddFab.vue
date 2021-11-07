<template>
  <v-speed-dial
    v-if="$auth.check"
    v-model="fab"
    :direction="direction"
    :transition="`slide-y-reverse-transition`"
    :absolute="!$vuetify.breakpoint.smAndDown"
    :fixed="$vuetify.breakpoint.smAndDown"
    :top="!$vuetify.breakpoint.smAndDown"
    :bottom="$vuetify.breakpoint.smAndDown"
    right
    :class="{'mr-11': $vuetify.breakpoint.smAndDown}"
  >
    <template #activator>
      <v-btn
        v-model="fab"
        color="primary"
        fab
        :small="$vuetify.breakpoint.smAndDown"
      >
        <v-icon v-if="fab">
          mdi-close
        </v-icon>
        <v-icon v-else>
          mdi-plus
        </v-icon>
      </v-btn>
    </template>
    <v-btn
      fab
      small
      @click="$store.dispatch('task/openTaskDialog')"
    >
      <v-tooltip left>
        <template
          #activator="{ on }"
        >
          <v-icon v-on="on">
            mdi-check-circle-outline
          </v-icon>
        </template>
        Add task
      </v-tooltip>
    </v-btn>
    <v-btn
      fab
      small
      @click="$store.dispatch('note/openNoteDialog')"
    >
      <v-tooltip left>
        <template
          #activator="{ on }"
        >
          <v-icon v-on="on">
            mdi-note-plus-outline
          </v-icon>
        </template>
        Add note
      </v-tooltip>
    </v-btn>
    <v-btn
      v-if="isTimerRunning !== true"
      fab
      small
    >
      <v-tooltip left>
        <template
          #activator="{ on }"
        >
          <v-icon v-on="on">
            more_time
          </v-icon>
        </template>
        Add timer
      </v-tooltip>
    </v-btn>
  </v-speed-dial>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'CoreAddFab',

  data: () => ({
    fab: false,
  }),

  computed: {
    ...mapGetters('timer', [
      'isTimerRunning',
    ]),
    direction: function() { return this.$vuetify.breakpoint.smAndDown ? 'top' : 'bottom'; },
  }
}
</script>
