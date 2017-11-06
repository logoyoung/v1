import * as types from './mutation-type';

const userLogin = ({commit},{uid,encpass}) => {
  commit(types.SET_UID,uid);
  commit(types.SET_ENCPASS,encpass);
};

const userLogout = ({commit}) => {
  commit(types.SET_UID,'');
  commit(types.SET_ENCPASS,'');
};

export {
  userLogin,
  userLogout
}