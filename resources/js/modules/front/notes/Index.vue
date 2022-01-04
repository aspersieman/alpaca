<template>
  <v-container class="my-2">
    <add-fab />
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
          @input="searchNotes(true)"
        />
      </v-flex>
    </v-layout>

    <v-layout
      v-if="notes.length == 0"
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
            :notes="notes"
            :width="350"
            :height="350"
            :show-task="showTask"
          />
        </v-col>
      </v-row>
    </v-container>
  </v-container>
</template>

<script>
import { mapGetters } from 'vuex';
import NoteList from './components/NoteList';
import AddFab from '@/components/core/AddFab';

export default {
  components: {
    NoteList,
    AddFab,
  },

  data() {
    return {
      search: '',
      showTask: true,
      query: {
        name: '',
        userId: '',
      },
      searchNotesTimeout: null
    }
  },

  computed: {
    ...mapGetters('note', [
      'notes',
    ]),
  },

  mounted() {
    this.searchNotes();
    this.$store.subscribe((mutation) => {
      if (
        [
          'note/ADDED',
          'note/UPDATED',
          'note/DELETED',
        ].includes(mutation.type)
      ) {
        this.searchNotes();
      }
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

    searchNotes(debounce) {
      this.setNotesSettings();
      if (debounce) {
        clearTimeout(this.searchNotesTimeout);
        this.searchNotesTimeout = setTimeout(() => {
          this.prepareQuery();
          this.$store.dispatch('note/getNotes', this.query)
            .then(() => this.$overlay.show(false))
        }, 700);
      } else {
        this.prepareQuery();
        this.$store.dispatch('note/getNotes', this.query)
          .then(() => this.$overlay.show(false))
      }
    },

    prepareQuery() {
      this.resetQuery();
      this.setQueryValue('name', this.search || null);
      this.setQueryValue('userId', this.$auth?.user?.id);
    },

    setQueryValue(key, value) {
      if (value === null) {
        delete(this.query[key]);
      } else {
        this.query[key] = value;
      }
    },

    resetQuery(){
      this.query = {
        name: null,
        userId: null,
      }
    },
  }
}
</script>
<style scoped lang="scss">
@import "../../../../sass/index";
</style>
