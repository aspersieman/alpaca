<template>
  <div class="component-wrap">
    <v-card dark>
      <v-form
        ref="fileGroupFormEdit"
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
                File group details
              </div>
            </v-flex>
            <v-flex xs12>
              <v-text-field
                v-model="name"
                dark
                label="Group name"
                :rules="nameRules"
              />
            </v-flex>
            <v-flex xs12>
              <v-textarea
                v-model="description"
                dark
                label="Group description"
                :rules="descriptionRules"
              />
            </v-flex>
            <v-flex xs12>
              <v-btn
                :disabled="!valid"
                color="primary"
                dark
                @click="save()"
              >
                Update
              </v-btn>
            </v-flex>
          </v-layout>
        </v-container>
      </v-form>
    </v-card>
  </div>
</template>

<script>
import EventNames from '@/common/EventNames';

export default {
  props: {
    propFileGroupId: {
      required: true,
      type: Number
    }
  },
  data() {
    return {
      valid: false,
      isLoading: false,
      name: '',
      nameRules: [
        (v) => !!v || 'Name is required',
      ],
      description: '',
      descriptionRules: [
        (v) => !!v || 'Description is required',
      ],
    }
  },
  watch: {
    propFileGroupId(v) {
      if(v) {
        this.loadFileGroup(()=>{});
      }
    }
  },
  mounted() { },
  methods: {
    save() {
      const self = this;
      let payload = {
        name: self.name,
        description: self.description
      };

      self.isLoading = true;

      window.axios.put('/api/admin/file-groups/' + self.propFileGroupId,payload).then(function(response) {
        self.$store.commit('showSnackbar',{
          message: response.data.message,
          color: 'success',
          duration: 3000
        });

        self.isLoading = false;
        self.$eventBus.$emit(EventNames.FILE_GROUP_UPDATED);
      }).catch(function (error) {
        self.isLoading = false;
        if (error.response) {
          self.$store.commit('showSnackbar',{
            message: error.response.data.message,
            color: 'error',
            duration: 3000
          });
        } else if (error.request) {
          this.$debug(error.request);
        } else {
          console.error('Error', error.message);
        }
      });
    },
    loadFileGroup(cb) {
      const self = this;
      window.axios.get('/api/admin/file-groups/' + self.propFileGroupId).then(function(response) {
        let Group = response.data.data;
        self.name = Group.name;
        self.description = Group.description;
        cb();
      });
    },
  }
}
</script>
