<template>
  <div class="component-wrap">
    <v-card>
      <div class="d-flex flex-row">
        <div class="flex-grow-1 pa-2">
          <v-text-field
            v-model="filters.title"
            prepend-icon="search"
            label="Filter by permission title"
          />
        </div>
        <div class="flex-grow-1 pa-2 text-right">
          <v-btn
            class="primary lighten-1"
            dark
            @click="$router.push({name:'admin.users.permissions.create'})"
          >
            New permission
            <v-icon right>
              add
            </v-icon>
          </v-btn>
        </div>
      </div>
    </v-card>
    <v-data-table
      hide-default-header
      :headers="headers"
      :options.sync="pagination"
      :items="items"
      :server-items-length="totalItems"
      class="elevation-1"
    >
      <template #header="{props:{headers}}">
        <thead>
          <tr>
            <th
              v-for="header in headers"
              :key="header.text"
            >
              <span v-if="header.value=='key'"><v-icon>mdi-vpn_key</v-icon> {{ header.text }}</span>
              <span v-else-if="header.value=='created_at'"><v-icon>mdi-date_range</v-icon> {{ header.text }}</span>
              <span v-else>{{ header.text }}</span>
            </th>
          </tr>
        </thead>
      </template>
      <template #body="{items}">
        <tbody>
          <tr
            v-for="item in items"
            :key="item.id"
          >
            <td>
              <div class="text-center">
                <v-btn
                  class="ma-2"
                  outlined
                  fab
                  small
                  color="info"
                  @click="$router.push({name:'admin.users.permissions.edit',params:{id:item.id}})"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  class="ma-2"
                  outlined
                  fab
                  small
                  color="red"
                  @click="trash(item)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </div>
            </td>
            <td>{{ item.title }}</td>
            <td>{{ item.key }}</td>
            <td>{{ item.description }}</td>
            <td>{{ $appFormatters.formatDate(item.created_at) }}</td>
          </tr>
        </tbody>
      </template>
    </v-data-table>
  </div>
</template>

<script>
import methods from '../../../../common/Methods';

export default {
  data () {
    return {
      headers: [
        { text: 'Action', value: false, align: 'left', sortable: false },
        { text: 'Title', value: 'name', align: 'left', sortable: false },
        { text: 'Key', value: 'key', align: 'left', sortable: false },
        { text: 'Description', value: 'description', align: 'left', sortable: false },
        { text: 'Date Created', value: 'created_at', align: 'left', sortable: false },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        rowsPerPage: 10
      },
      filters: {
        title: '',
      },
      dialogs: {
        edit: {
          group: {},
          show: false
        },
        add: {
          show: false
        }
      }
    }
  },
  watch: {
    'pagination.page':function(){
      this.loadPermissions(()=>{});
    },
    'pagination.rowsPerPage':function(){
      this.loadPermissions(()=>{});
    },
    'filters.title': window._.debounce(function(){
      const self = this;
      self.loadPermissions(()=>{});
    },700),
  },
  mounted() {
    const self = this;
    self.$store.commit('setBreadcrumbs',[
      { label: 'Users', to: { name: 'admin.users.list' } },
      { label: 'Permissions', name: '' },
    ]);
  },
  methods: {
    trash(permission) {
      const self = this;
      self.$store.commit('showDialog',{
        type: "confirm",
        title: "Confirm Deletion",
        message: "Are you sure you want to delete this permission?",
        okCb: ()=>{
          window.axios.delete('/api/admin/permissions/' + permission.id).then(function(response) {
            self.$store.commit('showSnackbar',{
              message: response.data.message,
              color: 'success',
              duration: 3000
            });
            self.loadPermissions(()=>{});
          }).catch(function (error) {
            self.$store.commit('hideLoader');
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
    loadPermissions(cb) {
      const self = this;
      let params = {
        title: self.filters.title,
        page: self.pagination.page,
        per_page: self.pagination.rowsPerPage
      };
      window.axios.get('/api/admin/permissions',{params: params}).then(function(response) {
        self.items = response.data.data.data;
        self.totalItems = response.data.data.total;
        self.pagination.totalItems = response.data.data.total;
        (cb || Function)();
      });
    }
  }
}
</script>
