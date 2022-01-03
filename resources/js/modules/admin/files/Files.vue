<template>
  <div class="page_wrap_vue">
    <v-tabs
      v-model="active"
      color="primary" 
    >
      <v-tab
        key="files" 
        href="#files"
        ripple
      >
        Files
      </v-tab>
      <v-tab
        key="manage-groups"
        href="#manage-groups"
        ripple
      >
        Manage file groups
      </v-tab>
      <v-tab
        key="upload"
        href="#upload"
        ripple
      >
        Upload
      </v-tab>

      <v-tab-item value="files">
        <v-card flat>
          <v-card-text>
            <file-lists />
          </v-card-text>
        </v-card>
      </v-tab-item>
      <v-tab-item value="manage-groups">
        <v-card flat>
          <v-card-text>
            <file-group-lists />
          </v-card-text>
        </v-card>
      </v-tab-item>
      <v-tab-item value="upload">
        <v-card flat>
          <v-card-text>
            <file-upload />
          </v-card-text>
        </v-card>
      </v-tab-item>
    </v-tabs>
  </div>
</template>

<script>
import FileGroupLists from './components/FileGroupLists.vue';
import FileUpload from './components/FileUpload.vue';
import FileLists from './components/FileLists.vue';

export default {
  components: {
    FileUpload,
    FileGroupLists,
    FileLists
  },
  data() {
    return {
      active: 'files'
    }
  },
  watch: {
    active(v) {
      console.log('active tab: ' + v);
    }
  },
}
</script>

<style scoped="">
.finder_wrap {
  padding: 0 20px;
}
</style>
