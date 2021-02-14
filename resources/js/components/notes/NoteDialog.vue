<template>
  <v-dialog
    v-model="dialog"
    max-width="600px"
    width="100%"
    scrollable
    :fullscreen="$vuetify.breakpoint.xs || fullscreen"
  >
    <span
      class="maximise-button text-right"
      :class="{ 'maximise-button-normal': !fullscreen, 'maximise-button-maximised': fullscreen }"
    >
      <v-btn 
        v-if="!$vuetify.breakpoint.xs"
        ref="maximiseButton"
        class="text-sm-caption text-right"
        fab
        x-small
        light
        @click="maximise()"
      >
        <v-icon
          v-if="!fullscreen"
        >
          fullscreen
        </v-icon>
        <v-icon
          v-if="fullscreen"
        >
          fullscreen_exit
        </v-icon>
      </v-btn>
    </span>
    <v-card>
      <v-card-title class="grey--text">
        <span v-if="mode == 'ADD'">
          Add a New Note
        </span>
        <span v-if="mode == 'UPDATE'">
          Update Note
        </span>
      </v-card-title>
      <v-divider />
      <v-card-text>
        <v-text-field
          v-model="name"
          label="Title"
          autofocus
        />
        <editable
          v-model="content"
          label="Content" 
          only-edit.sync="onlyEdit"
        />
      </v-card-text>
      <v-divider />
      <v-card-actions>
        <v-btn
          v-if="mode == 'ADD'"
          text
          class="success mx-0 mt-3"
          :loading="loading"
          :disabled="loading"
          @click="createNote()"
        >
          Add note
        </v-btn>
        <v-btn
          v-if="mode == 'UPDATE'"
          text
          class="success mx-0 mt-3"
          :loading="loading"
          :disabled="loading"
          @click="changeNote(note)"
        >
          Update note
        </v-btn>
        <v-spacer />
        <v-btn
          text
          class="error mx-0 mt-3"
          @click="close();"
        >
          Cancel
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>

import EventNames from '../../common/EventNames';
import NotesMixin from '../../mixins/NotesMixin';
import Editable from '../../components/core/Editable';

export default {
  components: { Editable },
  mixins: [ NotesMixin ],
  data() {
    return {
      mode: 'ADD',
      dialog: false,
      name: '',
      content: '',
      userId: '',
      loading: false,
      fullscreen: false,
      onlyEdit: true,
      note: {
        id: '',
        name: '',
        content: ''
      }
    }
  },
  computed: { },
  created() {
    const self = this;
    this.$eventBus.$on(EventNames.NOTE_ADD_DIALOG, (value) => {
      self.dialog = value;
      self.mode = 'ADD';
      self.formReset();
    });
    self.$eventBus.$on(EventNames.NOTE_UPDATE_DIALOG, (value) => {
      self.dialog = value;
      self.note = self.$store.state.note;
      self.name = self.note.name;
      self.content = self.note.content;
      self.mode = 'UPDATE';
    });
    self.$eventBus.$on([ EventNames.NOTE_ADDED, EventNames.NOTE_UPDATED ], (value) => {
      self.dialog = value || false;
      this.loading = false;
      this.close();
      this.formReset();
    });
  },
  methods: {
    close() {
      this.dialog = false;
      this.fullscreen = false;
    },
    formReset() {
      this.name = '';
      this.content = null;
      this.isCreating = false;
    },
    createNote() {
      if (this.name != '' && this.content != '') {
        this.loading = true;
        const name = this.name;
        const content = this.content;
        const task_id = this.$store.state.task.id == 0 || typeof this.$store.state.task.id == 'undefined' ? null : this.$store.state.task.id;
        const note = {
          task_id,
          name,
          content,
        };
        this.addNote(note);
      }
    },
    changeNote() {
      if (this.name != '' && this.content != '') {
        this.loading = true;
        const noteUpdate = {
          id: this.note.id,
          name: this.name,
          content: this.content,
        };
        this.updateNote(noteUpdate);
      }
    },
    maximise() {
      this.fullscreen = !this.fullscreen;
    }
  }
}
</script>
<style scoped>
.title-container {
  padding: 0;
}
.maximise-button {
  position: absolute;
  z-index: 1;
}
.maximise-button-normal {
  margin-left: 15px;
  margin-top: -15px;
  width: 600px;
}
.maximise-button-maximised {
  top: 10px;
  right: 0px;
  width: 100%;
}
</style>
