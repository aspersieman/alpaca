<template>
  <div>
    <v-container class="fill-height">
      <v-row
        align="center"
        justify="center"
      >
        <v-btn
          color="pink"
          dark
          @click.stop="drawer = !drawer"
        >
          Notes
        </v-btn>
      </v-row>
    </v-container>

    <v-navigation-drawer
      v-model="drawer"
      app
      temporary
      right
      hide-overlay
    >
      <v-list-item>
        <v-list-item-avatar>
          <v-icon>notes</v-icon>
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title>Notes</v-list-item-title>
        </v-list-item-content>
      </v-list-item>

      <v-divider />

      <v-list dense>
        <v-list-item>
          <v-list-item-content>
            <list-notes 
              :notes="notes" 
            />
          </v-list-item-content>
        </v-list-item>
      </v-list>
      <template
        #append
      >
        <div class="pa-2">
          <create-note @create-note="createNote" />
        </div>
      </template>
    </v-navigation-drawer>
  </div>
</template>

<script>
import ListNotes from './ListNotes';
import CreateNote from './CreateNotes';
export default {
  components: {
    ListNotes,
    CreateNote,
  },
  data() {
    return {
      drawer: null,
      notes: [{
        title: 'Lunch at 12PM',
        description: 'Call Robert to remember him.'
      }, {
        title: 'Gym with Jane',
        description: 'She is needing your motivation...'
      }, {
        title: 'Call mom',
        description: 'Ask her about the cats?'
      }],
    };
  },
  mounted() {
    console.log("NotesDrawer");
  },
  methods: {
    createNote(note) {
      this.notes.push(note);
      alert('Note created!');
    },
  },
}
</script>
