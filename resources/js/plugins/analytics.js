import VueGtag from "vue-gtag";

export default [ VueGtag, {
  config: { id: "G-634ZXVJ4GH" },
  enabled: process.env.NODE_ENV === 'production'
} ];
