<template>
  <v-dialog
    v-model="dialog"
    max-width="600px"
    width="100%"
    persistent
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
      <v-card-subtitle
        v-if="subtitle != '' && subtitle != null"
      >
        <v-icon small>
          task_alt
        </v-icon>
        {{ subtitle }}
      </v-card-subtitle>
      <v-divider />
      <v-card-text>
        <v-text-field
          v-model="name"
          label="Title"
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
          @click="save()"
        >
          Add note
        </v-btn>
        <v-btn
          v-if="mode == 'UPDATE'"
          text
          class="success mx-0 mt-3"
          :loading="loading"
          :disabled="loading"
          @click="update(note)"
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

import NoteMixin from '@/mixins/NoteMixin';
import TaskMixin from '@/mixins/TaskMixin';
import Editable from '@/components/core/Editable';

export default {
  components: { Editable },
  mixins: [
    NoteMixin,
    TaskMixin
  ],
  data() {
    return {
      mode: 'ADD',
      dialog: false,
      name: '',
      content: '',
      subtitle: '',
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
  created() {
    const self = this;
    this.$eventBus.$on('note-dialog-add', (value) => {
      self.formReset();
      self.dialog = value;
      self.mode = 'ADD';
      self.subtitle = self.$store.getters['task/task']?.name || '';
    });
    self.$eventBus.$on('note-dialog-update', (value, note) => {
      self.formReset();
      self.dialog = value;
      self.note = note;
      self.name = self.note.name;
      self.content = self.note.content;
      self.subtitle = self.note.task?.name || '';
      self.mode = 'UPDATE';
    });
    self.$eventBus.$on([ 'note-added', 'note-updated' ], (value) => {
      self.formReset();
      self.dialog = value || false;
      this.loading = false;
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
      this.subtitle = '';
      this.isCreating = false;
      this.loading = false;
      this.fullscreen = false;
      this.onlyEdit = true;
      this.note = {
        id: '',
        name: '',
        content: ''
      };
    },
    save() {
      if (this.name != '' && this.content != '') {
        this.loading = true;
        const name = this.name;
        const content = this.content;
        const task_id = this.$store.getters['task/task'].id == 0 || typeof this.$store.getters['task/task'].id == 'undefined' ? null : this.$store.getters['task/task'].id;
        const note = {
          task_id,
          name,
          content,
        };
        this.$overlay.show(true);
        this.saveNote(note)
          .then(() => {
            this.$overlay.show(false);
            this.close();
          });
      }
    },
    update() {
      if (this.name != '' && this.content != '') {
        this.loading = true;
        const noteUpdate = {
          id: this.note.id,
          name: this.name,
          content: this.content,
        };
        this.$overlay.show(true);
        this.updateNote(noteUpdate)
          .then(() => {
            this.$overlay.show(false);
            this.close();
          });
      }
    },
    maximise() {
      this.fullscreen = !this.fullscreen;
    },
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
