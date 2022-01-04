<template>
  <div>
    <v-dialog
      v-show="showDialog"
      v-model="showDialog"
      absolute
      persistent
      max-width="450px"
    >
      <v-card>
        <v-card-title>
          <div class="headline">
            <v-icon v-if="dialogIcon">
              {{ dialogIcon }}
            </v-icon>
            {{ dialogTitle }}
          </div>
        </v-card-title>
        <v-card-text>{{ dialogMessage }}</v-card-text>
        <v-card-actions v-if="dialogType=='confirm'">
          <v-spacer />
          <v-btn
            color="orange darken-1"
            text
            @click="dialogCancel"
          >
            Cancel
          </v-btn>
          <v-btn
            color="green darken-1"
            text
            @click="dialogOk"
          >
            Ok
          </v-btn>
        </v-card-actions>
        <v-card-actions v-else-if="dialogType=='ok'">
          <v-spacer />
          <v-btn
            color="green darken-1"
            text
            @click="dialogOk"
          >
            Ok
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  name: 'CoreConfirmDialog',
  props: {
    value: Boolean
  },
  emits: [
    'close'
  ],
  computed: {
    showDialog: {
      get() {
        return this.$store.getters['dialog/dialogShow'];
      },
      set(val) {
        if(!val) {
          this.$store.dispatch('dialog/hide');
        }
      }
    },
    dialogType() {
      return this.$store.getters['dialog/dialogType'];
    },
    dialogTitle() {
      return this.$store.getters['dialog/dialogTitle'];
    },
    dialogMessage() {
      return this.$store.getters['dialog/dialogMessage'];
    },
    dialogIcon() {
      return this.$store.getters['dialog/dialogIcon'];
    },
  },
  methods: {
    getFullUrl(file, width, action) {
      let w = width || window.innerHeight;
      let act = action || 'resize';
      return window.APP_GLOBALS.APP_URL +`/files/`+file.id+`/preview?w=`+w+`&action=`+act;
    },
    downloadFile(file) {
      window.open(window.APP_GLOBALS.APP_URL + '/files/'+file.id+'/download?file_token='+file.file_token);
    },
    close() {
      this.$emit('close');
    },
    dialogOk() {
      this.$store.getters['dialog/dialogOkCb']();
      this.$store.dispatch('dialog/hide');
    },
    dialogCancel() {
      this.$store.getters['dialog/dialogCancelCb']();
      this.$store.dispatch('dialog/hide');
    }
  },
}
</script>
