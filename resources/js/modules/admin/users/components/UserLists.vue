<template>
  <div class="component-wrap">
    <v-card class="pt-3">
      <div class="d-flex flex-row">
        <div class="flex-grow-1 pa-2">
          <v-btn
            class="primary lighten-1"
            dark
            @click="$router.push({name:'admin.users.create'})"
          >
            New user
            <v-icon
              right
              dark
            >
              add
            </v-icon>
          </v-btn>
        </div>
        <div class="flex-grow-1 pa-2">
          <v-btn
            class="primary lighten-1 float-right"
            dark
            @click="$router.push({name:'admin.users.groups.list'})"
          >
            Manage groups
            <v-icon
              right
              dark
            >
              group
            </v-icon>
          </v-btn>
          <v-btn
            class="primary lighten-1 float-right mr-2"
            dark
            @click="$router.push({name:'admin.users.permissions.list'})"
          >
            Manage permissions
            <v-icon
              right
              dark
            >
              vpn_key
            </v-icon>
          </v-btn>
        </div>
      </div>
      <div class="d-flex flex-lg-row flex-sm-column">
        <div class="flex-grow-1 pa-2">
          <v-text-field
            v-model="filters.name"
            filled
            prepend-icon="search"
            label="Filter by name"
            outlined
            dense
            clearable
          />
        </div>
        <div class="flex-grow-1 pa-2">
          <v-text-field
            v-model="filters.email"
            filled
            prepend-icon="email"
            label="Filter by email"
            outlined
            dense
            clearable
          />
        </div>
        <div class="flex-grow-1 pa-2">
          <v-autocomplete
            v-model="filters.groupId"
            filled
            multiple
            chips
            deletable-chips
            clearable
            prepend-icon="groups"
            label="Filter by groups"
            :items="filters.groupOptions"
            item-text="name"
            item-value="id"
            outlined
            dense
          />
        </div>
      </div>
    </v-card>
    <v-divider class="pb-2" />
    <v-data-table
      hide-default-header
      :headers="headers"
      :options.sync="pagination"
      :items="items"
      :server-items-length="usersTotal"
      class="elevation-1"
    >
      <template #header="{props:{headers}}">
        <thead>
          <tr>
            <th
              v-for="header in headers"
              :key="header.text"
            >
              <div
                v-if="header.value=='name'"
                :class="`text-${header.align}`"
              >
                <v-icon>mdi-person</v-icon>
                {{ header.text }}
              </div>
              <div
                v-else-if="header.value=='email'"
                :class="`text-${header.align}`"
              >
                <v-icon>mdi-email</v-icon>
                {{ header.text }}
              </div>
              <div
                v-else-if="header.value=='permissions'"
                :class="`text-${header.align}`"
              >
                <v-icon>mdi-vpn_key</v-icon>
                {{ header.text }}
              </div>
              <div
                v-else-if="header.value=='groups'"
                :class="`text-${header.align}`"
              >
                <v-icon>mdi-group</v-icon>
                {{ header.text }}
              </div>
              <div
                v-else-if="header.value=='last_login'"
                :class="`text-${header.align}`"
              >
                <v-icon>mdi-av_timer</v-icon>
                {{ header.text }}
              </div>
              <div
                v-else
                :class="`text-${header.align}`"
              >
                {{ header.text }}
              </div>
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
              <v-btn
                class="ma-2"
                outlined
                fab
                small
                icon
                color="info"
                @click="$router.push({name:'admin.users.edit',params:{id: String(item.id)}})"
              >
                <v-icon>
                  mdi-pencil
                </v-icon>
              </v-btn>
              <v-btn
                class="ma-2"
                outlined
                fab
                small
                icon
                color="red"
                @click="$store.dispatch('user/deleteUser', item)"
              >
                <v-icon>
                  mdi-delete
                </v-icon>
              </v-btn>
            </td>
            <td>{{ item.name }}</td>
            <td>{{ item.email }}</td>
            <td>
              <v-btn
                small
                outlined
                rounded
                color="grey"
                dark
                @click="showDialog('admin.user_permissions',item.permissions)"
              >
                Show
              </v-btn>
            </td>
            <td>
              <v-chip
                v-for="group in item.groups"
                :key="group.id"
                outlined
                color="grey"
              >
                {{ group.name }}
              </v-chip>
            </td>
            <td>{{ $appFormatters.formatDate(item.last_login) }}</td>
            <td class="text-center">
              <v-avatar outlined>
                <v-icon
                  v-if="item.active!=null"
                  class="green--text"
                >
                  check_circle
                </v-icon>
                <v-icon
                  v-else
                  class="grey--text"
                >
                  error_outline
                </v-icon>
              </v-avatar>
            </td>
          </tr>
        </tbody>
      </template>
    </v-data-table>
    <v-divider class="py-5" />
    <v-dialog
      v-model="dialogs.showPermissions.show"
      absolute
      max-width="300px"
    >
      <v-card>
        <v-card-title>
          <div class="headline">
            User permissions
            <v-icon>vpn_key</v-icon>
          </div>
        </v-card-title>
        <v-card-text>
          <v-chip
            v-for="(permission,key) in dialogs.showPermissions.items"
            :key="key"
            class="ma-1"
            :class="{'green':(permission.value==1),'red':(permission.value==-1),'blue':(permission.value==0)}"
          >
            <v-avatar
              v-if="permission.value==-1"
              class="red darken-4"
              title="Deny"
            >
              <v-icon>block</v-icon>
            </v-avatar>
            <v-avatar
              v-if="permission.value==1"
              class="green darken-4"
              title="Allow"
            >
              <v-icon>check_circle</v-icon>
            </v-avatar>
            <v-avatar
              v-if="permission.value==0"
              class="blue darken-4"
              title="Inherit"
            >
              <v-icon>swap_horiz</v-icon>
            </v-avatar>
            {{ permission.title }}
          </v-chip>
          <p v-if="dialogs.showPermissions.items.length==0">
            No permissions
          </p>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
window._ = require('lodash');

export default {
  data () {
    return {
      headers: [
        { text: 'Action', value: false, align: 'left', sortable: false },
        { text: 'Name', value: 'name', align: 'left', sortable: false },
        { text: 'Email', value: 'email', align: 'left', sortable: false },
        { text: 'Permissions', value: 'permissions', align: 'left', sortable: false },
        { text: 'Groups', value: 'groups', align: 'left', sortable: false },
        { text: 'Last Login', value: 'last_login', align: 'left', sortable: false },
        { text: 'Active', value: 'active', align: 'center', sortable: false },
      ],
      items: [],
      pagination: {
        itemsPerPage: 10
      },

      filters: {
        name: '',
        email: '',
        groupId: [],
        groupOptions: []
      },

      dialogs: {
        showPermissions: {
          items: [],
          show: false
        }
      }
    }
  },
  computed: {
    ...mapGetters('user', [
      'users',
      'usersTotal',
    ]),
  },
  watch: {
    pagination: {
      handler () {
        this.loadUsers(()=>{});
      },
    },
    'filters.name': window._.debounce(function(){
      const self = this;
      self.loadUsers(()=>{});
    },700),
    'filters.email': window._.debounce(function(){
      const self = this;
      self.loadUsers(()=>{});
    },700),
    'filters.groupId': window._.debounce(function(){
      const self = this;
      self.loadUsers(()=>{});
    },700),

  },
  mounted() {
    this.loadGroups(()=>{});

    this.$store.subscribe((mutation) => {
      if (
        [
          'user/ADDED',
          'user/UPDATED',
          'user/DELETED',
          'group/ADDED',
        ].includes(mutation.type)
      ) {
        this.loadUsers(()=>{});
      }
    });
  },
  methods: {
    showDialog(dialog, data) {
      const self = this;
      switch (dialog){
        case 'admin.user_permissions':
          self.dialogs.showPermissions.items = data;
          setTimeout(()=>{
            self.dialogs.showPermissions.show = true;
          },500);
          break;
      }
    },

    loadUsers(cb) {
      let params = {
        name: this.filters.name,
        email: this.filters.email,
        group_id: this.filters.groupId.join(","),
        page: this.pagination.page,
        per_page: this.pagination.itemsPerPage
      };

      this.$store.dispatch('user/getUsers', params)
        .then(() => {
          this.items = this.users;
          this.pagination.totalItems = this.usersTotal;
        });
    },

    loadGroups(cb) {
      const self = this;
      let params = {
        paginate: 'no'
      };
      window.axios.get('/api/v1/groups',{params: params}).then(function(response) {
        self.filters.groupOptions = response.data.data;
        cb();
      });
    },
  }
}
</script>
