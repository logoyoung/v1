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

	var _news = __webpack_require__(18);

	var _news2 = _interopRequireDefault(_news);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	(0, _jquery2.default)(document).ready(function () {

		new _Vue2.default({
			el: "#unpublishBody",
			data: {
				currentView: 'news',
				viewList: ['news', 'events', 'announcement'],
				typeList: {
					news: 5,
					events: 8,
					announcement: 13
				},
				keyword: '',
				goSearch: false,
				tid: 5
			},
			ready: function ready() {
				console.log('indexs goSearch' + this.goSearch);
			},
			computed: {
				lisKeyWord: function lisKeyWord() {
					return this.keyword;
				}
			},
			methods: {
				changeView: function changeView(index) {
					this.currentView = this.viewList[index];
					this.tid = this.typeList[this.currentView];
					console.log(this.tid);
				},
				create: function create() {
					location.href = $conf.domain + 'view/newsCreate.php?sidebar=8-0&type=' + this.typeList[this.currentView];
				},
				search: function search() {
					console.log('pre do search and set goSearch=true');
					this.goSearch = true;
				}
			},
			components: {
				news: {
					props: {
						keyword: String,
						goSearch: {
							type: Boolean,
							required: true
						},
						tid: {
							type: Number,
							required: true
						},
						elementId: String
					},
					ready: function ready() {
						console.log('news' + this.gosearch);
					},
					computed: {
						listenTid: function listenTid() {
							return this.tid;
						}
					},
					template: '<div class="tab-pane user-head-list active" id="news"><unpublish :go-search.sync="goSearch" :keyword="keyword" :tid="tid" :element-id="elementId"></unpublish></div>',
					components: {
						unpublish: _news2.default
					}
				}
			}
		});
	}); /**
	     * Created by hantong on 16/11/29.
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
/* 15 */,
/* 16 */,
/* 17 */,
/* 18 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_script__ = __webpack_require__(19)
	__vue_template__ = __webpack_require__(23)
	module.exports = __vue_script__ || {}
	if (module.exports.__esModule) module.exports = module.exports.default
	if (__vue_template__) { (typeof module.exports === "function" ? module.exports.options : module.exports).template = __vue_template__ }
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), true)
	  if (!hotAPI.compatible) return
	  var id = "/Users/hantong/Sites/website/mobileGame/huanpeng/htdocs/admin2/vue/newsManage/pub/news.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 19 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _crossPage = __webpack_require__(5);

	var _crossPage2 = _interopRequireDefault(_crossPage);

	var _inputCheckBox = __webpack_require__(8);

	var _inputCheckBox2 = _interopRequireDefault(_inputCheckBox);

	var _dialog = __webpack_require__(20);

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
	        tid: {
	            type: Number,
	            require: true
	        },
	        keyword: {
	            type: String
	        },
	        goSearch: {
	            type: Boolean,
	            required: true
	        }
	    },
	    data: function data() {
	        return {
	            dataList: [],
	            totalCounts: 1,
	            currentPage: 1,
	            params: ['checked', 'title', 'pic', 'nick', 'id', 'ctime'],
	            publishOptionShow: false,
	            newsid: 0,
	            currentTid: 5,
	            currentKeyWord: '' //防止第一次重复加载
	        };
	    },
	    ready: function ready() {
	        this.requestList(1);
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
	        changeView: function changeView() {
	            if (this.currentTid != this.tid) {
	                this.requestList();
	                this.currentTid = this.tid;
	            }
	            return this.tid;
	        },
	        noSearch: function noSearch() {
	            if (this.currentKeyWord != this.keyword && this.keyword == '') {
	                console.log('no search event run');
	                this.requestList();
	            }
	            return this.keyword;
	        }
	    },
	    events: {
	        pageChange: function pageChange(num, doCallBack) {
	            console.log('emit run here:' + num);
	            this.requestList(num, doCallBack);
	        },
	        publish: function publish(newsid, type, callback) {
	            if (!newsid) return false;

	            this.publishRequest(newsid, type, callback);
	        }
	    },
	    methods: {
	        requestList: function requestList(page) {
	            var self = this;
	            var doCallBack = typeof arguments[1] == 'function' ? arguments[1] : function () {};
	            var url = $conf.api + 'information/info/getInformationList.php';

	            //                var keyword = '';
	            this.currentKeyWord = this.keyword;
	            //                if(this.goSearch)
	            //                    keyword = this.keyword || ''


	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie("admin_enc"),
	                type: 1,
	                size: this.pageSize,
	                page: page || self.currentPage || 1,
	                status: 1,
	                tid: this.tid,
	                stime: this.sTime || '',
	                etime: this.eTime || '',
	                keyword: this.keyword
	            };

	            ajaxRequest({ url: url, data: data }, function (d) {
	                self.dataList.splice(0, self.dataList.length);
	                self.totalCount = d.total;
	                var list = d.list;
	                for (var i in list) {
	                    var data = {
	                        checked: false,
	                        title: list[i].title,
	                        pic: list[i].poster,
	                        nick: list[i].nick,
	                        id: list[i].id,
	                        ctime: list[i].ctime
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
	        deleteItem: function deleteItem() {
	            var list = [];
	            for (var i in this.dataList) {
	                if (this.dataList[i].checked) list.push(this.dataList[i].id);
	            }
	            if (!list) return;
	            this.deleteItemkRequest(list);
	        },
	        deleteItemkRequest: function deleteItemkRequest(arr, callback) {
	            var self = this;
	            var url = $conf.api + 'information/info/changeInformation.php';
	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie('admin_enc'),
	                type: getCookie('admin_type'),
	                status: 2,
	                id: arr.join()

	            };
	            ajaxRequest({ url: url, data: data }, function () {
	                self.requestList();
	            });
	        },
	        prePublish: function prePublish(index) {
	            console.log('pre publish do ');
	            console.log(this.publishOptionShow);
	            this.publishOptionShow = true;
	            this.newsid = Number(this.dataList[index].id);
	        },
	        publishRequest: function publishRequest(newsid, type, callback) {
	            var self = this;
	            var url = $conf.api + 'information/info/changeInformation.php';
	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie('admin_enc'),
	                type: getCookie('admin_type'),
	                id: newsid,
	                status: 1,
	                isRecommend: Number(type)
	            };
	            ajaxRequest({ url: url, data: data }, function () {
	                self.requestList();
	                callback && typeof callback == 'function' && callback();
	            }, function (d) {
	                alert(d.desc);
	            });
	        },
	        turnTo: function turnTo(index) {
	            location.href = $conf.domain + 'view/newsCreate.php?sidebar=8-0&nid=' + this.dataList[index].id;
	        }
	    },
	    components: {
	        'pageCross': _crossPage2.default,
	        'checkbox': _inputCheckBox2.default,
	        publishBox: _dialog2.default
	    }

	};
	// </script>
	// <template>
	//     <table class="table table-striped table-bordered table-hover">
	//         <thead>
	//         <tr>
	//             <th class="checkbox-th"></th>
	//             <th>封面</th>
	//             <th>标题</th>
	//             <th>小编</th>
	//             <th>发布时间</th>
	//             <th>管理</th>
	//             <th>编辑</th>
	//         </tr>
	//         </thead>
	//         <tbody>
	//         <tr v-for="(index,item) in dataList" @click="mark(index)">
	//             <td><checkbox :is-checked="item.checked"></checkbox></td>
	//             <td v-if="item.pic"><img :src="item.pic"/></td>
	//             <td v-else>无</td>
	//             <td>{{item.title}}</td>
	//             <td>{{item.nick}}</td>
	//             <td>{{item.ctime}}</td>
	//             <td><button class="btn" @click.stop="prePublish(index)">推荐首页</button></td>
	//             <td><a class="btn" @click.stop="turnTo(index)">编辑</a></td>
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
	//             <button class="recheck btn btn-circle pull-right" @click="deleteItem">删除</button>
	//         </div>
	//     </nav>
	//     <div v-show="false">{{todoSearch + changeView + noSearch}}</div>
	//     <publish-box :newsid.sync="newsid" :publish-option-show.sync="publishOptionShow"></publish-box>
	// </template>
	//
	// <script>

/***/ },
/* 20 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_script__ = __webpack_require__(21)
	__vue_template__ = __webpack_require__(22)
	module.exports = __vue_script__ || {}
	if (module.exports.__esModule) module.exports = module.exports.default
	if (__vue_template__) { (typeof module.exports === "function" ? module.exports.options : module.exports).template = __vue_template__ }
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), true)
	  if (!hotAPI.compatible) return
	  var id = "/Users/hantong/Sites/website/mobileGame/huanpeng/htdocs/admin2/vue/newsManage/pub/dialog.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 21 */
/***/ function(module, exports) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});
	// <template>
	//     <div v-show="publishOptionShow">
	//         <div class="modal-backdrop fade in" style="z-index:10050"></div>
	//         <div  class="modal fade" :class="publishOptionShow ? 'in' : ''" :style="publishOptionShow ? 'display:block;':''" role="dialog" aria-hidden="true">
	//             <div class="modal-dialog">
	//                 <div class="modal-content">
	//                     <div class="modal-header">
	//                         <button type="button" class="close" data-dismiss="" aria-hidden="true" @click="close"></button>
	//                         <h4 class="modal-title">发布</h4>
	//                     </div>
	//                     <div class="modal-body form">
	//                         <form action="#" class="form-horizontal form-row-seperated" style="padding: 20px 0px 20px 160px;">
	//                             <div class="row">
	//                                 <input type="radio" id="to-index-point" value="1" v-model="selectType">
	//                                 <label for="to-index-point">推荐到首页焦点（需要有封面）</label>
	//                             </div>
	//                             <div class="row">
	//                                 <input type="radio" id="to-index" value="2" v-model="selectType">
	//                                 <label for="to-index">推荐到首页列表</label>
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
	        newsid: {
	            type: Number,
	            required: true
	        },
	        publishOptionShow: {
	            type: Boolean,
	            required: true
	        }
	    },
	    data: function data() {
	        return {
	            selectType: 2
	        };
	    },
	    methods: {
	        publish: function publish() {
	            var self = this;
	            this.$dispatch('publish', self.newsid, self.selectType, function (d) {
	                self.close();
	            });
	        },
	        close: function close() {
	            this.publishOptionShow = false;
	            this.selectType = 2;
	            //                this.newsid=0;
	        }
	    }

	};
	// </script>

/***/ },
/* 22 */
/***/ function(module, exports) {

	module.exports = "\n    <div v-show=\"publishOptionShow\">\n        <div class=\"modal-backdrop fade in\" style=\"z-index:10050\"></div>\n        <div  class=\"modal fade\" :class=\"publishOptionShow ? 'in' : ''\" :style=\"publishOptionShow ? 'display:block;':''\" role=\"dialog\" aria-hidden=\"true\">\n            <div class=\"modal-dialog\">\n                <div class=\"modal-content\">\n                    <div class=\"modal-header\">\n                        <button type=\"button\" class=\"close\" data-dismiss=\"\" aria-hidden=\"true\" @click=\"close\"></button>\n                        <h4 class=\"modal-title\">发布</h4>\n                    </div>\n                    <div class=\"modal-body form\">\n                        <form action=\"#\" class=\"form-horizontal form-row-seperated\" style=\"padding: 20px 0px 20px 160px;\">\n                            <div class=\"row\">\n                                <input type=\"radio\" id=\"to-index-point\" value=\"1\" v-model=\"selectType\">\n                                <label for=\"to-index-point\">推荐到首页焦点（需要有封面）</label>\n                            </div>\n                            <div class=\"row\">\n                                <input type=\"radio\" id=\"to-index\" value=\"2\" v-model=\"selectType\">\n                                <label for=\"to-index\">推荐到首页列表</label>\n                            </div>\n                        </form>\n                    </div>\n                    <div class=\"modal-footer\">\n                        <button id=\"modal-submit\"  type=\"button\" class=\"btn btn-primary yellow-crusta\" @click=\"publish\">确定</button>\n                        <button id=\"modal-close\" type=\"button\" class=\"btn btn-default\" data-dismiss=\"\" @click=\"close\">取消</button>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n";

/***/ },
/* 23 */
/***/ function(module, exports) {

	module.exports = "\n    <table class=\"table table-striped table-bordered table-hover\">\n        <thead>\n        <tr>\n            <th class=\"checkbox-th\"></th>\n            <th>封面</th>\n            <th>标题</th>\n            <th>小编</th>\n            <th>发布时间</th>\n            <th>管理</th>\n            <th>编辑</th>\n        </tr>\n        </thead>\n        <tbody>\n        <tr v-for=\"(index,item) in dataList\" @click=\"mark(index)\">\n            <td><checkbox :is-checked=\"item.checked\"></checkbox></td>\n            <td v-if=\"item.pic\"><img :src=\"item.pic\"/></td>\n            <td v-else>无</td>\n            <td>{{item.title}}</td>\n            <td>{{item.nick}}</td>\n            <td>{{item.ctime}}</td>\n            <td><button class=\"btn\" @click.stop=\"prePublish(index)\">推荐首页</button></td>\n            <td><a class=\"btn\" @click.stop=\"turnTo(index)\">编辑</a></td>\n        </tr>\n        </tbody>\n    </table>\n    <nav class=\"handle-option\">\n        <div class=\"col-md-3\">\n            <button class=\"btn btn-circle\" @click=\"allSelect\">全选</button>\n            <button class=\"btn btn-circle\" @click=\"inverseSelect\">反选</button>\n        </div>\n        <div class=\"col-md-6\" style=\"text-align: center\">\n            <page-cross :element-id=\"elementId\" :page-size=\"pageSize\" :total-counts=\"totalCounts\" :current-page=\"currentPage\"></page-cross>\n        </div>\n        <div class=\"col-md-3\">\n            <button class=\"recheck btn btn-circle pull-right\" @click=\"deleteItem\">删除</button>\n        </div>\n    </nav>\n    <div v-show=\"false\">{{todoSearch + changeView + noSearch}}</div>\n    <publish-box :newsid.sync=\"newsid\" :publish-option-show.sync=\"publishOptionShow\"></publish-box>\n";

/***/ }
/******/ ]);