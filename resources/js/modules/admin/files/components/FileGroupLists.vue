<template>
  <div class="component-wrap">
    <v-card>
      <div class="d-flex flex-row">
        <div class="flex-grow-1">
          <v-text-field
            v-model="filters.name"
            prepend-icon="search"
            label="Filter by name"
          />
        </div>
        <div class="flex-grow-1 text-right">
          <v-btn
            dark 
            class="primary lighten-1"
            @click="showDialog('file_group_add')"
          >
            New file group
            <v-icon right>
              add
            </v-icon>
          </v-btn>
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
                @click="showDialog('file_group_edit',item)"
              >
                <v-icon class="blue--text">
                  edit
                </v-icon>
              </v-btn>
              <v-btn
                icon
                small
                @click="trash(props.item)"
              >
                <v-icon class="red--text">
                  delete
                </v-icon>
              </v-btn>
            </td>
            <td>{{ item.name }}</td>
            <td>{{ item.description }}</td>
            <td>{{ item.file_count }}</td>
            <td>{{ $appFormatters.formatDate(item.created_at) }}</td>
          </tr>
        </tbody>
      </template>
    </v-data-table>

    <v-dialog
      v-model="dialogs.add.show"
      fullscreen
      transition="dialog-bottom-transition"
      :overlay="false"
    >
      <v-card>
        <v-toolbar class="primary">
          <v-btn
            icon
            @click.native="dialogs.add.show = false"
          >
            <v-icon>close</v-icon>
          </v-btn>
          <v-toolbar-title>Create New File Group</v-toolbar-title>
          <v-spacer />
          <v-toolbar-items>
            <v-btn
              text
              @click.native="dialogs.add.show = false"
            >
              Done
            </v-btn>
          </v-toolbar-items>
        </v-toolbar>
        <v-card-text>
          <file-group-add />
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="dialogs.edit.show"
      fullscreen
      :laze="false" 
      transition="dialog-bottom-transition"
      :overlay="false"
    >
      <v-card>
        <v-toolbar class="primary">
          <v-btn
            icon
            @click.native="dialogs.edit.show = false"
          >
            <v-icon>close</v-icon>
          </v-btn>
          <v-toolbar-title>Edit file group</v-toolbar-title>
          <v-spacer />
          <v-toolbar-items>
            <v-btn
              text
              @click.native="dialogs.edit.show = false"
            >
              Done
            </v-btn>
          </v-toolbar-items>
        </v-toolbar>
        <v-card-text>
          <file-group-edit :prop-file-group-id="dialogs.edit.fileGroup.id" />
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import FileGroupAdd from './FileGroupAdd.vue';
import FileGroupEdit from './FileGroupEdit.vue';
import methods from '../../../../common/Methods';
import EventNames from '../../../../common/EventNames';

export default {
  components: {
    FileGroupAdd,
    FileGroupEdit
  },
  data() {
    return {
      headers: [
        { text: 'Action', value: false, align: 'left', sortable: false },
        { text: 'Group Name', value: 'name', align: 'left', sortable: false },
        { text: 'Description', value: 'description', align: 'left', sortable: false },
        { text: 'Total Files', value: 'file_count', align: 'left', sortable: false },
        { text: 'Date Created', value: 'created_at', align: 'left', sortable: false },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        rowsPerPage: 10
      },

      filters: {
        name: '',
      },

      dialogs: {
        edit: {
          fileGroup: {},
          show: false
        },
        add: {
          show: false
        }
      }
    }
  },
  watch: {
    'filters.name': window._.debounce(function() {
      this.loadFileGroups(()=>{});
    }, 500),
    'pagination.page':function(){
      this.loadFileGroups(()=>{});
    },
    'pagination.rowsPerPage':function(){
      this.loadFileGroups(()=>{});
    },
  },
  mounted() {
    const self = this;
    self.$eventBus.$on([EventNames.FILE_GROUP_ADDED, EventNames.FILE_GROUP_UPDATED, EventNames.FILE_GROUP_DELETED],()=>{
      self.loadFileGroups(()=>{});
    });
  },
  methods: {
    trash(group) {
      const self = this;
      self.$store.commit('showDialog',{
        type: "confirm",
        title: "Confirm Deletion",
        message: "Are you sure you want to delete this file group?",
        okCb: ()=>{
          window.axios.delete('/api/admin/file-groups/' + group.id).then(function(response) {
            self.$store.commit('showSnackbar',{
              message: response.data.message,
              color: 'success',
              duration: 3000
            });
            self.$eventBus.$emit(EventNames.FILE_GROUP_DELETED);
          }).catch(function (error) {
            if (error.response) {
              self.$store.commit('showSnackbar',{
                message: error.response.data.message,
                color: 'error',
                duration: 3000
              });
            } else if (error.request) {
              methods.debug(error.request);
            } else {
              console.error('Error', error.message);
            }
          });
        },
        cancelCb: ()=>{
          console.log("CANCEL");
        }
      });
    },
    showDialog(dialog, data) {
      const self = this;
      switch (dialog){
        case 'file_group_edit':
          self.dialogs.edit.fileGroup = data;
          setTimeout(()=>{
            self.dialogs.edit.show = true;
          },500);
          break;
        case 'file_group_add':
          setTimeout(()=>{
            self.dialogs.add.show = true;
          },500);
          break;
      }
    },
    loadFileGroups(cb) {
      const self = this;
      let params = {
        name: self.filters.name,
        page: self.pagination.page,
        per_page: self.pagination.rowsPerPage
      };
      window.axios.get('/api/admin/file-groups',{params: params}).then(function(response) {
          self.items = response.data.data.data;
          self.totalItems = response.data.data.total;
          self.pagination.totalItems = response.data.data.total;
          (cb || Function)();
      });
    }
  }
}
</script>
