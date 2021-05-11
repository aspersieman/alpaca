<template>
  <div>
    <v-navigation-drawer
      ref="drawer"
      clipped
      app
      right
      hide-overlay
      :value="drawerState"
      :width="navigation.width"
      :touchless="true"
    >
      <div
        v-if="$vuetify.breakpoint.mdAndUp"
        class="toggle"
      >
        <v-btn
          ref="resizeButton"
          fab
          x-small
          light
        >
          <v-icon>drag_indicator</v-icon>
        </v-btn>
      </div>
      <v-list-item
        fixed
        class="drawer-title"
      >
        <v-list-item-avatar>
          <v-icon>note</v-icon>
        </v-list-item-avatar>

        <v-list-item-content
          v-if="!showSearch"
        >
          <v-list-item-title>Notes</v-list-item-title>
          <v-list-item-subtitle>
            <v-icon small>
              task_alt
            </v-icon>
            {{ task.name }}
          </v-list-item-subtitle>
        </v-list-item-content>
        <v-list-item-content
          v-if="showSearch"
        >
          <v-text-field
            ref="searchInput"
            v-model="search"
            outlined
            dense
            label="Search"
            class="search-input"
            type="text"
            append-icon="close"
            @click:append="searchShow(false)"
          >
            <v-icon>
              <template #prepend>
                search
              </template>
            </v-icon>
          </v-text-field>
        </v-list-item-content>
        <v-btn
          v-if="!showSearch"
          icon
          light
          @click="searchShow(true)"
        >
          <v-icon>search</v-icon>
        </v-btn>
        <v-btn
          icon
          light
          x-small
          @click="close()"
        >
          <v-icon>close</v-icon>
        </v-btn>
      </v-list-item>

      <v-divider />

      <v-list dense>
        <v-list-item>
          <v-list-item-content>
            <note-list
              :notes="searchNotes"
            />
          </v-list-item-content>
        </v-list-item>
      </v-list>
      <template
        #append
      >
        <note-create @create-note="saveNote" />
      </template>
    </v-navigation-drawer>
  </div>
</template>

<script>
import NoteList from './NoteList';
import NoteCreate from './NoteCreate';
import NoteMixin from '@/mixins/NoteMixin';

export default {
  components: {
    NoteList,
    NoteCreate,
  },
  mixins: [
    NoteMixin
  ],
  data() {
    return {
      notes: [],
      task: {
        id: null,
        name: null,
        due: null,
        status: null,
        order: null,
        timers: []
      },
      defaultWidth: 450,
      navigation: {
        width: 450,
        borderSize: 3,
        maxWidth: 700
      },
      search: "",
      showSearch: false
    };
  },
  computed: {
    drawerState: {
      get() {
        return this.$store.getters['drawer/noteDrawer'];
      },
      set(state) {
        if (state) {
          this.close();
        }
      },
    },
    searchNotes() {
      const self = this;
      return this.notes.filter(note => {
        if (self.search != "" && self.search != null) {
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
  created() {
    const self = this;
    self.navigation.width = self.$vuetify.breakpoint.mdAndUp ? self.defaultWidth : window.innerWidth;
    self.$eventBus.$on('note-drawer-toggle', (value, notes, task) => {
      if (value == true) {
        self.task = task;
        self.notes = notes;
      } else {
        value = false;
        self.notes = [];
        self.task = {
          name: null,
          due: null,
          status: null,
          order: null,
          timers: []
        };
      }
      self.$store.commit('drawer/NOTE_DRAWER', value);
    });
    self.$eventBus.$on('note-deleted', (note) => {
      self.notes = self.task.notes
        .filter(function(element) {
          return element.id !== note.id;
        });
    });
    self.$eventBus.$on('note-added', (note) => {
      self.notes.push(note);
    });
    self.$eventBus.$on('note-updated', (note) => {
      const noteIndex = self.notes.findIndex((obj => obj.id == note.id));
      self.notes[noteIndex] = note;
    });
  },
  mounted() {
    if (this.$vuetify.breakpoint.mdAndUp) {
      this.setBorderWidth();
      this.setEvents();
    }
  },
  methods: {
    setBorderWidth() {
      let i = this.$refs.drawer.$el.querySelector(
        ".v-navigation-drawer__border"
      );
      i.style.width = this.navigation.borderSize + "px";
      i.style.cursor = "ew-resize";
      i.style.backgroundColor = "#ebebeb";
    },

    setEvents() {
      const self = this;
      const minSize = this.navigation.borderSize;
      const el = this.$refs.drawer.$el;
      const drawerBorder = el.querySelector(".v-navigation-drawer__border");
      const resizeButton = this.$refs.resizeButton.$el;
      const direction = el.classList.contains("v-navigation-drawer--right")
        ? "right"
        : "left";

      function resize(e) {
        let evt = typeof e.touches !== 'undefined' && e.touches.length > 0 ? e.touches[0] : e;
        document.body.style.cursor = "ew-resize";
        let f =
          direction === "right"
            ? document.body.scrollWidth - evt.clientX
            : evt.clientX;
        if (f <= self.navigation.maxWidth) {
          el.style.width = f + "px";
        }
      }

      drawerBorder.addEventListener(
        "mousedown",
        (e) => {
          if (e.offsetX < minSize) {
            el.style.transition = "initial";
            document.addEventListener("mousemove", resize, false);
          }
        },
        false
      );

      resizeButton.addEventListener(
        "mousedown",
        () => {
          el.style.transition = "initial";
          document.addEventListener("mousemove", resize, false);
        },
        false
      );

      drawerBorder.addEventListener(
        "touchstart",
        (e) => {
          if (e.offsetX < minSize) {
            el.style.transition = "initial";
            document.addEventListener("touchmove", resize, false);
          }
        },
        false
      );

      resizeButton.addEventListener(
        "touchstart",
        () => {
          el.style.transition = "initial";
          document.addEventListener("touchmove", resize, false);
        },
        false
      );

      document.addEventListener(
        "mouseup",
        () => {
          el.style.transition = "";
          this.navigation.width = el.style.width;
          document.body.style.cursor = "";
          document.removeEventListener("mousemove", resize, false);
        },
        false
      );

      document.addEventListener(
        "touchend",
        () => {
          el.style.transition = "";
          this.navigation.width = el.style.width;
          document.body.style.cursor = "";
          document.removeEventListener("mousemove", resize, false);
        },
        false
      );
    },

    close() {
      this.$eventBus.noteDrawerToggle(false);
    },

    searchShow(value) {
      this.search = '';
      this.showSearch = value;
    },
  },
}
</script>
<style scoped lang="scss">
@import "../../../../../sass/index";
.toggle {
  position: absolute;
  left: -10px;
  top: 50%;
  bottom: 50%;
  vertical-align: middle;
  cursor: move;
  z-index: 1;
  cursor: ew-resize;
}

.toggle button {
  cursor: ew-resize;
}

.v-list-item__content {
  display: block !important;
}

.drawer-title {
  height: 64px;
  position: -webkit-sticky;
  position: sticky;
  top: 0;
  z-index: 1;
  background-color: $theme-light-background;
  border-left: solid 1px $border-light-grey;
}

.search-input {
  margin-bottom: -29px;
}
</style>
