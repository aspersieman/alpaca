<template>
  <v-dialog
    v-model="dialog"
    max-width="600px"
    width="100%"
    persistent
    scrollable
    :fullscreen="$vuetify.breakpoint.xs || fullscreen"
  >
    <v-form
      v-model="valid"
      lazy-validation
    >
      <v-card>
        <v-card-title class="grey--text">
          <span v-if="mode == 'ADD'">
            Add a New Note
          </span>
          <span v-if="mode == 'UPDATE'">
            Update Note
          </span>
          <v-spacer />
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
            :rules="nameRules"
            label="Title"
          />
          <quill-editor
            ref="myQuillEditor"
            v-model="content"
          />
        </v-card-text>
        <v-divider />
        <v-card-actions>
          <v-btn
            v-if="mode == 'ADD'"
            text
            class="success mx-0 mt-3"
            :disabled="loading"
            @click="save()"
          >
            Add note
          </v-btn>
          <v-btn
            v-if="mode == 'UPDATE'"
            text
            class="success mx-0 mt-3"
            @click="update(note)"
          >
            Update note
          </v-btn>
          <v-spacer />
          <v-btn
            text
            class="error mx-0 mt-3"
            @click="close()"
          >
            Cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-form>
  </v-dialog>
</template>

<script>
import { mapGetters } from 'vuex';
import Editor from '@/components/core/Editor';
import { quillEditor } from 'vue-quill-editor'
import Quill from 'quill'

export default {
  components: {
    quillEditor,
  },

  data() {
    return {
      mode: 'ADD',
      dialog: false,
      valid: false,
      id: '',
      name: '',
      content: 'Loading...',
      subtitle: '',
      loading: false,
      fullscreen: false,
      onlyEdit: true,
      nameRules: [
        (v) => (v.length >= 2) || 'Cannot be shorter than 2 characters',
        (v) => !!v || 'Name is required',
      ],
    }
  },

  computed: {
    ...mapGetters('task', [
      'task',
    ]),
    ...mapGetters('note', [
      'note',
      'noteDialogAdd',
      'noteDialogUpdate'
    ]),
  },

  created() {
    const self = this;
    this.$store.subscribe((mutation) => {
      if (mutation.type === 'note/NOTE_DIALOG_ADD') {
        self.formReset();
        self.dialog = self.noteDialogAdd;
        self.mode = 'ADD';
        self.subtitle = self.task?.name || '';
      }
      if (mutation.type === 'note/NOTE_DIALOG_UPDATE') {
        self.formReset();
        self.dialog = self.noteDialogUpdate;
        self.id = self.note.id;
        self.name = self.note.name;
        self.content = self.note.content;
        self.$store.dispatch('task/getTask', self.note.task_id)
          .then(() => self.subtitle = self.task.name || '');
        self.mode = 'UPDATE';
      }
    });
  },
  methods: {
    close() {
      this.dialog = false;
      this.fullscreen = false;
    },

    formReset() {
      this.id = '';
      this.name = '';
      this.content = null;
      this.subtitle = '';
      this.isCreating = false;
      this.loading = false;
      this.fullscreen = false;
      this.onlyEdit = true;
    },

    save() {
      if (this.valid) {
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
        this.$store.dispatch('note/saveNote', note)
          .then(() => {
            this.$overlay.show(false);
            this.close();
            this.loading = false;
          })
          .catch(() => {
            this.loading = false;
          });
      }
    },

    update() {
      if (this.valid) {
        this.loading = true;
        const noteUpdate = {
          id: this.id,
          name: this.name,
          content: this.content,
        };
        this.$store.dispatch('note/updateNote', noteUpdate);
      }
    },

    maximise() {
      this.fullscreen = !this.fullscreen;
    },

    delta(value) {
        if(value === '') {
            return this.content = ''
        }
        this.content = value.getText()
    },
  }
}
</script>
<style scoped>
.title-container {
  padding: 0;
}
</style>
