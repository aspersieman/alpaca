<template>
  <div class="reports">
    <v-container class="my-2">
      <v-layout
        row
        class="mb-3"
        wrap
      >
        <v-avatar>
          <v-icon>
            note
          </v-icon>
        </v-avatar>
        <h1 class="subheading grey--text ml-4">
          Notes
        </h1>
      </v-layout>
      <v-layout
        row
        class="mb-1 mt-1"
        wrap
      >
        <v-btn
          v-shortkey.once="['ctrl', 'alt', '/']"
          style="display: none;"
          @shortkey="searchFocus()"
        >
          Search focus
        </v-btn>
        <v-flex
          class="search-input-container"
          xs5
          md3
          lg3
        >
          <v-text-field
            ref="searchInput"
            v-model="search"
            outlined
            clearable
            dense
            label="Search"
            class="search-input"
            type="text"
          />
        </v-flex>
      </v-layout>

      <v-layout
        v-if="searchNotes.length == 0"
        row
        justify-center
      >
        <v-layout
          column
          align-center
        >
          <span :class="{'grey--text display-3': $vuetify.breakpoint.smAndUp, 'display-1 grey--text': $vuetify.breakpoint.xsOnly}">Nothing here </span>
        </v-layout>
      </v-layout>
      <v-container
        v-else
        class="tasks-container"
      >
        <v-row>
          <v-col
            class="col-12"
          >
            <note-list
              :notes="searchNotes"
              :width="350"
              :height="350"
              :show-task="showTask"
            />
          </v-col>
        </v-row>
      </v-container>
    </v-container>
  </div>
</template>

<script>
import NoteList from './components/NoteList';
import NoteMixin from '@/mixins/NoteMixin';

export default {
  components: {
    NoteList,
  },
  mixins: [
    NoteMixin,
  ],
  data() {
    return {
      search: '',
      notes: [],
      showTask: true
    }
  },
  computed: {
    searchNotes() {
      const self = this;
      self.setNotesSettings();
      return this.$store.getters['note/notes'].filter(note => {
        if (self.search != '' && self.search != null) {
          if (note.name.toLowerCase().includes(self.search)) {
            return note;
          }
          if (note.content.toLowerCase().includes(self.search)) {
            return note;
          }
        } else {
          return note;
        }
      });
    },
  },
  mounted() {
    this.$nextTick(function () {
      this.getNotesSettings();
    });
  },
  methods: {
    setNotesSettings() {
      const parsedSearch = JSON.stringify(this.search);
      localStorage.setItem('notes-search', parsedSearch);
      const parsedDateRange = JSON.stringify(this.dates);
      localStorage.setItem('notes-date-range', parsedDateRange);
      const parsedSort = JSON.stringify(this.sort);
      localStorage.setItem('notes-sort', parsedSort);
    },
    getNotesSettings() {
      if (localStorage.getItem('notes-search')) {
        try {
          this.search = JSON.parse(localStorage.getItem('notes-search'));
        } catch(e) {
          localStorage.removeItem('notes-search');
        }
      }
      if (localStorage.getItem('notes-date-range')) {
        try {
          this.dates = JSON.parse(localStorage.getItem('notes-date-range'));
        } catch(e) {
          localStorage.removeItem('notes-date-range');
        }
      }
      if (localStorage.getItem('notes-sort')) {
        try {
          this.sort = JSON.parse(localStorage.getItem('notes-sort'));
        } catch(e) {
          localStorage.removeItem('notes-sort');
        }
      }
    },
  }
}
</script>
<style scoped lang="scss">
@import "../../../../sass/index";
</style>
