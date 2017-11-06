/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _Vue = __webpack_require__(1);

	var _Vue2 = _interopRequireDefault(_Vue);

	var _jquery = __webpack_require__(2);

	var _jquery2 = _interopRequireDefault(_jquery);

	var _table = __webpack_require__(3);

	var _table2 = _interopRequireDefault(_table);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	(0, _jquery2.default)(document).ready(function () {
		new _Vue2.default({
			el: "#anchorRecommend",
			data: {
				viewPage: 'recommend',
				keyword: '',
				goSearch: false,
				isShow: false,
				viewPageList: ['recommend', 'all']
			},
			methods: {
				changeView: function changeView(index) {
					this.viewPage = this.viewPageList[index];
				},
				create: function create() {
					this.isShow = true;
				},
				search: function search() {
					this.goSearch = true;
				}
			},
			components: {
				recommend: {
					props: {
						keyword: String,
						goSearch: {
							type: Boolean,
							required: true
						},
						elementId: String,
						isShow: Boolean,
						viewPage: {
							type: String,
							required: true
						}
					},
					template: '<div class="tab-pane user-head-list active" id="news"><recommend-table :go-search.sync="goSearch" :keyword="keyword"  :element-id="elementId" :is-show.sync="isShow" :view-page="viewPage"></recommend-table></div>',
					components: {
						recommendTable: _table2.default
					}
				}
			}
		});
	}); /**
	     * Created by hantong on 16/12/1.
	     */

/***/ },
/* 1 */
/***/ function(module, exports) {

	module.exports = Vue;

/***/ },
/* 2 */
/***/ function(module, exports) {

	module.exports = window.$;

/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_script__ = __webpack_require__(4)
	__vue_template__ = __webpack_require__(14)
	module.exports = __vue_script__ || {}
	if (module.exports.__esModule) module.exports = module.exports.default
	if (__vue_template__) { (typeof module.exports === "function" ? module.exports.options : module.exports).template = __vue_template__ }
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), true)
	  if (!hotAPI.compatible) return
	  var id = "/Users/hantong/Sites/website/mobileGame/huanpeng/htdocs/admin2/vue/anchorRecommend/allAnchor/table.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _crossPage = __webpack_require__(5);

	var _crossPage2 = _interopRequireDefault(_crossPage);

	var _inputCheckBox = __webpack_require__(8);

	var _inputCheckBox2 = _interopRequireDefault(_inputCheckBox);

	var _dialog = __webpack_require__(11);

	var _dialog2 = _interopRequireDefault(_dialog);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	exports.default = {
	    props: {
	        elementId: {
	            type: String
	        },
	        pageSize: {
	            type: Number
	        },
	        sTime: {
	            type: String
	        },
	        eTime: {
	            type: String
	        },
	        viewPage: {
	            type: String,
	            require: true
	        },
	        keyword: {
	            type: String
	        },
	        goSearch: {
	            type: Boolean,
	            required: true
	        },
	        isShow: {
	            typ: Boolean,
	            required: true
	        }

	    },
	    data: function data() {
	        return {
	            dataList: [],
	            totalCounts: 1,
	            currentPage: 1,
	            params: ['checked', 'pic', 'nick', 'uid', 'ctime', 'liveStatus'],
	            publishOptionShow: false,
	            currentView: 'recommend',
	            currentKeyWord: '' //防止第一次重复加载
	        };
	    },
	    ready: function ready() {
	        this.requestList();
	        console.log(this.viewPage);
	    },
	    computed: {
	        todoSearch: function todoSearch() {
	            var self = this;
	            if (this.goSearch) {
	                this.requestList('', function () {});
	                self.goSearch = false;
	            }
	            return this.goSearch;
	        },
	        noSearch: function noSearch() {
	            if (this.currentKeyWord != this.keyword && this.keyword == '') {
	                console.log('no search event run');
	                this.requestList();
	            }
	            return this.keyword;
	        },
	        changeView: function changeView() {
	            if (this.viewPage != this.currentView) {
	                this.requestList(1);
	            }
	            return this.viewPage;
	        }
	    },
	    events: {
	        pageChange: function pageChange(num, doCallBack) {
	            console.log('emit run here:' + num);
	            this.requestList(num, doCallBack);
	        },
	        addAnchor: function addAnchor(nick, toIndex, callBack) {
	            if (!nick) {
	                alert('昵称不能为空');
	                return;
	            }
	            var self = this;
	            var url = $conf.recommend.api + 'live/addToWaitList.php';
	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie('admin_enc'),
	                type: getCookie('admin_type'),
	                addType: 1,
	                nick: nick
	            };
	            ajaxRequest({ url: url, data: data }, function (d) {
	                var uid = d.luid;
	                alert('添加成功');
	                if (toIndex) {
	                    self.publishRequest(uid, callBack);
	                } else {
	                    self.requestList();
	                    callBack();
	                }
	            }, function (d) {
	                alert(d.desc);
	            });
	        }
	    },
	    methods: {
	        requestList: function requestList(page) {
	            var self = this;
	            var doCallBack = typeof arguments[1] == 'function' ? arguments[1] : function () {};
	            var url = '';

	            this.currentView = this.viewPage;

	            if (self.currentView == 'recommend') {
	                url = $conf.recommend.api + 'live/getRecommentList.php';
	            } else {
	                url = $conf.recommend.api + 'live/getWaitList.php';
	            }

	            this.currentKeyWord = this.keyword;

	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie('admin_enc'),
	                type: getCookie('admin_type'),
	                size: this.pageSize,
	                page: page || self.currentPage || 1,
	                searchType: 1,
	                keyword: this.keyword
	            };

	            ajaxRequest({ url: url, data: data }, function (d) {
	                self.dataList.splice(0, self.dataList.length);
	                var list = d.list;
	                for (var i in list) {
	                    var data = {
	                        checked: false,
	                        //                            title:list[i].title,
	                        pic: list[i].head,
	                        nick: list[i].nick,
	                        uid: list[i].uid,
	                        ctime: list[i].ctime,
	                        liveStatus: list[i].isLiving == 1 ? '正在直播' : '暂未直播'
	                    };
	                    self.dataList.push(data);
	                }
	                self.totalCounts = parseInt(d.total) || 1;
	                doCallBack();
	            });
	        },
	        mark: function mark(index) {
	            var data = rebuildVueData(this.params, this.dataList[index]);
	            data.checked = !data.checked;
	            this.dataList.$set(index, data);
	        },
	        allSelect: function allSelect() {
	            for (var i in this.dataList) {
	                var data = rebuildVueData(this.params, this.dataList[i]);
	                data.checked = true;
	                this.dataList.$set(i, data);
	            }
	        },
	        inverseSelect: function inverseSelect() {
	            for (var i in this.dataList) {
	                this.mark(i);
	            }
	        },
	        deleteItem: function deleteItem() {
	            var list = [];
	            for (var i in this.dataList) {
	                if (this.dataList[i].checked) list.push(this.dataList[i].uid);
	            }
	            if (!list) return;
	            this.deleteItemkRequest(list);
	        },
	        deleteItemkRequest: function deleteItemkRequest(arr, callback) {
	            var self = this;
	            var url = $conf.recommend.api + 'live/removeWaitList.php';
	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie('admin_enc'),
	                type: getCookie('admin_type'),
	                luid: arr.join()
	            };
	            ajaxRequest({ url: url, data: data }, function () {
	                self.requestList();
	            });
	        },
	        cancelPublish: function cancelPublish(index) {
	            var uid = this.dataList[index].uid;
	            if (!uid) return;

	            var self = this;
	            var url = $conf.recommend.api + 'live/removeRecommend.php';
	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie('admin_enc'),
	                type: getCookie('admin_type'),
	                luid: uid
	            };
	            ajaxRequest({ url: url, data: data }, function () {
	                self.requestList();
	            }, function (d) {
	                alert(d.desc);
	            });
	        },
	        publish: function publish(index, callBack) {
	            var uid = this.dataList[index].uid;
	            this.publishRequest(uid, callBack);
	        },
	        publishRequest: function publishRequest(uid, callBack) {
	            if (!uid) return;
	            var self = this;
	            var url = $conf.recommend.api + 'live/addToRecommend.php';
	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie('admin_enc'),
	                type: getCookie('admin_type'),
	                luid: uid,
	                client: 2
	            };
	            ajaxRequest({ url: url, data: data }, function () {
	                self.requestList();
	                callBack && typeof callBack == 'function' && callBack(1);
	            }, function (d) {
	                alert(d.desc);
	                callBack && typeof callBack == 'function' && callBack(0);
	            });
	        },
	        //recommend
	        cancelTop: function cancelTop(index) {
	            this.toTop(1);
	        },
	        toTop: function toTop(index) {
	            var self = this;
	            var list = this.getTempList();
	            if (list.length < index + 1) return;

	            var newTop = list[index];
	            var newList = [];
	            var uidList = [];

	            delete list[index];

	            newList.push(newTop);
	            for (var i in list) {
	                if (list[i]) newList.push(list[i]);
	            }
	            for (var i in newList) {
	                uidList.push(newList[i].uid);
	            }

	            var url = $conf.recommend.api + 'live/changeOrder.php';
	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie('admin_enc'),
	                type: getCookie('admin_type'),
	                list: uidList.join()
	            };

	            ajaxRequest({ url: url, data: data }, function () {
	                self.dataList.splice(0, self.dataList.length);
	                for (var i in newList) {
	                    self.dataList.push(newList[i]);
	                }
	            });
	        },
	        getTempList: function getTempList() {
	            var list = [];
	            for (var i in this.dataList) {
	                list.push(rebuildVueData(this.params, this.dataList[i]));
	            }
	            return list;
	        }
	    },
	    components: {
	        pageCross: _crossPage2.default,
	        checkbox: _inputCheckBox2.default,
	        addDialog: _dialog2.default
	    }
	};
	// </script>
	// <template>
	//     <table v-if="viewPage=='all'" class="table table-striped table-bordered table-hover">
	//         <thead>
	//         <tr>
	//             <th class="checkbox-th"></th>
	//             <th>顺序</th>
	//             <th>头像</th>
	//             <th>昵称</th>
	//             <th>UID</th>
	//             <th>直播状态</th>
	//             <th>管理</th>
	//         </tr>
	//         </thead>
	//         <tbody>
	//         <tr v-for="(index,item) in dataList" @click="mark(index)">
	//             <td><checkbox :is-checked="item.checked"></checkbox></td>
	//             <td>{{index + 1}}</td>
	//             <td><img :src="item.pic"/></td>
	//             <td>{{item.nick}}</td>
	//             <td>{{item.uid}}</td>
	//             <td>{{item.liveStatus}}</td>
	//             <td><button class="btn" @click.stop="publish(index)">推荐</button></td>
	//         </tr>
	//         </tbody>
	//     </table>
	//     <table v-else class="table table-striped table-bordered table-hover">
	//         <thead>
	//         <tr>
	//             <th>顺序</th>
	//             <th>头像</th>
	//             <th>昵称</th>
	//             <th>UID</th>
	//             <th>直播状态</th>
	//             <th>管理</th>
	//         </tr>
	//         </thead>
	//         <tbody>
	//         <tr v-for="(index,item) in dataList" >
	//             <td v-if="index==0"><button class="btn" @click="cancelTop(index)">取消置顶</button></td>
	//             <td v-else><button class="btn" @click="toTop(index)">置顶</button></td>
	//             <td><img :src="item.pic"/></td>
	//             <td>{{item.nick}}</td>
	//             <td>{{item.uid}}</td>
	//             <td>{{item.liveStatus}}</td>
	//             <td><button class="btn" @click.stop="cancelPublish(index)">取消推荐</button></td>
	//         </tr>
	//         </tbody>
	//     </table>
	//     <nav v-if="viewPage=='all'" class="handle-option">
	//         <div class="col-md-3">
	//             <button class="btn btn-circle" @click="allSelect">全选</button>
	//             <button class="btn btn-circle" @click="inverseSelect">反选</button>
	//         </div>
	//         <div class="col-md-6" style="text-align: center">
	//             <page-cross :element-id="elementId" :page-size="pageSize" :total-counts="totalCounts" :current-page="currentPage"></page-cross>
	//         </div>
	//         <div class="col-md-3">
	//             <button class="recheck btn btn-circle pull-right" @click="deleteItem">删除</button>
	//         </div>
	//     </nav>
	//     <div v-show="false">{{todoSearch }}</div>
	//     <div v-show="false">{{ changeView }}</div>
	//     <div v-show="false">{{ noSearch}}</div>
	//     <add-dialog :is-show.sync="isShow"></add-dialog>
	// </template>
	//
	// <script>

/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_script__ = __webpack_require__(6)
	__vue_template__ = __webpack_require__(7)
	module.exports = __vue_script__ || {}
	if (module.exports.__esModule) module.exports = module.exports.default
	if (__vue_template__) { (typeof module.exports === "function" ? module.exports.options : module.exports).template = __vue_template__ }
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), true)
	  if (!hotAPI.compatible) return
	  var id = "/Users/hantong/Sites/website/mobileGame/huanpeng/htdocs/admin2/vue/components/crossPage.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 6 */
/***/ function(module, exports) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});
	// <template>
	//     <nav style="text-align: center;" v-if="elementId" id="page-{{elementId}}">
	//         <ul class="pageCross pagination"></ul>
	//     </nav>
	//     <div v-show="false">{{pageChange}}</div>
	// </template>
	//
	//
	// <script>
	exports.default = {
	    props: {
	        elementId: {
	            type: String
	        },
	        pageSize: {
	            type: Number,
	            default: 10
	        },
	        totalCounts: Number,
	        currentPage: Number,
	        visiblePages: {
	            type: Number,
	            default: 6
	        }

	    },
	    computed: {
	        pageChange: function pageChange() {
	            var self = this;
	            $('#page-' + self.elementId + ' .pageCross').jqPaginator('option', {
	                totalCounts: self.totalCounts ? self.totalCounts : 1
	            });
	            return this.totalCounts;
	        }
	    },
	    ready: function ready() {
	        var self = this;
	        var readyLoading = true;
	        $.jqPaginator('#page-' + self.elementId + ' .pageCross', {
	            totalCounts: this.totalCounts,
	            pageSize: this.pageSize,
	            visiblePages: this.visiblePages,
	            currentPage: this.currentPage,
	            onPageChange: function onPageChange(num, type) {
	                console.log('on change ' + num);
	                if (readyLoading) {
	                    readyLoading = false;
	                    return;
	                }
	                self.$dispatch('pageChange', num, function () {
	                    $('#page-' + self.elementId + ' .pageCross').jqPaginator('option', {
	                        totalCounts: self.totalCounts
	                    });
	                });
	            }
	        });
	    }
	};
	// </script>

/***/ },
/* 7 */
/***/ function(module, exports) {

	module.exports = "\n    <nav style=\"text-align: center;\" v-if=\"elementId\" id=\"page-{{elementId}}\">\n        <ul class=\"pageCross pagination\"></ul>\n    </nav>\n    <div v-show=\"false\">{{pageChange}}</div>\n";

/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_script__ = __webpack_require__(9)
	__vue_template__ = __webpack_require__(10)
	module.exports = __vue_script__ || {}
	if (module.exports.__esModule) module.exports = module.exports.default
	if (__vue_template__) { (typeof module.exports === "function" ? module.exports.options : module.exports).template = __vue_template__ }
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), true)
	  if (!hotAPI.compatible) return
	  var id = "/Users/hantong/Sites/website/mobileGame/huanpeng/htdocs/admin2/vue/components/inputCheckBox.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 9 */
/***/ function(module, exports) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});
	// <template>
	//     <div class="input-checkbox-div">
	//         <label for="" class="input-checkbox" :class="isChecked ? 'checked' : ''" @click="check"></label>
	//         <slot></slot>
	//     </div>
	// </template>
	//
	// <script>
	exports.default = {
	    props: ['isChecked'],
	    methods: {
	        check: function check() {
	            this.isChecked = !this.isChecked;
	        }
	    }
	};
	// </script>

/***/ },
/* 10 */
/***/ function(module, exports) {

	module.exports = "\n    <div class=\"input-checkbox-div\">\n        <label for=\"\" class=\"input-checkbox\" :class=\"isChecked ? 'checked' : ''\" @click=\"check\"></label>\n        <slot></slot>\n    </div>\n";

/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_script__ = __webpack_require__(12)
	__vue_template__ = __webpack_require__(13)
	module.exports = __vue_script__ || {}
	if (module.exports.__esModule) module.exports = module.exports.default
	if (__vue_template__) { (typeof module.exports === "function" ? module.exports.options : module.exports).template = __vue_template__ }
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), true)
	  if (!hotAPI.compatible) return
	  var id = "/Users/hantong/Sites/website/mobileGame/huanpeng/htdocs/admin2/vue/anchorRecommend/dialog.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 12 */
/***/ function(module, exports) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});
	// <template>
	//     <div v-show="isShow">
	//         <div class="modal-backdrop fade in" style="z-index:10050"></div>
	//         <div  class="modal fade" :class="isShow ? 'in' : ''" :style="isShow ? 'display:block;':''" role="dialog" aria-hidden="true">
	//             <div class="modal-dialog">
	//                 <div class="modal-content">
	//                     <div class="modal-header">
	//                         <button type="button" class="close" data-dismiss="" aria-hidden="true" @click="close"></button>
	//                         <h4 class="modal-title">发布</h4>
	//                     </div>
	//                     <div class="modal-body form">
	//                         <form action="#" class="form-horizontal form-row-seperated" style="padding: 20px 0px 20px 160px;">
	//                             <div class="row">
	//                                 <label for="anchorRecommendNick">昵称</label>
	//                                 <input type="text" id="anchorRecommendNick" value="" v-model="nick">
	//                             </div>
	//                             <div class="row">
	//                                 <input type="checkbox" id="to-index" value="false" v-model="toIndex">
	//                                 <label for="to-index">是否推荐到首页</label>
	//                             </div>
	//                         </form>
	//                     </div>
	//                     <div class="modal-footer">
	//                         <button id="modal-submit"  type="button" class="btn btn-primary yellow-crusta" @click="publish">确定</button>
	//                         <button id="modal-close" type="button" class="btn btn-default" data-dismiss="" @click="close">取消</button>
	//                     </div>
	//                 </div>
	//             </div>
	//         </div>
	//     </div>
	// </template>
	//
	// <script>
	exports.default = {
	    props: {
	        isShow: {
	            type: Boolean,
	            required: true
	        }
	    },
	    data: function data() {
	        return {
	            nick: '',
	            toIndex: false
	        };
	    },
	    methods: {
	        publish: function publish() {
	            var self = this;
	            this.$dispatch('addAnchor', self.nick, self.toIndex, function (d) {
	                self.close();
	            });
	        },
	        close: function close() {
	            this.isShow = false;
	            this.nick = '';
	            this.toIndex = false;
	        }
	    }
	};
	// </script>

/***/ },
/* 13 */
/***/ function(module, exports) {

	module.exports = "\n    <div v-show=\"isShow\">\n        <div class=\"modal-backdrop fade in\" style=\"z-index:10050\"></div>\n        <div  class=\"modal fade\" :class=\"isShow ? 'in' : ''\" :style=\"isShow ? 'display:block;':''\" role=\"dialog\" aria-hidden=\"true\">\n            <div class=\"modal-dialog\">\n                <div class=\"modal-content\">\n                    <div class=\"modal-header\">\n                        <button type=\"button\" class=\"close\" data-dismiss=\"\" aria-hidden=\"true\" @click=\"close\"></button>\n                        <h4 class=\"modal-title\">发布</h4>\n                    </div>\n                    <div class=\"modal-body form\">\n                        <form action=\"#\" class=\"form-horizontal form-row-seperated\" style=\"padding: 20px 0px 20px 160px;\">\n                            <div class=\"row\">\n                                <label for=\"anchorRecommendNick\">昵称</label>\n                                <input type=\"text\" id=\"anchorRecommendNick\" value=\"\" v-model=\"nick\">\n                            </div>\n                            <div class=\"row\">\n                                <input type=\"checkbox\" id=\"to-index\" value=\"false\" v-model=\"toIndex\">\n                                <label for=\"to-index\">是否推荐到首页</label>\n                            </div>\n                        </form>\n                    </div>\n                    <div class=\"modal-footer\">\n                        <button id=\"modal-submit\"  type=\"button\" class=\"btn btn-primary yellow-crusta\" @click=\"publish\">确定</button>\n                        <button id=\"modal-close\" type=\"button\" class=\"btn btn-default\" data-dismiss=\"\" @click=\"close\">取消</button>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n";

/***/ },
/* 14 */
/***/ function(module, exports) {

	module.exports = "\n    <table v-if=\"viewPage=='all'\" class=\"table table-striped table-bordered table-hover\">\n        <thead>\n        <tr>\n            <th class=\"checkbox-th\"></th>\n            <th>顺序</th>\n            <th>头像</th>\n            <th>昵称</th>\n            <th>UID</th>\n            <th>直播状态</th>\n            <th>管理</th>\n        </tr>\n        </thead>\n        <tbody>\n        <tr v-for=\"(index,item) in dataList\" @click=\"mark(index)\">\n            <td><checkbox :is-checked=\"item.checked\"></checkbox></td>\n            <td>{{index + 1}}</td>\n            <td><img :src=\"item.pic\"/></td>\n            <td>{{item.nick}}</td>\n            <td>{{item.uid}}</td>\n            <td>{{item.liveStatus}}</td>\n            <td><button class=\"btn\" @click.stop=\"publish(index)\">推荐</button></td>\n        </tr>\n        </tbody>\n    </table>\n    <table v-else class=\"table table-striped table-bordered table-hover\">\n        <thead>\n        <tr>\n            <th>顺序</th>\n            <th>头像</th>\n            <th>昵称</th>\n            <th>UID</th>\n            <th>直播状态</th>\n            <th>管理</th>\n        </tr>\n        </thead>\n        <tbody>\n        <tr v-for=\"(index,item) in dataList\" >\n            <td v-if=\"index==0\"><button class=\"btn\" @click=\"cancelTop(index)\">取消置顶</button></td>\n            <td v-else><button class=\"btn\" @click=\"toTop(index)\">置顶</button></td>\n            <td><img :src=\"item.pic\"/></td>\n            <td>{{item.nick}}</td>\n            <td>{{item.uid}}</td>\n            <td>{{item.liveStatus}}</td>\n            <td><button class=\"btn\" @click.stop=\"cancelPublish(index)\">取消推荐</button></td>\n        </tr>\n        </tbody>\n    </table>\n    <nav v-if=\"viewPage=='all'\" class=\"handle-option\">\n        <div class=\"col-md-3\">\n            <button class=\"btn btn-circle\" @click=\"allSelect\">全选</button>\n            <button class=\"btn btn-circle\" @click=\"inverseSelect\">反选</button>\n        </div>\n        <div class=\"col-md-6\" style=\"text-align: center\">\n            <page-cross :element-id=\"elementId\" :page-size=\"pageSize\" :total-counts=\"totalCounts\" :current-page=\"currentPage\"></page-cross>\n        </div>\n        <div class=\"col-md-3\">\n            <button class=\"recheck btn btn-circle pull-right\" @click=\"deleteItem\">删除</button>\n        </div>\n    </nav>\n    <div v-show=\"false\">{{todoSearch }}</div>\n    <div v-show=\"false\">{{ changeView }}</div>\n    <div v-show=\"false\">{{ noSearch}}</div>\n    <add-dialog :is-show.sync=\"isShow\"></add-dialog>\n";

/***/ }
/******/ ]);