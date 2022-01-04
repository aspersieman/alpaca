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
            ref="myQuillEditor"
            v-model="content"
            :options="quillOptions"
            class="note-editor"
          >
            <div id="toolbar" slot="toolbar">
              <!-- Add font size dropdown -->
              <select class="ql-size">
                <option value="small" />
                <!-- Note a missing, thus falsy value, is used to reset to default -->
                <option selected />
                <option value="large" />
                <option value="huge" />
              </select>
              <button class="ql-bold" />
              <button class="ql-italic" />
              <button class="ql-underline" />
              <button class="ql-strike" />
              <button class="ql-code-block" />
              <button
                class="ql-indent"
                value="+1"
              />
              <button
                class="ql-indent"
                value="-1"
              />
              <button
                class="ql-list"
                value="ordered"
              />
              <button
                class="ql-list"
                value="bullet"
              />
              <button
                class="ql-script"
                value="sub"
              />
              <button
                class="ql-script"
                value="super"
              />
              <select class="ql-header">
                <option value="1" />
                <!-- Note a missing, thus falsy value, is used to reset to default -->
                <option value="2" />
                <option value="3" />
                <option value="4" />
                <option value="5" />
                <option value="6" />
                <option selected />
              </select>
              <select class="ql-color" />
              <select class="ql-background" />
              <select class="ql-align" />
              <button class="ql-clean" />
              <button class="ql-link" />
              <button class="ql-image" />
              <button class="ql-video" />
            </div>
          </quill-editor>
        </v-card-text>
        <v-divider />
        <v-card-actions>
          <v-btn
            v-if="mode == 'ADD'"
            v-shortkey.once="['ctrl', 'enter']"
            text
            class="success mx-0 mt-3"
            :disabled="loading"
            @click="save()"
            @shortkey.propagate="save"
          >
            Save
          </v-btn>
          <v-btn
            v-if="mode == 'UPDATE'"
            v-shortkey.once="['ctrl', 'enter']"
            text
            class="success mx-0 mt-3"
            @click="update(note)"
            @shortkey.propagate="update(note)"
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
      quillOptions: {
        modules: {
          toolbar: '#toolbar'
        }
      }
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
    this.setup();
  },
  methods: {
    setup() {
      const self = this;
      this.$store.subscribe((mutation) => {
        if (mutation.type === 'note/NOTE_DIALOG_ADD') {
          self.formReset();
          self.dialog = self.noteDialogAdd;
          self.mode = 'ADD';
          self.subtitle = self.task?.name || '';
        }
        if (mutation.type === 'note/NOTE_DIALOG_UPDATE') {
          self.setupUpdate();
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

    setupUpdate() {
      this.formReset();
      this.dialog = this.noteDialogUpdate;
      this.id = this.note.id;
      this.name = this.note.name;
      this.content = this.note.content;
      if (this.note.task_id) {
        this.$store.dispatch('task/getTask', this.note.task_id)
          .then(() => this.subtitle = this.task.name || '');
      }
      this.mode = 'UPDATE';
    },

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
        console.log(this.node);
        this.$store.dispatch('note/saveNote', note)
          .then(() => {
            this.$overlay.show(false);
            this.loading = false;
          })
          .then(() => {
            console.log('save');
            console.log(this.note);
            this.setupUpdate();
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
