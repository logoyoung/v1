import * as types from './mutation-type';

const mutations = {
  [types.SET_UID](state,uid) {
  	state.uid = uid;
  },
  [types.SET_ENCPASS](state,encpass) {
  	state.encpass = encpass;
  }
};

export default mutations;