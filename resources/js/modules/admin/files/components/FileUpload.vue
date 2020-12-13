<template>
  <div class="component-wrap">
    <v-card>
      <v-form
        ref="fileFormUpload"
        lazy-validation
      >
        <v-container grid-list-md>
          <v-layout
            row
            wrap
          >
            <v-flex
              xs12
              sm8
            >
              <v-select
                v-model="uploadTo"
                label="Upload To File Group"
                :items="fileGroups"
                item-text="name"
                item-value="id"
              />
            </v-flex>
            <v-flex
              xs12
              sm4
            >
              <v-btn
                class="primary lighten-1"
                dark
                @click="clear()"
              >
                Clear
              </v-btn>
            </v-flex>
            <v-flex xs12>
              <div
                id="fileupload"
                class="dropzone"
              />
            </v-flex>
          </v-layout>
        </v-container>
      </v-form>
    </v-card>
  </div>
</template>

<script>
import methods from '../../../../common/Methods';

export default {
  data() {
    return {
      dropzone: null,
      fileGroups: [],
      uploadTo: '',
      addedFiles: []
    }
  },
  mounted() {
    const self = this;

    self.loadFileGroups(()=>{});
    self.initDropzone();

    self.$eventBus.$on(['file-group-added'],()=>{
        self.loadFileGroups(()=>{});
    });
  },
  methods: {
    clear() {
      const self = this;
      window._.each(
        self.addedFiles,
        f => {
          self.dropzone.removeFile(f);
        }
      );
      self.addedFiles = [];
    },
    upload() {
      const self = this;
      self.dropzone.processQueue();
    },
    initDropzone() {
      const self = this;
      window.Dropzone.autoDiscover = false;
      self.dropzone = new window.Dropzone("#fileupload", {
        url:'/api/admin/files',
        paramName: "file", // The name that will be used to transfer the file
        maxFilesize: 50, // 50MB
        uploadMultiple: true,
        //acceptedFiles: 'image/*',
        headers: {'X-CSRF-TOKEN' : window._token},
        autoProcessQueue: true
      });

      self.dropzone.on("addedfile", function(file) {
        if(!self.uploadTo) {
          self.$store.commit('showDialog',{
            type: "ok",
            icon: 'warning',
            title: "Warning",
            message: "Please choose file group to upload the file(s)",
            okCb: ()=>{ }
          });
          self.dropzone.removeFile(file);
        } else {
          self.addedFiles.push(file);
        }
      });

      self.dropzone.on('sending',(file,xhr,formData)=> {
          formData.append('file_group_id',self.uploadTo);
      });

      self.dropzone.on("complete", function(file) {
        self.$store.commit('showSnackbar',{
          message: "File(s) uploaded successfully.",
          color: 'success',
          duration: 3000
        });

        self.$eventBus.$emit('file-uploaded');
        methods.debug("File uploaded: ");
        methods.debug(file);
      });
    },
    loadFileGroups(cb) {
      const self = this;
      let params = {
          paginate: 'no'
      };
      window.axios.get('/api/admin/file-groups',{params: params}).then(function(response) {
        self.fileGroups = response.data.data;
        cb();
      });
    }
  }
}
</script>
<style scoped>
    #fileupload {
        min-height: 400px;
        background: grey;
        border: 1px dashed #eaeaea;
    }
    .dropzone .dz-preview.dz-image-preview {
        background: none;
    }
</style>
