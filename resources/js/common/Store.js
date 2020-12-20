import Vue from 'vue';
import Vuex from 'vuex';
window.moment = require('moment-timezone');

Vue.use(Vuex);
Vue.config.devtools = process.env.NODE_ENV === 'development';

export default new Vuex.Store({
  state: {
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
    tasks: [],
    timer: null,
    notes: [],
    notesDrawer: false,
    activeTimer: null,
    activeTimerCounter: {
      seconds: 0,
      timer: {
        id: null,
        name: null,
        task_id: null, 
        user_id: null,
        started_at: null,
        stopped_at: null, 
        duration: 0,
        created_at: null,
        updated_at: null,
      },
      title: 'Timing...',
      task: {
        id: null,
        name: null,
        due: null,
        status: null,
        order: null,
      }
    },
    stoppedTimer: null,
    stoppedTimerValue: ""
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
    setSelectedTask(state, task) {
      state.task = task;
    },
    setTasks(state, tasks) {
      state.tasks = tasks;
    },
    setActiveTimer(state, timer) {
      state.activeTimer = timer;
    },
    setActiveTimerCounter(state, activeTimerCounter) {
      state.activeTimerCounter = activeTimerCounter;
    },
    setStoppedTimer(state, stoppedTimer) {
      state.stoppedTimer = stoppedTimer;
    },
    setStoppedTimerValue(state, value) {
      state.stoppedTimerValue = value;
    },
    setSelectedTimer(state, timer) {
      state.timer = timer;
    },
    setNotes(state, notes) {
      state.notes = notes;
    },
    setNotesDrawer(state, value) {
      state.notesDrawer = value;
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
    getSelectedTask: state => {
      return state.task
    },
    getTasks: state => {
      return state.tasks
    },
    getActiveTimer: state => {
      return state.activeTimer
    },
    getActiveTimerCounter: state => {
      return state.activeTimerCounter
    },
    getStoppedTimer: state => {
      return state.stoppedTimer
    },
    getStoppedTimerValue: state => {
      return state.stoppedTimerValue
    },
    getSelectedTimer: state => {
      return state.timer
    },
    getNotes: state => {
      return state.notes
    },
    getNotesDrawer: state => {
      return state.notesDrawer
    },
  },
  actions: {
    loadMenu({commit}) {
      window.axios.get("/api/menu").then(menu => {
        commit("setMenu", menu.data);
      }).catch(error => {
        throw new Error(`API ${error}`);
      });
    }
  }
});
