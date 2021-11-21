<template>
  <v-dialog
    v-model="dialog"
    max-width="600px"
    width="100%"
    persistent
    scrollable
    :fullscreen="$vuetify.breakpoint.xs || fullscreen"
    class="form-dialog"
  >
    <v-form
      v-model="valid"
      lazy-validation
    >
      <v-card>
        <v-card-title class="grey--text">
          Note
          <v-spacer />
          <span
            class="text-right"
          >
            <v-btn
              v-if="!$vuetify.breakpoint.xs"
              ref="maximiseButton"
              class="text-sm-caption text-right"
              icon
              text
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
            <v-btn
              class="text-sm-caption text-right"
              icon
              text
              x-small
              light
              color="error"
              @click="close()"
            >
              <v-icon>
                close
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
            class="note-editor"
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
            Save
          </v-btn>
          <v-btn
            v-if="mode == 'UPDATE'"
            text
            class="success mx-0 mt-3"
            @click="update(note)"
          >
            Save
          </v-btn>
          <success-checkmark
            v-if="updated || added"
            class="success-checkmark"
          />
        </v-card-actions>
      </v-card>
    </v-form>
  </v-dialog>
</template>

<script>
import { mapGetters } from 'vuex';
import { quillEditor } from 'vue-quill-editor'
import SuccessCheckmark from '@/components/core/SuccessCheckmark';

export default {
  components: {
    quillEditor,
    SuccessCheckmark
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
      updated: false,
      added: false,
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
        if (self.note.task_id) {
          self.$store.dispatch('task/getTask', self.note.task_id)
            .then(() => self.subtitle = self.task.name || '');
        }
        self.mode = 'UPDATE';
      }
      if (mutation.type === 'note/UPDATED') {
        self.updated = true;
        setTimeout(function () {
          self.updated = false;
        }, 3000);
      }
      if (mutation.type === 'note/ADDED') {
        self.added = true;
        setTimeout(function () {
          self.added = false;
        }, 3000);
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
