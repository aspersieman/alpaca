<template>
  <div class="component-wrap">
    <v-card>
      <v-card-title>
        <v-icon>vpn_key</v-icon> Create Permission
      </v-card-title>
      <v-divider class="mb-2" />
      <v-form
        ref="permissionFormAdd"
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
                Permission Details
              </div>
            </v-flex>
            <v-flex xs12>
              <v-text-field
                v-model="title"
                label="Permission Title"
                :rules="titleRules"
              />
            </v-flex>
            <v-flex xs12>
              <v-text-field
                v-model="permissionKey"
                label="Permission Key"
                :rules="permissionKeyRules"
              />
            </v-flex>
            <v-flex xs12>
              <v-textarea
                v-model="description"
                label="Description"
                :rules="descriptionRules"
              />
            </v-flex>
            <v-flex xs12>
              <v-btn
                :loading="isLoading"
                :disabled="!valid || isLoading"
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
      isLoading: false,
      title: '',
      titleRules: [
        (v) => !!v || 'Title is required',
      ],
      description: '',
      descriptionRules: [
        (v) => !!v || 'Description is required',
      ],
      permissionKey: '',
      permissionKeyRules: [
        (v) => !!v || 'Permission Key is required',
        (v) => (v && !v.match(/[^\w\.]+/g)) || 'Description cannot contain special characters',
      ],
    }
  },
  watch: {
    permissionKey(v) {
      if(v) this.permissionKey = v.replace(' ','.').toLowerCase();
    },
    title(v) {
      if(v) this.permissionKey = v.replace(' ','.').toLowerCase();
    }
  },
  mounted() {
    this.$store.commit('setBreadcrumbs',[
      {label:'Users',to:{name:'users.list'}},
      {label:'Permissions',to:{name:'users.permissions.list'}},
      {label:'Create',name:''},
    ]);
  },
  methods: {
    save() {
      const self = this;
      let payload = {
        title: self.title,
        description: self.description,
        key: self.permissionKey
      };
      self.isLoading = true;
      window.axios.post('/api/v1/permissions',payload).then(function(response) {
        self.$store.commit('showSnackbar',{
            message: response.data.message,
            color: 'success',
            duration: 3000
        });
        self.$eventBus.$emit(EventNames.PERMISSION_ADDED);

        self.$refs.permissionFormAdd.reset();
        self.permissions = [];
        self.isLoading = false;
      }).catch(function (error) {
        self.isLoading = false;
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
    }
  }
}
</script>
