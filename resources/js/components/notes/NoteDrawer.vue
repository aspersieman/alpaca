<template>
  <div>
    <v-navigation-drawer
      ref="drawer"
      app
      right
      hide-overlay
      :value="drawerState"
      :width="navigation.width"
      :touchless="true"
      @input="updateNoteDrawerState"
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

        <v-list-item-content>
          <v-list-item-title>Notes</v-list-item-title>
          <v-list-item-subtitle>
            <v-icon small>
              task_alt
            </v-icon>
            {{ task.name }}
          </v-list-item-subtitle>
        </v-list-item-content>
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
              :notes="notes" 
            />
          </v-list-item-content>
        </v-list-item>
      </v-list>
      <template
        #append
      >
        <note-create @create-note="addNote" />
      </template>
    </v-navigation-drawer>
  </div>
</template>

<script>
import NoteList from './NoteList';
import NoteCreate from './NoteCreate';
import EventNames from '../../common/EventNames';
import NotesMixin from '../../mixins/NotesMixin';

export default {
  components: {
    NoteList,
    NoteCreate,
  },
  mixins: [ 
    NotesMixin
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
      }
    };
  },
  computed: {
    drawerState: {
      get() {
        return this.$store.getters.getNoteDrawer; 
      },
      set(state) {
        if (state) {
          this.close();
        }
      },
    },
  },
  created() {
    const self = this;
    self.navigation.width = self.$vuetify.breakpoint.mdAndUp ? self.defaultWidth : window.innerWidth;
    self.$eventBus.$on(EventNames.NOTE_DRAWER_TOGGLE, (value) => {
      if (value == true) {
        self.notes = self.getNotes();
        self.task = self.$store.state.task;
        if (self.$refs.drawer) {
          let w = self.$refs.drawer.$el.offsetWidth;
          self.$eventBus.$emit(EventNames.NOTE_DRAWER_RESIZE, w);
        }
      } else {
        self.notes = [];
        self.task = {
          name: null,
          due: null,
          status: null,
          order: null,
          timers: []
        };
      }
      self.$store.commit('setNoteDrawer', value);
    });
    self.$eventBus.$on(EventNames.NOTE_DELETED, (note) => {
      self.notes = self.getNotes()
        .filter(function(element) {
          return element.id !== note.id;
        });
    });
    self.$eventBus.$on(EventNames.NOTE_UPDATED, (note) => {
      self.notes = self.getNotes();
      const noteIndex = self.notes.findIndex((obj => obj.id == note.id));
      self.notes[noteIndex].name = note.name;
      self.notes[noteIndex].content = note.content;
    });
  },
  mounted() {
    if (this.$vuetify.breakpoint.mdAndUp) {
      this.setBorderWidth();
      this.setEvents();
    }
  },
  methods: {
    getNotes() {
      return this.$store.state.notes;
    },
    getTasks() {
      return this.$store.state.tasks;
    },
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
          self.$eventBus.$emit(EventNames.NOTE_DRAWER_RESIZE, f);
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

    close () {
      this.$eventBus.$emit(EventNames.NOTE_DRAWER_TOGGLE, false);
      this.$eventBus.$emit(EventNames.NOTE_DRAWER_RESIZE, 0);
    },

    updateNoteDrawerState(state) {
      if (state) {
        //this.close();
      }
    }
  },
}
</script>
<style scoped lang="scss">
@import "../../../sass/index";
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
  box-shadow: 0 2px 4px -1px rgba(0,0,0,.2), 0 4px 5px 0 rgba(0,0,0,.14), 0 1px 10px 0 rgba(0,0,0,.12);
  border-left: solid 1px $border-light-grey;
}
</style>
