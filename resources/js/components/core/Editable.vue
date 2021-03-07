<template>
  <div>
    <span
      class="editable-label"
      :class="{ 'editable-label-active': active }"
    >
      <v-label 
        v-if="label != ''" 
      >
        {{ label }}
      </v-label>
    </span>
    <div class="edit-icon">
      <div 
        v-show="editing" 
        ref="me" 
        class="editable" 
        contenteditable 
        @input.stop="handleInput" 
        @blur="handleDone" 
        @keypress.stop="handleKeyPress"  
        v-text="value"
      />
      <div 
        v-if="!editing"
        class="editable"
        v-text="value" 
      />
    </div>
  </div>
</template>
<script>
export default {
  props: {
    value: {
      type: String,
      default: ''
    },
    label: {
      type: String,
      default: ''
    },
    maxLength: {
      type: Number,
      default: 10000
    },
    onlyEdit: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return { 
      editing: false,
      active: false
    }
  },
  created() {
    this.editing = this.onlyEdit;
  },
  mounted() {
    let contenteditable = document.querySelector('[contenteditable]')
    contenteditable.addEventListener('paste', function(e){           
      // Prevent the default pasting event and stop bubbling            
      e.preventDefault()
      e.stopPropagation()
      // Get the clipboard data
      let paste = (e.clipboardData || window.clipboardData).getData('text/plain')

      // Do something with paste like remove non-UTF-8 characters            
      paste = paste.replace(/\x0D/gi, "\n")          
      e.target.textContent += paste
    })
  },
  methods: {
    handleInput() {
    },
    handleKeyPress(e) {
      if (e.target.innerText.length >= this.maxLength) {
        e.preventDefault()
        return;
      }
    },
    handleDone(e) {
      this.$emit('input', e.target.innerText)
      this.active = false;
      if (!this.onlyEdit) {
        this.editing = false
      }
    }
  }
}
</script>
<style scoped lang="scss">
@import "../../../sass/_variables";
.edit-icon:hover:before {
  content: '✎';
  position: absolute;
  margin-left: -15px;
}
.editable {
  outline: 1px solid $border-dark-grey;
  padding: 4px;
  white-space: pre-wrap; 
  width: 100%;
  height: 100%;
  min-height: 30px;
}
.editable-label label {
  font-size: 12px;
}
.editable-label-active label {
  color: $brand-primary !important;
}
</style>
