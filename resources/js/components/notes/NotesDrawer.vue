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
      @input="updateNotesDrawerState"
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
      <v-list-item fixed>
        <v-list-item-avatar>
          <v-icon>note</v-icon>
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title>Notes</v-list-item-title>
          <v-list-item-subtitle>{{ task.name }}</v-list-item-subtitle>
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
            <notes-list 
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
import NotesList from './NotesList';
import NoteCreate from './NoteCreate';

export default {
  components: {
    NotesList,
    NoteCreate,
  },
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
      navigation: {
        width: 250,
        borderSize: 3
      }
    };
  },
  computed: {
    drawerState: {
      get() {
        console.log("GET drawerState: "+this.$store.getters.getNotesDrawer);
        return this.$store.getters.getNotesDrawer; 
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
    self.navigation.width = self.$vuetify.breakpoint.mdAndUp ? 250 : window.innerWidth;
    self.$eventBus.$on('notes-drawer-toggle', (value) => {
      if (value == true) {
        self.notes = this.getNotes();
        self.task = this.$store.state.task;
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
      console.log('notes-drawer-toggle: '+value);
      this.$store.commit('setNotesDrawer', value);
    });
    self.$eventBus.$on('note-deleted', (note) => {
      self.notes = this.getNotes()
        .filter(function(element) {
          return element.id !== note.id;
        });
    });
    self.$eventBus.$on('note-updated', (note) => {
      self.notes = this.getNotes();
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

    addNote(note) {
      if(note.name != '') {
        this.loading = true;
        
        const newNote = {
          name: note.name,
          content: note.content,
          task_id: note.task_id
        };

        window.axios.post('/api/notes', newNote)
          .then(response => {
          this.loading = false;
          this.dialog = false;
          this.$eventBus.$emit('note-added');
          let notes = this.getNotes();
          notes.push(newNote);
          this.$store.commit('setNotes', notes);
          this.$store.commit('showSnackbar',{
            message: "Note added",
            color: 'success',
            duration: 1000
          });
          return response;
        })
          .catch(error => {
          console.error(error);
          this.$store.commit('showSnackbar',{
            message: error.response.data.message,
            color: 'error',
            duration: 3000
          });
          this.loading = false;
        });

      }
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
        el.style.width = f + "px";
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
      console.log("close");
      this.$eventBus.$emit('notes-drawer-toggle', false);
    },

    updateNotesDrawerState(state) {
      if (state) {
        //this.close();
      }
    }
  },
}
</script>
<style scoped>
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
</style>
