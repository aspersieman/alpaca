<template>
  <div class="component-wrap">
    <v-card>
      <div class="d-flex flex-column">
        <div class="flex-grow-1 pa-2">
          <v-text-field
            v-model="filters.name"
            prepend-icon="search"
            label="Filter by name or extension"
          />
        </div>
        <div class="flex-grow-1 pa-2">
          Show only:
        </div>
        <div class="flex-grow-1 pa-2">
          <span
            v-for="(group,i) in filters.fileGroupsHolder"
            :key="i"
          >
            <v-checkbox
              v-model="filters.fileGroupId[group.id]"
              :label="group.name"
            />
          </span>
        </div>
      </div>
    </v-card>

    <v-data-table
      :headers="headers"
      :options.sync="pagination"
      :items="items"
      :server-items-length="totalItems"
      class="elevation-1"
    >
      <template #body="{items}">
        <tbody>
          <tr
            v-for="item in items"
            :key="item.id"
          >
            <td>
              <v-btn
                icon
                small
                @click="showDialog('file_show',item)"
              >
                <v-icon class="blue--text">
                  mdi-magnify
                </v-icon>
              </v-btn>
              <v-btn
                icon
                small
                @click="trash(props.item)"
              >
                <v-icon class="red--text">
                  mdi-delete
                </v-icon>
              </v-btn>
            </td>
            <td>
              <v-avatar
                tile
                :size="'50px'"
                class="grey lighten-4"
              >
                <img :src="getFullUrl(item,50,'fit')">
              </v-avatar>
            </td>
            <td>{{ item.name }}</td>
            <td>{{ $appFormatters.formatByteToMB(item.size).toString() + ' MB' }}</td>
            <td>{{ item.group.name }}</td>
            <td>{{ $appFormatters.formatDate(item.created_at) }}</td>
          </tr>
        </tbody>
      </template>
    </v-data-table>
    <v-dialog
      v-model="dialogs.view.show"
      fullscreen
      :laze="false"
      transition="dialog-bottom-transition"
      :overlay="false"
    >
      <v-card>
        <v-toolbar class="primary">
          <v-btn
            icon
            dark
            @click.native="dialogs.view.show = false"
          >
            <v-icon>close</v-icon>
          </v-btn>
          <v-toolbar-title>{{ dialogs.view.file.name }}</v-toolbar-title>
          <v-spacer />
          <v-toolbar-items>
            <v-btn
              dark
              text
              @click.native="downloadFile(dialogs.view.file)"
            >
              Download
              <v-icon
                right
                dark
              >
                file_download
              </v-icon>
            </v-btn>
          </v-toolbar-items>
          <v-toolbar-items>
            <v-btn
              dark
              text
              @click.native="trash(dialogs.view.file)"
            >
              Delete
              <v-icon
                right
                dark
              >
                delete
              </v-icon>
            </v-btn>
          </v-toolbar-items>
        </v-toolbar>
        <v-card-text>
          <div class="file_view_popup">
            <div class="file_view_popup_link">
              <v-text-field
                text
                disabled
                :value="getFullUrl(dialogs.view.file)"
              />
            </div>
            <img :src="getFullUrl(dialogs.view.file)">
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import EventNames from '@/common/EventNames';

export default {
  components: {},
  data() {
    return {
      headers: [
        { text: 'Action', value: false, align: 'left', sortable: false },
        { text: 'Thumb', value: 'thumb', align: 'left', sortable: false },
        { text: 'Name', value: 'name', align: 'left', sortable: false },
        { text: 'Size', value: 'size', align: 'left', sortable: false },
        { text: 'Found In', value: 'group', align: 'left', sortable: false },
        { text: 'Date Created', value: 'created_at', align: 'left', sortable: false },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        rowsPerPage: 10
      },

      filters: {
        name: '',
        selectedGroupIds: '',
        fileGroupId: [],
        fileGroupsHolder: []
      },

      dialogs: {
        view: {
          file: {},
          show: false
        },
      }
    }
  },
  watch: {
    'filters.fileGroupId': window._.debounce(function(v) {
      let selected = [];
      window._.each(v,(v,k)=>{
        if(v) selected.push(k);
      });
      this.filters.selectedGroupIds = selected.join(',');
    },500),
    'filters.selectedGroupIds'() {
      this.loadFiles(()=>{});
    },
    'filters.name': window._.debounce(function() {
      this.loadFiles(()=>{});
    },500),
    'pagination.page':function(){
      this.loadFiles(()=>{});
    },
    'pagination.rowsPerPage':function(){
      this.loadFiles(()=>{});
    },
  },
  mounted() {
    const self = this;
    self.$eventBus.$on([EventNames.FILE_DELETED, EventNames.FILE_UPLOADED], function () {
      self.loadFiles(() => {});
    });
    self.loadFileGroups(() => {});
  },
  methods: {
    getFullUrl(file, width, action) {
      let w = width || 4000;
      let act = action || 'resize';
      return window.APP_GLOBALS.APP_URL +`/files/`+file.id+`/preview?w=`+w+`&action=`+act;
    },
    downloadFile(file) {
      window.open(window.APP_GLOBALS.APP_URL + '/files/'+file.id+'/download?file_token='+file.file_token);
      this.$debug(window.APP_GLOBALS.APP_URL + '/files/'+file.id+'/download?file_token='+file.file_token);
    },
    showDialog(dialog, data) {
      const self = this;
      switch (dialog){
        case 'file_show':
          self.dialogs.view.file = data;
          setTimeout(()=>{
            self.dialogs.view.show = true;
          },500);
          break;
      }
    },
    trash(file) {
      const self = this;
      self.$store.commit('showDialog',{
        type: "confirm",
        title: "Confirm Deletion",
        message: "Are you sure you want to delete this file?",
        okCb: ()=>{
          window.axios.delete('/api/v1/files/' + file.id).then(function() {
            self.$flash.success({
              text: 'File deleted successfully'
            });

            self.$eventBus.$emit(EventNames.FILE_DELETED);

            // maybe the action took place from view file
            // lets close it.
            self.dialogs.view.show = false;
          }).catch(function (error) {
            console.error(error);
            self.$flash.success({
              text: 'Error deleting file'
            });
          });
        },
        cancelCb: ()=>{
          console.log("CANCEL");
        }
      });
    },
    loadFileGroups(cb) {
      const self = this;
      let params = {
        paginate: 'no'
      };
      window.axios.get('/api/v1/file-groups',{ params: params }).then(function(response) {
        self.filters.fileGroupsHolder = response.data.data;
        cb();
      });
    },
    loadFiles(cb) {
      const self = this;
      let params = {
        name: self.filters.name,
        file_group_id: self.filters.selectedGroupIds,
        page: self.pagination.page,
        per_page: self.pagination.rowsPerPage
      };

      window.axios.get('/api/v1/files',{ params: params }).then(function(response) {
        self.items = response.data.data.data;
        self.totalItems = response.data.data.total;
        self.pagination.totalItems = response.data.data.total;
        (cb || Function)();
      });
    }
  }
}
</script>

<style scoped>
.file_view_popup {
  min-width: 500px;
  text-align: center;
}
</style>
