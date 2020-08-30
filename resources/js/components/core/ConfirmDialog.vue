<template>
  <div>
    <v-dialog v-show="showDialog" v-model="showDialog" absolute max-width="450px">
      <v-card>
        <v-card-title>
          <div class="headline"><v-icon v-if="dialogIcon">{{dialogIcon}}</v-icon> {{ dialogTitle }}</div>
        </v-card-title>
        <v-card-text>{{ dialogMessage }}</v-card-text>
        <v-card-actions v-if="dialogType=='confirm'">
          <v-spacer></v-spacer>
          <v-btn color="orange darken-1" text @click.native="dialogCancel">Cancel</v-btn>
          <v-btn color="green darken-1" text @click.native="dialogOk">Ok</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  name: 'CoreImageDialog',
  components: {},
  props: {
    value: Boolean
  },
  computed: {
    showDialog: {
      get() {
        return this.$store.getters.showDialog;
      },
      set(val) {
        if(!val) store.commit('hideDialog');
      }
    },
    dialogType() {
      return this.$store.getters.dialogType
    },
    dialogTitle() {
      return this.$store.getters.dialogTitle
    },
    dialogMessage() {
      return this.$store.getters.dialogMessage
    },
    dialogIcon() {
      return this.$store.getters.dialogIcon
    },
  },
  methods: {
    getFullUrl(file, width, action) {
      let w = width || window.innerHeight;
      let act = action || 'resize';
      return APP_GLOBALS.APP_URL +`/files/`+file.id+`/preview?w=`+w+`&action=`+act;
    },
    downloadFile(file) {
      window.open(APP_GLOBALS.APP_URL + '/files/'+file.id+'/download?file_token='+file.file_token);
    },
    close() {
      this.$emit('close');
    },
    dialogOk() {
      this.$store.commit('dialogOk');
    },
    dialogCancel() {
      this.$store.commit('dialogCancel');
    }
  },
}
</script>

