<template>
  <v-menu
    v-model="dateRangeMenu"
    :close-on-content-click="false"
    :nudge-right="40"
    transition="scale-transition"
    offset-y
    min-width="290px"
  >
    <template #activator="{ on, attrs }">
      <v-text-field
        v-model="selected"
        class="date-range-input"
        :label="label"
        :prepend-icon="getIcon()"
        :clearable="clearable"
        :style="{'width': width, 'max-width': width}"
        readonly
        value
        outlined
        dense
        v-bind="attrs"
        v-on="on"
        @input="$emit('input', $event)"
        @blur="date = (value)"
      />
    </template>
    <v-date-picker
      v-model="selected"
      light
      range
    />
  </v-menu>
</template>

<script>
export default {
  props: {
    value: {
      required: false,
      type: Array,
      default: function() {
        return [ new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().substr(0, 10), new Date().toISOString().substr(0, 10) ]
      }
    },
    showIcon: {
      required: false,
      type: Boolean,
      default: false
    },
    clearable: {
      required: false,
      type: Boolean,
      default: false
    },
    label: {
      required: false,
      type: String,
      default: "Date range"
    },
    width: {
      required: false,
      type: String,
      default: "100%"
    }
  },

  emits: [
    'input'
  ],

  data() {
    return {
      dateRangeMenu: false,
      dates: [ new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().substr(0, 10), new Date().toISOString().substr(0, 10) ],
    }
  },
  computed: {
    selected: {
      get() {
        return this.value
      },
      set(value) {
        this.$emit('input', value)
      }
    },
    dateRange () {
      return this.dates.join(' ~ ')
    },
  },
  methods: {
    parseDate (dates) {
      if (!dates) { return null }
      return dates.join(' ~ ')
    },
    getIcon() {
      if (this.showIcon) {
        return 'mdi-calendar';
      } else {
        return '';
      }
    }
  }
}
</script>

<style scoped lang="scss">
@import "../../../sass/index";
</style>
