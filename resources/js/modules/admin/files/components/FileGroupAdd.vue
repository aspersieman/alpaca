<template>
  <div class="component-wrap">
    <v-card>
      <v-form
        ref="fileGroupFormAdd"
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
                label="Group name"
                :rules="nameRules"
              />
            </v-flex>
            <v-flex xs12>
              <v-textarea
                v-model="description"
                label="Group description"
                :rules="descriptionRules"
              />
            </v-flex>
            <v-flex xs12>
              <v-btn
                :disabled="!valid"
                color="primary"
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
import EventNames from '@/common/EventNames';

export default {
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
  mounted() { },
  methods: {
    save() {
      const self = this;

      let payload = {
        name: self.name,
        description: self.description
      };

      self.isLoading = true;

      window.axios.post('/api/v1/file-groups',payload).then(function() {
        self.$flash.success({
          message: 'File roup added successfully'
        });

        self.isLoading = false;
        self.$eventBus.$emit(EventNames.FILE_GROUP_ADDED);

        self.$refs.fileGroupFormAdd.reset();
      }).catch(function (error) {
        self.isLoading = false;
        console.error(error);
        if (error.response) {
          self.$flash.error({
            message: 'Error adding file group'
          });
        }
      });
    }
  }
}
</script>
