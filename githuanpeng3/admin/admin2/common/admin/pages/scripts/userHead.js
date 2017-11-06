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

	var _headCheckBox = __webpack_require__(36);

	var _headCheckBox2 = _interopRequireDefault(_headCheckBox);

	var _headCheckTable = __webpack_require__(42);

	var _headCheckTable2 = _interopRequireDefault(_headCheckTable);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	// function getHeadInfo(doCallBack) {
	//     var url = $conf.user.api + 'getWaitPassList.php';
	//     var data = {uid:getCookie('admin_uid'), encpass:getCookie('admin_enc')}
	//     ajaxRequest({url:url,data:data}, function (d) {
	//         var list = d.data;
	//         var arr = [];
	//         for(var i in list){
	//             var data = list[i];
	//             arr.push({
	//                 uid:data.uid,
	//                 stat:1,
	//                 imgUrl:data.pic
	//             })
	//         }
	//         typeof doCallBack =='function' && doCallBack(arr);
	//     });
	// }
	//
	// getHeadInfo(function (d) {
	//     for(var i in d){
	//         dataList.push(d[i]);
	//     }
	//     new Vue({
	//         el:"#userCheckBox",
	//         data:{
	//             checkList:dataList
	//         },
	//         components:{
	//             'headCheckBox':HeadCheckBox
	//         },
	//         methods:{
	//             getHeadInfo:function (doCallBack) {
	//                 getHeadInfo(doCallBack);
	//             }
	//         },
	//     });
	// })

	/**
	 * Created by hantong on 16/11/1.
	 */
	(0, _jquery2.default)(document).ready(function () {

	    new _Vue2.default({
	        el: '#headCheckBody',
	        data: {
	            currentView: 'check',
	            viewList: ['check', 'pass', 'unpass']
	        },
	        methods: {
	            changeView: function changeView(index) {
	                this.currentView = this.viewList[index];
	            }
	        },
	        components: {
	            check: {
	                template: '<div class="tab-pane user-head-list active" id="head-wait"> <div id="userCheckBox"> <head-check-box ></head-check-box> </div>', //:check-list="checkList"
	                components: {
	                    headCheckBox: _headCheckBox2.default
	                }
	            },
	            pass: {
	                template: '<div class="tab-pane user-head-list" id="head-pass"> <head-table-show :element-id="\'head-pass\'" :status="1"></head-table-show> </div>',
	                components: {
	                    'headTableShow': _headCheckTable2.default
	                }
	            },
	            unpass: {
	                template: '<div class="tab-pane user-head-list" id="head-pass"> <head-table-show :element-id="\'head-unpass\'" :status="0"></head-table-show> </div>',
	                components: {
	                    'headTableShow': _headCheckTable2.default
	                }
	            }
	        }
	    });

	    // new Vue({
	    //     el:'#head-pass',
	    //     components:{
	    //         'headTableShow':headCheckTable
	    //     }
	    // });
	    // new Vue({
	    //     el:"#head-unpass",
	    //     components:{
	    //         'headTableShow':headCheckTable
	    //     }
	    // });
	});

/***/ },
/* 1 */
/***/ function(module, exports) {

	module.exports = Vue;

/***/ },
/* 2 */
/***/ function(module, exports) {

	module.exports = window.$;

/***/ },
/* 3 */,
/* 4 */,
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
/* 11 */,
/* 12 */,
/* 13 */,
/* 14 */,
/* 15 */,
/* 16 */,
/* 17 */,
/* 18 */,
/* 19 */,
/* 20 */,
/* 21 */,
/* 22 */,
/* 23 */,
/* 24 */,
/* 25 */,
/* 26 */,
/* 27 */,
/* 28 */,
/* 29 */,
/* 30 */,
/* 31 */,
/* 32 */,
/* 33 */,
/* 34 */,
/* 35 */,
/* 36 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_script__ = __webpack_require__(37)
	__vue_template__ = __webpack_require__(41)
	module.exports = __vue_script__ || {}
	if (module.exports.__esModule) module.exports = module.exports.default
	if (__vue_template__) { (typeof module.exports === "function" ? module.exports.options : module.exports).template = __vue_template__ }
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), true)
	  if (!hotAPI.compatible) return
	  var id = "/Users/hantong/Sites/website/mobileGame/huanpeng/htdocs/admin2/vue/UserManage/userHead/headCheckBox.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 37 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _headBox = __webpack_require__(38);

	var _headBox2 = _interopRequireDefault(_headBox);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	exports.default = {
	    data: function data() {
	        return {
	            checkList: []
	        };
	    },
	    ready: function ready() {
	        var self = this;
	        this.getUserInfo(function (arr) {
	            for (var i in arr) {
	                self.checkList.push(arr[i]);
	            }
	        });
	    },
	    methods: {
	        exchange: function exchange() {
	            console.log(this.checkList);
	            var list = this.checkList;
	            for (var index in list) {
	                var stat = this.checkList[index].stat;
	                var imgUrl = this.checkList[index].imgUrl;
	                var uid = this.checkList[index].uid;
	                this.checkList.$set(index, { uid: uid, stat: !stat, imgUrl: imgUrl });
	            }
	        },
	        submit: function submit() {
	            var succList = [];
	            var failedList = [];
	            for (var i in this.checkList) {
	                if (this.checkList[i].stat) {
	                    succList.push(this.checkList[i].uid);
	                } else {
	                    failedList.push(this.checkList[i].uid);
	                }
	            }
	            console.log(succList);
	            console.log(failedList);
	            var self = this;
	            var requestData = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie('admin_enc'),
	                succuid: succList.join(),
	                failuid: failedList.join()
	            };
	            //set pass and request new list
	            ajaxRequest({ url: $conf.user.api + 'setPicPass.php', data: requestData }, function () {
	                self.checkList.splice(0, self.checkList.length); //clear all
	                console.log(self.checkList);
	                self.getHeadInfo(function (d) {
	                    for (var i in d) {
	                        self.checkList.push(d[i]);
	                    }
	                });
	            });
	        },
	        getUserInfo: function getUserInfo(doCallBack) {
	            this.checkList.splice(0, this.checkList.length);

	            var url = $conf.user.api + 'getWaitPassList.php';
	            var data = { uid: getCookie('admin_uid'), encpass: getCookie('admin_enc') };
	            ajaxRequest({ url: url, data: data }, function (d) {
	                var list = d.data;
	                var arr = [];
	                for (var i in list) {
	                    var data = list[i];
	                    arr.push({
	                        uid: data.uid,
	                        stat: 1,
	                        imgUrl: data.pic
	                    });
	                }
	                typeof doCallBack == 'function' && doCallBack(arr);
	            });
	        }
	    },
	    components: {
	        'userheadcheckbox': _headBox2.default
	    }
	};
	// </script>
	// <template>
	//     <div class="head-succ-list col-md-5-5">
	//         <userheadcheckbox :types="1" :title="'通过'" :check-list="checkList"></userheadcheckbox>
	//     </div>
	//     <div class="head-check-exchange col-md-1">
	//         <div class="option-group"><button type="button" class="btn btn-circle" @click="exchange"></button></div>
	//         <div class="option-group"><button type="button" class="btn btn-circle" @click="exchange"></button></div>
	//     </div>
	//     <div class="head-failed-list col-md-5-5">
	//         <userheadcheckbox :types="0" :title="'驳回'" :check-list="checkList"></userheadcheckbox>
	//     </div>
	//     <div class="head-check-submit col-md-12">
	//         <button type="submit" @click="submit" class="btn  submit">提交审核</button>
	//     </div>
	// </template>
	//
	//
	// <script>

/***/ },
/* 38 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_script__ = __webpack_require__(39)
	__vue_template__ = __webpack_require__(40)
	module.exports = __vue_script__ || {}
	if (module.exports.__esModule) module.exports = module.exports.default
	if (__vue_template__) { (typeof module.exports === "function" ? module.exports.options : module.exports).template = __vue_template__ }
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), true)
	  if (!hotAPI.compatible) return
	  var id = "/Users/hantong/Sites/website/mobileGame/huanpeng/htdocs/admin2/vue/UserManage/userHead/headBox.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 39 */
/***/ function(module, exports) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
		value: true
	});
	//
	// <template>
	// 	<div class="head-check-box">
	// 		<div class="head-box-head">
	// 			<i></i>
	// 			<span>{{title}}</span>
	// 		</div>
	// 		<div class="head-box-body">
	// 			<div class="user-head-table">
	// 				<div class="user-head-one col-md-4" v-for="(index,item) in checkList" v-if="item" v-show="types==1?item.stat:!item.stat"  @click="checkHead(index)">
	// 					<img alt="" class="user-head" :src="item.imgUrl">
	// 				</div>
	// 			</div>
	// 		</div>
	// 	</div>
	// </template>
	//
	// <script>
	exports.default = {
		props: {
			types: {
				type: Number,
				default: 1
			},
			title: {
				type: String,
				default: '通过'
			},
			checkList: {
				type: [Array, Object],
				twoWay: true,
				default: function _default() {
					return {};
				}
			}
		},
		methods: {
			checkHead: function checkHead(index) {
				if (this.checkList[index]) {
					var stat = this.checkList[index].stat;
					var imgUrl = this.checkList[index].imgUrl;
					var uid = this.checkList[index].uid;
					this.checkList.$set(index, { uid: uid, stat: !stat, imgUrl: imgUrl });
				}
			}
		}
	};
	// </script>

/***/ },
/* 40 */
/***/ function(module, exports) {

	module.exports = "\n\t<div class=\"head-check-box\">\n\t\t<div class=\"head-box-head\">\n\t\t\t<i></i>\n\t\t\t<span>{{title}}</span>\n\t\t</div>\n\t\t<div class=\"head-box-body\">\n\t\t\t<div class=\"user-head-table\">\n\t\t\t\t<div class=\"user-head-one col-md-4\" v-for=\"(index,item) in checkList\" v-if=\"item\" v-show=\"types==1?item.stat:!item.stat\"  @click=\"checkHead(index)\">\n\t\t\t\t\t<img alt=\"\" class=\"user-head\" :src=\"item.imgUrl\">\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t</div>\n\t</div>\n";

/***/ },
/* 41 */
/***/ function(module, exports) {

	module.exports = "\n    <div class=\"head-succ-list col-md-5-5\">\n        <userheadcheckbox :types=\"1\" :title=\"'通过'\" :check-list=\"checkList\"></userheadcheckbox>\n    </div>\n    <div class=\"head-check-exchange col-md-1\">\n        <div class=\"option-group\"><button type=\"button\" class=\"btn btn-circle\" @click=\"exchange\"></button></div>\n        <div class=\"option-group\"><button type=\"button\" class=\"btn btn-circle\" @click=\"exchange\"></button></div>\n    </div>\n    <div class=\"head-failed-list col-md-5-5\">\n        <userheadcheckbox :types=\"0\" :title=\"'驳回'\" :check-list=\"checkList\"></userheadcheckbox>\n    </div>\n    <div class=\"head-check-submit col-md-12\">\n        <button type=\"submit\" @click=\"submit\" class=\"btn  submit\">提交审核</button>\n    </div>\n";

/***/ },
/* 42 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_script__ = __webpack_require__(43)
	__vue_template__ = __webpack_require__(44)
	module.exports = __vue_script__ || {}
	if (module.exports.__esModule) module.exports = module.exports.default
	if (__vue_template__) { (typeof module.exports === "function" ? module.exports.options : module.exports).template = __vue_template__ }
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), true)
	  if (!hotAPI.compatible) return
	  var id = "/Users/hantong/Sites/website/mobileGame/huanpeng/htdocs/admin2/vue/UserManage/userHead/headCheckTable.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 43 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _crossPage = __webpack_require__(5);

	var _crossPage2 = _interopRequireDefault(_crossPage);

	var _inputCheckBox = __webpack_require__(8);

	var _inputCheckBox2 = _interopRequireDefault(_inputCheckBox);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	// <template>
	//     <table class="table table-striped table-bordered table-hover">
	//         <thead>
	//         <tr>
	//             <th class="checkbox-th"></th>
	//             <th>头像</th>
	//             <th>昵称</th>
	//             <th>UID</th>
	//             <th>提交时间</th>
	//             <th>状态</th>
	//         </tr>
	//         </thead>
	//         <tbody>
	//         <tr v-for="(index,item) in dataList" @click="mark(index)">
	//             <td><checkbox :is-checked="item.checked"></checkbox></td>
	//             <td><img alt="" :src="item.imgUrl"></td>
	//             <td>{{item.nick}}</td>
	//             <td>{{item.uid}}</td>
	//             <td>{{item.ctime}}</td>
	//             <td><span class="icon" :class="item.pass? pass : unpass"></span>{{item.pass ? '通过' : '未通过'}}</td>
	//         </tr>
	//         </tbody>
	//     </table>
	//     <nav class="handle-option">
	//         <div class="col-md-3">
	//             <button class="btn btn-circle" @click="allSelect">全选</button>
	//             <button class="btn btn-circle" @click="inverseSelect">反选</button>
	//         </div>
	//         <div class="col-md-6" style="text-align: center">
	//             <page-cross :element-id="elementId" :page-size="pageSize" :total-counts="totalCounts" :current-page="currentPage"></page-cross>
	//         </div>
	//         <div class="col-md-3">
	//             <button class="recheck btn btn-circle pull-right" @click="reCheck">重新审核</button>
	//         </div>
	//     </nav>
	// </template>
	//
	// <script>
	exports.default = {
	    props: {
	        elementId: {
	            type: String
	        },
	        pageSize: {
	            type: Number
	        },
	        status: { //0:wait, 1:pass, 2:unpass
	            type: Number,
	            default: 1
	        },
	        sTime: {
	            type: String
	        },
	        eTime: {
	            type: String
	        }
	    },
	    data: function data() {
	        return {
	            dataList: [],
	            totalCounts: 1,
	            currentPage: 1,
	            params: ['checked', 'uid', 'imgUrl', 'nick', 'status']
	        };
	    },
	    ready: function ready() {
	        this.requestList(1);
	    },
	    events: {
	        pageChange: function pageChange(num, doCallBack) {
	            console.log('emit run here:' + num);
	            this.requestList(num, doCallBack);
	        }
	    },
	    methods: {
	        requestList: function requestList(page) {
	            var self = this;
	            var doCallBack = typeof arguments[1] == 'function' ? arguments[1] : function () {};
	            var url = $conf.user.api + 'getWaitPassList.php';
	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie("admin_enc"),
	                type: 1,
	                size: this.pageSize,
	                page: page || 1,
	                status: this.status,
	                stime: this.sTime || '',
	                etime: this.eTime || ''
	            };

	            ajaxRequest({ url: url, data: data }, function (d) {
	                self.dataList.splice(0, self.dataList.length);
	                self.totalCount = d.total;
	                var list = d.data;
	                var statusList = [0, 1, 0];
	                for (var i in list) {
	                    var data = {
	                        checked: false,
	                        imgUrl: list[i].pic,
	                        uid: list[i].uid,
	                        nick: list[i].nick,
	                        ctime: list[i].ctime,
	                        pass: statusList[list[i].status]
	                    };
	                    self.dataList.push(data);
	                }
	                self.totalCounts = parseInt(d.total);
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
	        reCheck: function reCheck() {
	            var list = [];
	            for (var i in this.dataList) {
	                if (this.dataList[i].checked) list.push(this.dataList[i].uid);
	            }
	            if (!list) return;
	            this.reCheckRequest(list);
	        },
	        reCheckRequest: function reCheckRequest(arr) {
	            var self = this;
	            var url = $conf.user.api + 'restartCheck.php';
	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie('admin_enc'),
                    type : getCookie('admin_type'),
	                succuid: arr.join()
	            };
	            ajaxRequest({ url: url, data: data }, function () {
	                self.requestList();
	            });
	        }
	    },
	    components: {
	        'pageCross': _crossPage2.default,
	        'checkbox': _inputCheckBox2.default
	    }

	};
	// </script>

/***/ },
/* 44 */
/***/ function(module, exports) {

	module.exports = "\n    <table class=\"table table-striped table-bordered table-hover\">\n        <thead>\n        <tr>\n            <th class=\"checkbox-th\"></th>\n            <th>头像</th>\n            <th>昵称</th>\n            <th>UID</th>\n            <th>提交时间</th>\n            <th>状态</th>\n        </tr>\n        </thead>\n        <tbody>\n        <tr v-for=\"(index,item) in dataList\" @click=\"mark(index)\">\n            <td><checkbox :is-checked=\"item.checked\"></checkbox></td>\n            <td><img alt=\"\" :src=\"item.imgUrl\"></td>\n            <td>{{item.nick}}</td>\n            <td>{{item.uid}}</td>\n            <td>{{item.ctime}}</td>\n            <td><span class=\"icon\" :class=\"item.pass? pass : unpass\"></span>{{item.pass ? '通过' : '未通过'}}</td>\n        </tr>\n        </tbody>\n    </table>\n    <nav class=\"handle-option\">\n        <div class=\"col-md-3\">\n            <button class=\"btn btn-circle\" @click=\"allSelect\">全选</button>\n            <button class=\"btn btn-circle\" @click=\"inverseSelect\">反选</button>\n        </div>\n        <div class=\"col-md-6\" style=\"text-align: center\">\n            <page-cross :element-id=\"elementId\" :page-size=\"pageSize\" :total-counts=\"totalCounts\" :current-page=\"currentPage\"></page-cross>\n        </div>\n        <div class=\"col-md-3\">\n            <button class=\"recheck btn btn-circle pull-right\" @click=\"reCheck\">重新审核</button>\n        </div>\n    </nav>\n";

/***/ }
/******/ ]);