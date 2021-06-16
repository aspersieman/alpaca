<template>
  <div class="component-wrap">
    <v-card>
      <v-card-title>
        <v-icon>groups</v-icon> Create Group
      </v-card-title>
      <v-divider />
      <v-form
        ref="groupFormAdd"
        v-model="valid"
        lazy-validation
      >
        <v-container grid-list-md>
          <v-layout
            row
            wrap
          >
            <v-flex xs12>
              <div class="body-2 white--text">
                Group details
              </div>
            </v-flex>
            <v-flex xs12>
              <v-text-field
                v-model="name"
                label="Group name"
                :rules="nameRules"
              />
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
                  close
                  class="white--text"
                  :class="{'green': (p.value==1), 'red': (p.value==-1), 'blue':(p.value==0) }"
                  @input="remove(k)"
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
                <div v-if="permissions.length === 0">
                  No special permissions assigned.
                </div>
              </div>
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
    return {
      valid: false,
      name: '',
      nameRules: [
        (v) => !!v || 'Name is required',
      ],
      permissions: [],
      selectedPermission: {},
      selectedPermissionValue: 0,
      options: {
          permissions: [],
          permissionValues:[
            { label: 'Allow', value: 1 },
            { label: 'Deny', value: -1 },
          ],
      }
    }
  },
  mounted() {
    const self = this;
    self.loadPermissions(()=>{});
    self.$store.commit('setBreadcrumbs',[
      { label: 'Users', to: { name: 'users.list' } },
      { label: 'Groups', to: {name: 'users.groups.list' } },
      { label: 'Create', to: '' },
    ]);
  },
  methods: {
    remove(i) {
      this.permissions.splice(i,1);
    },
    save() {
      const self = this;
      let payload = {
        name: self.name,
        permissions: self.permissions
      };
      window.axios.post('/api/v1/groups',payload).then(function() {
        self.$flash.success({
          text: 'Group added successfully'
        });
        self.$store.commit('hideLoader');
        self.$eventBus.$emit(EventNames.GROUP_ADDED);
        self.$refs.groupFormAdd.reset();
        self.permissions = [];
      }).catch(function (error) {
        console.error(error);
        if (error.response) {
          self.$flash.success({
            text: 'Error adding group'
          });
        }
      });
    },
    addSpecialPermission() {
      const self = this;
      window._.each(self.options.permissions,(p)=>{
        if (self.selectedPermission === p.key) {
          if(!self.existsInPermissions(self.selectedPermission)) {
            p.value = self.selectedPermissionValue;
            self.permissions.push(p);
          }
        }
      });
    },
    loadPermissions(cb) {
      const self = this;
      let params = {
        paginate: 'no',
        page: self.page
      };
      window.axios.get('/api/v1/permissions',{params: params}).then(function(response) {
        self.options.permissions = response.data.data;
        cb();
      });
    },
    existsInPermissions(permissionKey) {
      const self = this;
      let found = false;
      window._.each(self.permissions,(p)=>{
        if (p.permission === permissionKey) {
          found = true;
        }
      });
      return found;
    },
  }
}
</script>
