export const state = {
  dialogShow: false,
  dialogType: "",
  dialogTitle: "",
  dialogMessage: "",
  dialogIcon: null,
  dialogOkCb: ()=>{},
  dialogCancelCb: ()=>{},
}

export const mutations = {
  DIALOG_SHOW: (state, payload) => {
    state.dialogShow = payload
  },
  DIALOG_TYPE: (state, payload) => {
    state.dialogType = payload
  },
  DIALOG_TITLE: (state, payload) => {
    state.dialogTitle = payload
  },
  DIALOG_MESSAGE: (state, payload) => {
    state.dialogMessage = payload
  },
  DIALOG_ICON: (state, payload) => {
    state.dialogIcon = payload
  },
  DIALOG_OK_CB: (state, payload) => {
    state.dialogOkCb = payload
  },
  DIALOG_CANCEL_CB: (state, payload) => {
    state.dialogCancelCb = payload
  },
}

export const actions = {
  dialogReset({commit}) {
    commit('DIALOG_TYPE', "");
    commit('DIALOG_TITLE', "");
    commit('DIALOG_MESSAGE', "");
    commit('DIALOG_ICON', null);
    commit('DIALOG_OK_CB', ()=>{});
    commit('DIALOG_CANCEL_CB', ()=>{});
  },

  dialogShow({commit, dispatch}, payload) {
    dispatch('dialogReset');
    commit('DIALOG_TYPE', payload.type || "confirm");
    commit('DIALOG_TITLE', payload.title);
    commit('DIALOG_MESSAGE', payload.message);
    commit('DIALOG_ICON', payload.icon || null);
    commit('DIALOG_OK_CB', payload.okCb || function(){});
    commit('DIALOG_CANCEL_CB', payload.cancelCb || function(){});
    commit('DIALOG_SHOW', true);
  },

  dialogHide({commit, dispatch}) {
    dispatch('dialogReset');
    commit('DIALOG_SHOW', false);
  },
}

export const getters = {
  dialogShow: state => {
    return state.dialogShow;
  },
  dialogType: state => {
    return state.dialogType;
  },
  dialogTitle: state => {
    return state.dialogTitle;
  },
  dialogMessage: state => {
    return state.dialogMessage;
  },
  dialogIcon: state => {
    return state.dialogIcon;
  },
  dialogOkCb: state => {
    return state.dialogOkCb;
  },
  dialogCancelCb: state => {
    return state.dialogCancelCb;
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
