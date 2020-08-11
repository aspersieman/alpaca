import Vue from 'vue';
import Vuex from 'vuex';
window.moment = require('moment-timezone');

Vue.use(Vuex);
Vue.config.devtools = process.env.NODE_ENV === 'development';

export default new Vuex.Store({
  state: {
    /* TESTING REMOVE */
    articles: require('../../data/articles.json'),
    /* TESTING REMOVE */
    //logged in user
    user: null,
    // breadcrumbs
    breadcrumbs: [],
    drawer: false,
    // loader
    showLoader: false,
    // snackbar
    showSnackbar: false,
    snackbarMessage: "",
    snackbarColor: "",
    snackbarDuration: 3000,
    // dialog
    dialogShow: false,
    dialogType: "",
    dialogTitle: "",
    dialogMessage: "",
    dialogIcon: null,
    dialogOkCb: ()=>{},
    dialogCancelCb: ()=>{},
    imageDialogFile: {
      name: ""
    },
    menu: [],
    appName: "Alpaca",
    task: null,
    timer: null
  },
  mutations: {
    //logged in user
    setAuthUser(state, user) {
      state.user = user;
    },
    // breadcrumbs
    setBreadcrumbs(state, items) {
      items.unshift({label:"Home",to:{name:""}});
      state.breadcrumbs = items;
    },
    // drawer
    setDrawer: (state, payload) => (state.drawer = payload),
    toggleDrawer: state => (state.drawer = !state.drawer),
    // loader
    showLoader(state) {
      state.showLoader = true
    },
    hideLoader(state) {
      state.showLoader = false
    },
    // snackbar
    showSnackbar(state, data) {
      state.snackbarDuration = data.duration || 3000;
      state.snackbarMessage = data.message || "No message.";
      state.snackbarColor = data.color || "info";
      state.showSnackbar = true;
    },
    hideSnackbar(state) {
      state.showSnackbar = false;
    },
    // dialog
    showDialog(state, data) {
      state.dialogType = data.type || "confirm";
      state.dialogTitle = data.title;
      state.dialogMessage = data.message;
      state.dialogIcon = data.icon || null;
      state.dialogOkCb = data.okCb || function(){};
      state.dialogCancelCb = data.cancelCb || function(){};
      state.dialogShow = true;
    },
    hideDialog(state) {
      state.dialogShow = false;
    },
    dialogOk(state) {
      state.dialogOkCb();
      state.dialogShow = false;
    },
    dialogCancel(state) {
      state.dialogCancelCb();
      state.dialogShow = false;
    },
    setMenu(state, menu) {
      state.menu = menu.data;
    },
    setImageDialogFile(state, data) {
      state.imageDialogFile = data;
    },
    setActiveTask(state, task) {
      state.task = task;
    },
    setActiveTimer(state, timer) {
      state.timer = timer;
    },
  },
  getters: {
    //logged in user
    isLoggedIn(state) {
      return state.user !== null;
    },
    // get breadcrumbs
    getBreadcrumbs: state => {
      return state.breadcrumbs
    },
    // snackbar
    showSnackbar: state => {
      return state.showSnackbar
    },
    snackbarMessage: state => {
      return state.snackbarMessage
    },
    snackbarColor: state => {
      return state.snackbarColor
    },
    snackbarDuration: state => {
      return state.snackbarDuration
    },
    // dialog
    showDialog: state => {
      return state.dialogShow
    },
    dialogType: state => {
      return state.dialogType
    },
    dialogTitle: state => {
      return state.dialogTitle
    },
    dialogMessage: state => {
      return state.dialogMessage
    },
    dialogIcon: state => {
      return state.dialogIcon
    },
    imageDialogFile: state => {
      return state.imageDialogFile
    },
    menu: state => {
      return state.menu
    },
    awardDialog: state => {
      return state.awardDialog;
    },
    getActiveTask: state => {
      return state.task
    },
    getActiveTimer: state => {
      return state.timer
    },
  },
  actions: {
    loadMenu({commit}) {
      axios.get("/api/menu").then(menu => {
        commit("setMenu", menu.data);
      }).catch(error => {
        throw new Error(`API ${error}`);
      });
    }
  }
});
