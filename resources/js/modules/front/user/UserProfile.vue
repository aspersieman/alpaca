<template>
  <v-container
    id="user-profile-view"
    fluid
    tag="section"
  >
    <v-row justify="center">
      <v-col
        cols="12"
        md="8"
      >
        <app-card
          class="mt-4 text-center"
          icon="mdi-account-outline"
        >
          <avatar-uploader
            class="rounded-circle elevation-6 mt-n12 d-inline-block"
            :request="avatarUpdate"
            :name="user.name"
            :url="avatarUrl"
            :clickable="avatarClickable"
            :size="parseInt(128)"
            @success="avatarUpdateSuccess"
            @failed="avatarUpdateFailed"
          />
          <v-form
            ref="form"
            v-model="valid"
            lazy-validation
          >
            <v-container class="py-0">
              <v-row>
                <v-col
                  cols="12"
                  md="8"
                >
                  <v-text-field
                    disabled
                    label="Company (disabled)"
                  />
                </v-col>

                <v-col
                  cols="12"
                  md="4"
                >
                  <v-text-field
                    v-model="user.email"
                    label="Email address"
                    :rules="emailRules"
                  />
                </v-col>

                <v-col
                  cols="12"
                  md="8"
                >
                  <v-text-field
                    v-model="user.name"
                    label="Full name"
                    :rules="nameRules"
                  />
                </v-col>

                <v-col
                  cols="12"
                  md="4"
                >
                  <v-text-field
                    v-model="user.nickname"
                    label="Nick name"
                  />
                </v-col>

                <v-col
                  cols="12"
                  md="6"
                >
                  <v-text-field
                    v-model="password"
                    label="Password (Leave blank if unchange)"
                    type="password"
                    :rules="passwordRules"
                    @change="$refs.form.validate()"
                  />
                </v-col>
                <v-col
                  cols="12"
                  md="6"
                >
                  <v-text-field
                    v-model="passwordConfirm"
                    label="Confirm password"
                    type="password"
                    :rules="passwordConfirmRules"
                  />
                </v-col>

                <v-col
                  cols="12"
                  class="text-right"
                >
                  <v-btn
                    color="primary"
                    min-width="150"
                    @click="update()"
                  >
                    Save
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-form>
        </app-card>
      </v-col>

      <v-col
        v-if="$auth.user.groups.some(group => group.id === 1) > 0"
        cols="12"
        md="4"
      >
        <app-card class="mt-4">
          <v-card-text>
            <h4 class="text-h4 mb-3 text--primary">
              Access
            </h4>

            <h5 class="text-h5 mb-3 text--primary">
              Groups
            </h5>
            <p class="text--secondary">
              <v-chip
                v-for="group in user.groups"
                :key="group.id"
              >
                {{ group.name }}
              </v-chip>
            </p>

            <h5
              class="text-h5 mb-3 text--primary"
            >
              Permissions
            </h5>
            <div class="text--secondary">
              <v-chip
                v-for="(p,k) in user.permissions"
                :key="k"
                class="white--text"
                :class="{'green':(p.value==1),'red':(p.value==-1),'blue':(p.value==0)}"
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
              <div v-if="user.permissions && user.permissions.length === 0">
                No special permissions assigned.
              </div>
            </div>
          </v-card-text>
        </app-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import AppCard from '@/components/core/AppCard'
import AvatarUploader from '@/components/core/AvatarUploader'

import AvatarMixin from '@/mixins/AvatarMixin';

export default {
  name: 'UserProfileView',

  components: {
    AppCard,
    AvatarUploader
  },

  mixins: [
    AvatarMixin,
  ],

  data () {
    const self = this;
    return {
      avatarClickable: true,
      valid: false,
      user: {
        name: '',
        avatar: '',
        nickname: '',
        email: '',
        password: '',
        color: '',
        groups: [],
        permissions: []
      },
      nameRules: [
        (v) => !!v || 'Name is required',
      ],
      emailRules: [
        (v) => !!v || 'E-mail is required',
        (v) => /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'E-mail must be valid'
      ],
      password: '',
      passwordRules: [(v)=>{
        if(v && v.length > 0 && v.length < 8) {
          return 'Password needs minimum of 8 characters.'
        }
        return true;
      }],
      passwordConfirm: '',
      passwordConfirmRules: [
        (v) => !(v!==self.password) || 'Password do not match.',
      ],
    }
  },
  
  computed: {
    avatarUrl() {
      return this.user.avatar ? `/avatar/${this.user.id}?path=${this.user.avatar}` : null;
    }
  },

  mounted() {
    if (this.$auth.check) {
      this.user.id = this.$auth.user.id;
      this.user.name = this.$auth.user.name;
      this.user.email = this.$auth.user.email;
      this.user.nickname = this.$auth.user.nickname;
      this.user.avatar = this.$auth.user.avatar;
      this.user.groups = this.$auth.user.groups;
      this.user.permissions = this.$auth.user.permissions;
      this.user.color = this.getAvatarColour(this.user.name);
    }
  },

  methods: {
    update() {
      this.$refs.form.validate();
      if (this.valid) {
        const groups = this.user.groups.map((group)=>{
          return {
            id: group.id,
            shouldAttach: true
          };
        });
        console.log(groups);
        const user = {
          id: this.user.id,
          name: this.user.name,
          nickname: this.user.nickname,
          email: this.user.email,
          groups: groups,
          permissions: this.user.permissions
        };
        if (this.password) {
          user.password = this.password;
        }
        this.$store.dispatch('user/updateActive', user);
        this.clearForm()
      }
    },

    avatarUpdate(form, _config) {
      form.append('avatar_image', form.get('file'));
      form.delete('file'); // rename the field to the name expected by API
      return this.$store.dispatch('user/updateAvatar', { id: this.$auth.user.id, payload: form });
    },

    // avatarUpdateSuccess({ data }) {
    avatarUpdateSuccess() {
      this.$store.dispatch('user/getUser', this.user)
        .then((data) => {
          this.user = data.data
          this.$store.commit('auth/SET_AUTH_USER', data.data)
        })
    },

    avatarUpdateFailed(error) {
      this.$store.commit(
        'flash/showMessage',
        {text: 'Error setting avatar', color: 'red'}
      );
      console.error(error)
    },

    clearForm() {
      this.password = '';
      this.passwordConfirm = '';
    }
  }
}
</script>
