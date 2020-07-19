<template>
  <div>
    <v-dialog
      v-model="show"
      fullscreen
      transition="dialog-bottom-transition"
      flat>
      <v-card tile>
        <v-toolbar class="primary">
          <v-btn icon @click.native="close" dark>
            <v-icon>close</v-icon>
          </v-btn>
          <v-toolbar-title class="white--text">{{$store.getters.imageDialogFile.name}}</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-toolbar-items>
            <v-btn dark text @click.native="downloadFile($store.getters.imageDialogFile)">
              Download
              <v-icon right dark>file_download</v-icon></v-btn>
          </v-toolbar-items>
        </v-toolbar>
        <v-card-text>
          <div class="file_view_popup">
            <img :src="getFullUrl($store.getters.imageDialogFile)"/>
          </div>
        </v-card-text>
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
    show: {
      get () {
        return this.value
      },
      set (value) {
        this.$emit('input', value)
      }
    }
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
  },
}
</script>
