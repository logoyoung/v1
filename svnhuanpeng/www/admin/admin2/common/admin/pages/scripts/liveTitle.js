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

	var _liveCheckTable = __webpack_require__(15);

	var _liveCheckTable2 = _interopRequireDefault(_liveCheckTable);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	(0, _jquery2.default)(document).ready(function () {
		new _Vue2.default({
			el: "#titleCheckBody",
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
					template: '<div class="tab-pane user-head-list active" id="wait"><title-check-table :element-id="\'title-check\'" :checked-page="true" :status="0"></title-check-table> </div>', //:check-list="checkList"
					components: {
						titleCheckTable: _liveCheckTable2.default
					}
				},
				pass: {
					template: '<div class="tab-pane user-head-list" id="pass"> <title-check-table :element-id="\'title-pass\'" :checked-page="false" :status="1"></title-check-table> </div>',
					components: {
						titleCheckTable: _liveCheckTable2.default
					}
				},
				unpass: {
					template: '<div class="tab-pane user-head-list" id="unpass"> <title-check-table :element-id="\'title-unpass\'" :checked-page="false" :status="2"></title-check-table> </div>',
					components: {
						titleCheckTable: _liveCheckTable2.default
					}
				}
			}
		});
	}); /**
	     * Created by hantong on 16/11/14.
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
/* 15 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_script__ = __webpack_require__(16)
	__vue_template__ = __webpack_require__(17)
	module.exports = __vue_script__ || {}
	if (module.exports.__esModule) module.exports = module.exports.default
	if (__vue_template__) { (typeof module.exports === "function" ? module.exports.options : module.exports).template = __vue_template__ }
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), true)
	  if (!hotAPI.compatible) return
	  var id = "/Users/hantong/Sites/website/mobileGame/huanpeng/htdocs/admin2/vue/UserManage/liveTitle/liveCheckTable.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 16 */
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
	//             <th>直播标题</th>
	//             <th>昵称</th>
	//             <th>UID</th>
	//             <th>提交时间</th>
	//             <th>状态</th>
	//         </tr>
	//         </thead>
	//         <tbody>
	//         <tr v-for="(index ,item) in dataList" @click="mark(index)">
	//             <td><checkbox :is-checked="item.checked"></checkbox></td>
	//             <td>{{item.liveTitle}}</td>
	//             <td>{{item.nick}}</td>
	//             <td>{{item.uid}}</td>
	//             <td>{{item.ctime}}</td>
	//             <td><span class="icon" :class="checkStatus(item.status)"></span>{{checkText(item.status)}}</td>
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
	//         <div class="col-md-3" style="text-align: right" v-if="checkedPage">
	//             <button class="btn unpassed" @click="unpassed">驳回</button>
	//             <button class="btn passed" @click="passed">通过</button>
	//         </div>
	//         <div class="col-md-3" style="text-align: right" v-if="!checkedPage">
	//             <button class="btn recheck" @click="reCheck">重新审核</button>
	//         </div>
	//     </nav>
	// </template>
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
	        sTime: {
	            type: String
	        },
	        eTime: {
	            type: String
	        },
	        checkedPage: {
	            type: Boolean,
	            default: false
	        },
	        status: {
	            type: Number,
	            default: 0
	        }
	    },
	    data: function data() {
	        return {
	            dataList: [],
	            totalCounts: 1,
	            currentPage: 1,
	            params: ['checked', 'liveTitle', 'nick', 'uid', 'ctime', 'status', 'liveid'],
	            url: $conf.live.api + 'getLiveTitle.php',
	            passUrl: $conf.live.api + 'checkLiveTitle.php',
	            unpassUrl: $conf.live.api + 'checkLiveTitle.php',
	            reCheckUrl: $conf.live.api + ''
	        };
	    },

	    ready: function ready() {
	        this.requestList(1);
	    },
	    events: {
	        pageChange: function pageChange(num, doCallBack) {
	            this.requestList(num, doCallBack);
	        }
	    },
	    methods: {
	        requestList: function requestList(page) {
	            var self = this;
	            var doCallBack = typeof arguments[1] == 'function' ? arguments[1] : function () {};
	            var url = this.url;
	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie('admin_enc'),
	                type: 1,
	                page: page,
	                size: this.pageSize,
	                status: this.status,
	                stime: this.stime || '',
	                etime: this.etime || ''
	            };

	            ajaxRequest({ url: url, data: data }, function (d) {
	                self.dataList.splice(0, self.dataList.length);
	                self.totalCount = d.total;
	                var list = d.data;
	                for (var i in list) {
	                    var data = {
	                        checked: false,
	                        liveTitle: list[i].title,
	                        nick: list[i].nick,
	                        uid: list[i].uid,
	                        ctime: list[i].ctime,
	                        status: list[i].status,
	                        liveid: list[i].liveid
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
	            var uids = this.getCheckedliveIds();
	        },
	        passed: function passed() {
	            var self = this;
	            var liveids = this.getCheckedliveIds();
	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie('admin_enc'),
	                type: getCookie('admin_type'),
	                succList: liveids['select'].join(),
	                failedList: liveids['unselect'].join()
	            };
	            ajaxRequest({ url: this.passUrl, data: data }, function (d) {
	                self.requestList();
	            }, function (d) {
	                alert(d.desc);
	            });
	        },
	        unpassed: function unpassed() {
	            var self = this;
	            var liveids = this.getCheckedliveIds();
	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie('admin_enc'),
	                type: getCookie('admin_type'),
	                succList: liveids['unselect'].join(),
	                failedList: liveids['select'].join()
	            };
	            ajaxRequest({ url: this.unpassUrl, data: data }, function (d) {
	                self.requestList();
	            }, function (d) {
	                alert(d.desc);
	            });
	        },
	        getCheckedliveIds: function getCheckedliveIds() {
	            var unselect = [];
	            var select = [];
	            for (var i in this.dataList) {
	                if (this.dataList[i].checked) {
	                    if (this.dataList[i].liveid) select.push(this.dataList[i].liveid);
	                } else {
	                    if (this.dataList[i].liveid) unselect.push(this.dataList[i].liveid);
	                }
	            }
	            return { select: select, unselect: unselect };
	        },
	        clearList: function clearList() {
	            this.dataList.splice(0, this.dataList.length);
	        },
	        checkStatus: function checkStatus(status) {
	            return ['wait', 'pass', 'unpsas'][status];
	        },
	        checkText: function checkText(status) {
	            return ['审核中', '已通过', '未通过'][status];
	        }
	    },
	    components: {
	        checkbox: _inputCheckBox2.default,
	        pageCross: _crossPage2.default
	    }
	};
	// </script>

/***/ },
/* 17 */
/***/ function(module, exports) {

	module.exports = "\n    <table class=\"table table-striped table-bordered table-hover\">\n        <thead>\n        <tr>\n            <th class=\"checkbox-th\"></th>\n            <th>直播标题</th>\n            <th>昵称</th>\n            <th>UID</th>\n            <th>提交时间</th>\n            <th>状态</th>\n        </tr>\n        </thead>\n        <tbody>\n        <tr v-for=\"(index ,item) in dataList\" @click=\"mark(index)\">\n            <td><checkbox :is-checked=\"item.checked\"></checkbox></td>\n            <td>{{item.liveTitle}}</td>\n            <td>{{item.nick}}</td>\n            <td>{{item.uid}}</td>\n            <td>{{item.ctime}}</td>\n            <td><span class=\"icon\" :class=\"checkStatus(item.status)\"></span>{{checkText(item.status)}}</td>\n        </tr>\n        </tbody>\n    </table>\n    <nav class=\"handle-option\">\n        <div class=\"col-md-3\">\n            <button class=\"btn btn-circle\" @click=\"allSelect\">全选</button>\n            <button class=\"btn btn-circle\" @click=\"inverseSelect\">反选</button>\n        </div>\n        <div class=\"col-md-6\" style=\"text-align: center\">\n            <page-cross :element-id=\"elementId\" :page-size=\"pageSize\" :total-counts=\"totalCounts\" :current-page=\"currentPage\"></page-cross>\n        </div>\n        <div class=\"col-md-3\" style=\"text-align: right\" v-if=\"checkedPage\">\n            <button class=\"btn unpassed\" @click=\"unpassed\">驳回</button>\n            <button class=\"btn passed\" @click=\"passed\">通过</button>\n        </div>\n        <div class=\"col-md-3\" style=\"text-align: right\" v-if=\"!checkedPage\">\n            <button class=\"btn recheck\" @click=\"reCheck\">重新审核</button>\n        </div>\n    </nav>\n";

/***/ }
/******/ ]);