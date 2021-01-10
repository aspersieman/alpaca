<template>
  <div>
    <v-card>
      <v-card-title>
        <v-icon>person</v-icon> Create User
      </v-card-title>
      <v-divider />
      <v-form
        ref="userFormAdd"
        v-model="valid"
        lazy-validation
      >
        <v-container grid-list-md>
          <v-layout
            row
            wrap
          >
            <v-flex
              xs12
              sm6
            >
              <v-text-field
                v-model="name"
                label="First name"
                :rules="nameRules"
              />
            </v-flex>
            <v-flex
              xs12
              sm6
            >
              <v-text-field
                v-model="email"
                label="Email"
                :rules="emailRules"
              />
            </v-flex>
            <v-flex
              xs12
              sm6
            >
              <v-text-field
                v-model="password"
                label="Password"
                type="password"
                :rules="passwordRules"
              />
            </v-flex>
            <v-flex
              xs12
              sm6
            >
              <v-text-field
                v-model="passwordConfirm"
                label="Confirm password"
                type="password"
                :rules="passwordConfirmRules"
              />
            </v-flex>
            <v-flex
              xs12
              sm6
            >
              <v-switch
                v-model="active"
                label="Pre-Activate Account"
              />
            </v-flex>
            <v-flex xs12>
              <v-spacer />
            </v-flex>
            <v-flex xs12>
              <h1 class="title">
                <v-icon>vpn_key</v-icon>
                Special permissions
              </h1>
              <v-alert
                color="info"
                icon="info"
                :value="true"
              >
                Special permissions are permission exclusive to this user. Permissions defined here
                are more superior than any permission that is in his group. So if the User belongs to a group that has permission to "create something"
                but then is denied to "create something" here, the user will be denied on that permission. In short, special permissions
                has high priority that group permissions.
              </v-alert>
              <v-divider />
            </v-flex>
            <v-flex
              xs12
              sm4
            >
              <v-select
                v-model="selectedPermission"
                label="Select Permission"
                :items="options.permissions"
                item-text="title"
                item-value="key"
              />
            </v-flex>
            <v-flex
              xs12
              sm4
            >
              <v-select
                v-model="selectedPermissionValue"
                label="Permission Value"
                :items="options.permissionValues"
                item-text="label"
                item-value="value"
              />
            </v-flex>
            <v-flex
              xs12
              sm4
            >
              <v-btn
                class="primary lighten-1"
                dark
                @click="addSpecialPermission()"
              >
                Add permission
                <v-icon right>
                  add
                </v-icon>
              </v-btn>
            </v-flex>
            <v-flex xs12>
              <div class="permissions_container">
                <v-chip
                  v-for="(p,k) in permissions" 
                  :key="k"
                  class="white--text"
                  :class="{'green':(p.value==1),'red':(p.value==-1),'blue':(p.value==0)}"
                  @click:close="removePermission(k)"
                >
                  <v-avatar
                    v-if="p.value==-1"
                    class="red darken-4"
                    title="Deny"
                  >
                    <v-icon>block</v-icon>
                  </v-avatar>
                  <v-avatar
                    v-if="p.value==1"
                    class="green darken-4" 
                    title="Allow"
                  >
                    <v-icon>check_circle</v-icon>
                  </v-avatar>
                  <v-avatar
                    v-if="p.value==0"
                    class="blue darken-4"
                    title="Inherit"
                  >
                    <v-icon>swap_horiz</v-icon>
                  </v-avatar>
                  {{ p.title }}
                </v-chip>
                <div
                  v-if="permissions.length===0"
                  class="info pa-2"
                >
                  No special permissions assigned.
                </div>
              </div>
            </v-flex>
            <v-flex xs12>
              <v-spacer />
            </v-flex>
            <v-flex xs12>
              <h1 class="title">
                <v-icon>people</v-icon>
                Groups
              </h1>
              <v-divider />
            </v-flex>
            <v-flex xs12>
              <v-switch
                v-for="(g,k) in options.groups"
                :key="k"
                v-model="groups[g.id]"
                :label="g.name"
              />
            </v-flex>
            <v-flex xs12>
              <v-btn
                :disabled="!valid"
                color="primary"
                dark
                @click="save()"
              >
                Save
              </v-btn>
            </v-flex>
          </v-layout>
        </v-container>
      </v-form>
    </v-card>
  </div>
</template>

<script>
import EventNames from '../../../../common/EventNames';

export default {
  data() {
    const self = this;
    return {
      valid: false,
      name: '',
      nameRules: [
        (v) => !!v || 'Name is required',
      ],
      email: '',
      emailRules: [
        (v) => !!v || 'E-mail is required',
        (v) => /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'E-mail must be valid'
      ],
      password: '',
      passwordRules: [
        (v) => !!v || 'Password is required',
        (v) => v && v.length >= 8 || 'Password must be atleast 8 characters.',
      ],
      passwordConfirm: '',
      passwordConfirmRules: [
        (v) => !(v!==self.password) || 'Password do not match.',
      ],
      permissions: [],
      groups: [],
      active:'',
      options: {
        permissions: [],
        permissionValues:[
          {label:'Allow', value:1},
          {label:'Deny', value:-1},
          {label:'Inherit', value:0},
        ],
        groups: []
      },
      selectedPermission: {},
      selectedPermissionValue: 0,

      alert: {
        show: false,
        icon: '',
        color: '',
        message: ''
      }
    }
  },
  mounted() {
    console.log('components.UserFormAdd.vue');
    const self = this;
    self.loadPermissions();
    self.loadGroups();
    self.$store.commit('setBreadcrumbs',[
      {label:'Users',to:{name:'users.list'}},
      {label:'Create',to:''},
    ]);
  },
  methods: {
    removePermission(i) {
      const self = this;
      self.permissions.splice(i,1);
    },
    save() {
      const self = this;
      let payload = {
        name: self.name,
        email: self.email,
        password: self.password,
        active: self.active ? window.moment().format('YYYY-MM-DD') : null,
        permissions: self.permissions,
        groups: self.groups,
      };
      self.$store.commit('showLoader');
      window.axios.post('/api/admin/users',payload).then(function(response) {
        self.$store.commit('showSnackbar',{
          message: response.data.message,
          color: 'success',
          duration: 3000
        });
        self.$eventBus.$emit(EventNames.USER_ADDED);
        self.$store.commit('hideLoader');
        // reset
        self.$refs.userFormAdd.reset();
        self.permissions = [];
      }).catch(function (error) {
        self.$store.commit('hideLoader');
        if (error.response) {
          self.$store.commit('showSnackbar',{
            message: error.response.data.message,
            color: 'error',
            duration: 3000
          });
        } else if (error.request) {
          console.log(error.request);
        } else {
          console.log('Error', error.message);
        }
      });
    },
    addSpecialPermission() {
      const self = this;
      window._.each(self.options.permissions,(p)=>{
        if(self.selectedPermission===p.key) {
          if(!self.existsInPermissions(self.selectedPermission)) {
            p.value = self.selectedPermissionValue;
            self.permissions.push(p);
          }
        }
      });
    },
    existsInPermissions(permissionKey) {
      const self = this;
      let found = false;
      window._.each(self.permissions,(p)=>{
        if(p.key===permissionKey) found = true;
      });
      return found;
    },
    loadPermissions(cb) {
      const self = this;
      let params = {
        paginate: 'no'
      };
      window.axios.get('/api/admin/permissions',{params: params}).then(function(response) {
        self.options.permissions = response.data.data;
        cb();
      });
    },
    loadGroups(cb) {
      const self = this;
      let params = {
        paginate: 'no'
      };
      window.axios.get('/api/admin/groups',{params: params}).then(function(response) {
        self.options.groups = response.data.data;
        window._.each(self.options.groups,g=>{
          g.selected = false;
        });
        cb();
      });
    }
  }
}
</script>
