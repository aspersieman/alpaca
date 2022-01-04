<template>
  <div>
    <div v-if="clickable">
      <v-tooltip bottom>
        <template #activator="{ on, attrs }">
          <v-avatar
            v-bind="avatar"
            style="cursor: pointer"
            :size="size"
            v-on="on"
            @click="click"
          >
            <v-img
              v-if="url && !status.loading"
              :src="url"
            />

            <slot
              v-if="!url && !status.loading"
              name="none"
            >
              <v-avatar
                :color="color"
                :size="size"
              >
                <span class="white--text headline">{{ initials }}</span>
              </v-avatar>
            </slot>

            <slot
              v-if="status.loading"
              name="loading"
              :status="status"
            >
              <v-progress-circular
                :value="status.progress"
                :size="32"
                :width="2"
                color="primary"
              />
            </slot>
          </v-avatar>
        </template>
        <span>Click to change</span>
      </v-tooltip>

      <input
        ref="file"
        type="file"
        name="file"
        accept="image/*"
        style="display: none"
        @change="select($event.target.name, $event.target.files)"
      >
    </div>

    <div v-else>
      <v-avatar
        v-bind="avatar"
        :size="size"
      >
        <avatar
          v-if="!url"
        />
        <v-img
          v-if="url"
          :src="url"
        />

        <slot
          v-else
          name="none"
        >
          <v-avatar
            :color="color"
            :size="size"
          >
            <span class="white--text headline">{{ initials }}</span>
          </v-avatar>
        </slot>
      </v-avatar>
    </div>
  </div>
</template>

<script>
import Avatar from '@/components/core/Avatar'

import AvatarMixin from '@/mixins/AvatarMixin';

export default {
  components: {
    Avatar
  },

  mixins: [
    AvatarMixin
  ],

  props: {
    request: {
      type: Function,
      required: true
    },
    name: {
      type: String,
      required: true
    },
    url: {
      type: String,
      required: false,
      default: null
    },
    rename: {
      type: Function,
      required: false,
      default: file => file.name
    },
    field: {
      type: String,
      required: false,
      default: 'file'
    },
    clickable: {
      type: Boolean,
      required: false,
      default: true
    },
    maxSize: {
      type: Number,
      required: false,
      default: 2048
    },
    headers: {
      type: Object,
      required: false,
      default: () => ({})
    },
    // TODO: Consider just using $attrs instead
    avatar: {
      type: Object,
      required: false,
      default: () => ({})
    },
    size: {
      type: Number,
      required: false,
      default: 128
    },
  },

  data: () => ({
    status: {
      loading: false,
      progress: 0
    },
  }),

  computed: {
    initials() {
      return this.getInitials(this.name);
    },

    color() {
      return this.getAvatarColour(this.name);
    }
  },

  methods: {
    click () {
      if (!this.status.loading) {
        if (!this.url) {
          this.choose()
        } else {
          this.replace()
        }
      } else {
        this.$emit('cancel')
      }
    },

    choose () {
      return this.$refs.file.click()
    },

    replace () {
      if (this.$listeners.replace) {
        this.$emit('replace')
      } else {
        this.choose()
      }
    },

    select (field, file) {
      const [ image ] = file
      const { maxSize } = this
      if (file.length > 0) {
        const size = image.size / 1024
        if (!image.type.match('image.*')) {
          this.$emit('error-type')
        } else if (size > maxSize) {
          this.$emit('error-size')
        } else {
          this.upload(image)
        }
      } else {
        this.$emit('error-empty')
      }
    },
    
    async upload (file) {
      this.status.loading = true
      const form = new FormData()
      const config = {
        headers: {
          'Content-Type': 'multipart/form-data',
          ...this.headers
        },
        onUploadProgress: function (event) {
          if (event.lengthComputable) {
            const progress = (event.loaded / event.total) * 100
            this.status.progress = progress
            this.$emit('progress', progress)
          }
        }.bind(this)
      }
      form.append(this.field, file, this.rename(file))
      try {
        const upload = await this.request(form, config)
        this.$emit('success', upload)
      } catch (e) {
        this.$emit('failed', e)
      } finally {
        this.status.loading = false
        this.status.progress = 0
      }
    }
  }
}
</script>
