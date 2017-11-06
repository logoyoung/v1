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

	var _table = __webpack_require__(24);

	var _table2 = _interopRequireDefault(_table);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	(0, _jquery2.default)(document).ready(function () {
		new _Vue2.default({
			el: '#newsRecommend',
			data: {
				viewPage: 'focus',
				isShow: false,
				viewPageList: ['focus', 'news'],
				dialogOptType: 'create'
			},
			methods: {
				create: function create() {
					this.isShow = true;
					this.dialogOptType = 'create';
				},
				changeView: function changeView(index) {
					this.viewPage = this.viewPageList[index];
				}
			},
			components: {
				recommend: {
					props: {
						isShow: Boolean,
						viewPage: {
							type: String,
							required: true
						},
						dialogOptType: {
							type: String
						}
					},
					template: '<div class="tab-pane user-head-list active" id="newRecommendContent"> <recommend-table  :is-show.sync="isShow" :view-page="viewPage" :dialog-opt-type.sync="dialogOptType"></recommend-table> </div>',
					components: {
						recommendTable: _table2.default
					}
				}
			}
		});
	}); /**
	     * Created by hantong on 16/12/2.
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
/* 5 */,
/* 6 */,
/* 7 */,
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
/* 24 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_script__ = __webpack_require__(25)
	__vue_template__ = __webpack_require__(29)
	module.exports = __vue_script__ || {}
	if (module.exports.__esModule) module.exports = module.exports.default
	if (__vue_template__) { (typeof module.exports === "function" ? module.exports.options : module.exports).template = __vue_template__ }
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), true)
	  if (!hotAPI.compatible) return
	  var id = "/Users/hantong/Sites/website/mobileGame/huanpeng/htdocs/admin2/vue/newsManage/recommend/table.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 25 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _inputCheckBox = __webpack_require__(8);

	var _inputCheckBox2 = _interopRequireDefault(_inputCheckBox);

	var _dialog = __webpack_require__(26);

	var _dialog2 = _interopRequireDefault(_dialog);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	// <template>
	//     <table class="table table-striped table-bordered table-hover">
	//         <thead>
	//         <tr>
	//             <th class="checkbox-th"></th>
	//             <th>推荐</th>
	//             <th v-if="viewPage =='focus'">图片</th>
	//             <th v-else>图片</th>
	//             <th>标题</th>
	//             <th>链接</th>
	//             <th>推荐</th>
	//             <th>编辑</th>
	//         </tr>
	//         </thead>
	//         <tbody>
	//         <tr v-for="(index,item) in dataList" @click="mark(index)">
	//             <td><checkbox :is-checked="item.checked"></checkbox></td>
	//             <td v-if="index==0"><button class="btn" @click.stop="cancelTop(index)">取消置顶</button></td>
	//             <td v-else><button class="btn" @click.stop="toTop(index)">置顶</button></td>
	//             <td v-if="viewPage =='focus'"><img :src="item.poster"/></td>
	//             <td v-else>{{item.tname}}</td>
	//             <td>{{item.title}}</td>
	//             <td>{{item.link}}</td>
	//             <td><button class="btn" @click.stop="cancelPublish(index)">取消推荐</button></td>
	//             <td><button class="btn" @click.stop="modifyItem(index)">编辑</button></td>
	//         </tr>
	//         </tbody>
	//     </table>
	//     <nav class="handle-option">
	//         <div class="col-md-3">
	//             <button class="btn btn-circle" @click="allSelect">全选</button>
	//             <button class="btn btn-circle" @click="inverseSelect">反选</button>
	//         </div>
	//         <div class="col-md-6" style="text-align: center"></div>
	//         <div class="col-md-3">
	//             <button class="recheck btn btn-circle pull-right" @click="deleteItem">删除</button>
	//         </div>
	//     </nav>
	//     <div v-show="false">{{todoSearch }}</div>
	//     <div v-show="false">{{ changeView }}</div>
	//     <div v-show="false">{{ noSearch}}</div>
	//     <add-dialog :is-show.sync="isShow" :type.sync="dialogOptType" :page-view="viewPage" :title="dialogTitle" :link="dialogLink" :newsid="dialogNewid" :poster-url="dialogPosterURL"></add-dialog>
	// </template>
	//
	// <script>
	exports.default = {
	    props: {
	        //            sTime:{
	        //                type:String
	        //            },
	        //            eTime:{
	        //                type:String
	        //            },
	        viewPage: {
	            type: String,
	            require: true
	        },
	        //            keyword:{
	        //                type:String
	        //            },
	        //            goSearch:{
	        //                type:Boolean,
	        //                required:true
	        //            },
	        isShow: {
	            typ: Boolean,
	            required: true
	        },
	        dialogOptType: {
	            type: String,
	            default: 'create'
	        }
	    },
	    data: function data() {
	        return {
	            dataList: [],
	            params: ['checked', 'id', 'tname', 'title', 'poster', 'url', 'type'],
	            currentView: 'focus',
	            dialogTitle: '',
	            dialogLink: '',
	            dialogNewid: 0,
	            dialogPosterURL: ''
	        };
	    },
	    ready: function ready() {
	        this.requestList(1);
	    },
	    methods: {
	        requestList: function requestList(page) {
	            var self = this;
	            var doCallBack = typeof arguments[1] == 'function' ? arguments[1] : function () {};
	            var url = $conf.api + 'information/info/recommendList.php';

	            this.currentKeyWord = this.keyword;
	            this.currentView = this.viewPage;
	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie("admin_enc"),
	                type: 1,
	                id: this.itype
	            };

	            ajaxRequest({ url: url, data: data }, function (d) {
	                self.dataList.splice(0, self.dataList.length);
	                self.totalCount = d.total;
	                var list = d.list;
	                for (var i in list) {
	                    var data = {
	                        checked: false,
	                        id: list[i].id,
	                        tname: list[i].tname,
	                        title: list[i].title,
	                        poster: list[i].poster,
	                        url: list[i].url,
	                        type: list[i].type
	                    };
	                    self.dataList.push(data);
	                }
	                self.totalCounts = parseInt(d.total);
	                doCallBack();
	            });
	        },
	        modifyItem: function modifyItem(index) {
	            if (this.dataList[index].type == 0) {
	                location.href = $conf.domain + 'view/newsCreate.php?sidebar=8-0&nid=' + this.dataList[index].id;
	            } else {
	                this.dialogOptType = 'modify';
	                this.dialogTitle = this.dataList[index].title;
	                this.dialogNewid = Number(this.dataList[index].id);
	                this.dialogLink = this.dataList[index].url;
	                this.dialogPosterURL = this.dataList[index].poster;
	                this.isShow = true;
	            }
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
	            console.log(arr);
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
	        cancelPublish: function cancelPublish(index) {
	            var id = this.dataList[index].id;

	            this.cancelPublishRequest(id);
	        },
	        cancelPublishRequest: function cancelPublishRequest(id) {
	            if (!id) return;
	            var self = this;
	            var url = $conf.api + 'information/info/changeInformation.php';
	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie('admin_enc'),
	                type: getCookie('admin_type'),
	                status: 3,
	                id: id

	            };
	            ajaxRequest({ url: url, data: data }, function () {
	                self.requestList();
	            });
	        },
	        cancelTop: function cancelTop(index) {
	            this.toTop(1);
	        },
	        toTop: function toTop(index) {
	            var self = this;
	            var list = this.getTempList();
	            if (list.length < index + 1) return;

	            var newTop = list[index];
	            var newList = [];
	            var idList = [];

	            delete list[index];

	            newList.push(newTop);
	            for (var i in list) {
	                if (list[i]) newList.push(list[i]);
	            }
	            for (var i in newList) {
	                idList.push(newList[i].id);
	            }

	            var url = $conf.api + 'information/info/upOrCancel.php';
	            var data = {
	                uid: getCookie('admin_uid'),
	                encpass: getCookie('admin_enc'),
	                type: getCookie('admin_type'),
	                itype: this.itype,
	                list: idList.join()
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
	    computed: {
	        //            todoSearch:function () {
	        //                var self = this;
	        //                if(this.goSearch){
	        //                    this.requestList('',function(){
	        //
	        //                    });
	        //                    self.goSearch = false;
	        //                }
	        //                return this.goSearch;
	        //            },
	        //            noSearch:function () {
	        //                if(this.currentKeyWord != this.keyword && this.keyword==''){
	        //                    console.log('no search event run');
	        //                    this.requestList();
	        //                }
	        //                return this.keyword;
	        //            },
	        changeView: function changeView() {
	            if (this.viewPage != this.currentView) {
	                this.requestList(1);
	            }
	            return this.viewPage;
	        },
	        itype: function itype() {
	            if (this.viewPage == 'focus') {
	                return 1;
	            } else {
	                return 2;
	            }
	        }
	    },
	    events: {
	        create: function create(view, title, link, backImgURL, callback) {},
	        modify: function modify(view, newsid, title, link, backImgURL, callback) {}
	    },
	    components: {
	        checkbox: _inputCheckBox2.default,
	        addDialog: _dialog2.default
	    }
	};
	// </script>

/***/ },
/* 26 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_script__ = __webpack_require__(27)
	__vue_template__ = __webpack_require__(28)
	module.exports = __vue_script__ || {}
	if (module.exports.__esModule) module.exports = module.exports.default
	if (__vue_template__) { (typeof module.exports === "function" ? module.exports.options : module.exports).template = __vue_template__ }
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), true)
	  if (!hotAPI.compatible) return
	  var id = "/Users/hantong/Sites/website/mobileGame/huanpeng/htdocs/admin2/vue/newsManage/recommend/dialog.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 27 */
/***/ function(module, exports) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});
	// <template>
	//     <div v-if="isShow">
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
	//                             <div v-if="pageView=='focus'" class="row">
	//                                 <label>自定义推荐</label>
	//                                 <div class="face-box" style="width:260px;height: 116px;">
	//                                     <img :src="posterUrl" alt="">
	//                                     <input type="file" id="poster-upload" v-model="fileURL">
	//                                 </div>
	//                             </div>
	//                             <div class="row">
	//                                 <label for="anchorRecommendNick">标题</label>
	//                                 <input type="text" id="anchorRecommendNick" value="" v-model="title">
	//                             </div>
	//                             <div class="row">
	//                                 <label for="to-index">链接</label>
	//                                 <input type="text" id="to-index" value="" v-model="link">
	//                             </div>
	//                         </form>
	//                     </div>
	//                     <div class="modal-footer">
	//                         <button id="modal-submit"  type="button" class="btn btn-primary yellow-crusta" @click="submit">确定</button>
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
	        posterUrl: {
	            type: String,
	            default: ''
	        },
	        title: {
	            type: String,
	            default: ''
	        },
	        link: {
	            type: String,
	            default: ''
	        },
	        type: {
	            type: String,
	            default: 'create'
	        },
	        newsid: {
	            type: Number,
	            default: 0
	        },
	        pageView: {
	            type: String,
	            default: 'focus'
	        },
	        isShow: {
	            type: Boolean,
	            required: true
	        }
	    },
	    data: function data() {
	        return {
	            fileURL: '',
	            backImgUrl: ''
	        };
	    },
	    computed: {
	        changeImg: function changeImg() {
	            this.posterURL = this.getFileURL();
	            return this.fileURL;
	        }
	    },
	    methods: {
	        getFileURL: function getFileURL() {
	            var url;
	            if (navigator.userAgent.indexOf("MSIE") >= 1) {
	                // IE
	                url = this.fileURL;
	            } else if (navigator.userAgent.indexOf("Firefox") > 0) {
	                // Firefox
	                url = window.URL.createObjectURL(document.getElementById('poster-upload').files.item(0));
	            } else if (navigator.userAgent.indexOf("Chrome") > 0) {
	                // Chrome
	                url = window.URL.createObjectURL(document.getElementById('poster-upload').files.item(0));
	            }
	            return url;
	        },
	        submit: function submit() {
	            var self = this;
	            if (this.type == 'create') {
	                this.$dispatch('create', this.viewPage, this.title, this.link, this.backImgUrl, function () {
	                    self.close();
	                });
	            } else {
	                this.$dispatch('modify', this.viewPage, this.newsid, this.title, this.link, this.backImgUrl, function () {
	                    self.close();
	                });
	            }
	        },
	        close: function close() {
	            this.isShow = false;
	            this.newsid = 0;
	            this.backImgUrl = this.fileURL = this.link = this.title = '';
	        }
	    }
	};
	// </script>

/***/ },
/* 28 */
/***/ function(module, exports) {

	module.exports = "\n    <div v-if=\"isShow\">\n        <div class=\"modal-backdrop fade in\" style=\"z-index:10050\"></div>\n        <div  class=\"modal fade\" :class=\"isShow ? 'in' : ''\" :style=\"isShow ? 'display:block;':''\" role=\"dialog\" aria-hidden=\"true\">\n            <div class=\"modal-dialog\">\n                <div class=\"modal-content\">\n                    <div class=\"modal-header\">\n                        <button type=\"button\" class=\"close\" data-dismiss=\"\" aria-hidden=\"true\" @click=\"close\"></button>\n                        <h4 class=\"modal-title\">发布</h4>\n                    </div>\n                    <div class=\"modal-body form\">\n                        <form action=\"#\" class=\"form-horizontal form-row-seperated\" style=\"padding: 20px 0px 20px 160px;\">\n                            <div v-if=\"pageView=='focus'\" class=\"row\">\n                                <label>自定义推荐</label>\n                                <div class=\"face-box\" style=\"width:260px;height: 116px;\">\n                                    <img :src=\"posterUrl\" alt=\"\">\n                                    <input type=\"file\" id=\"poster-upload\" v-model=\"fileURL\">\n                                </div>\n                            </div>\n                            <div class=\"row\">\n                                <label for=\"anchorRecommendNick\">标题</label>\n                                <input type=\"text\" id=\"anchorRecommendNick\" value=\"\" v-model=\"title\">\n                            </div>\n                            <div class=\"row\">\n                                <label for=\"to-index\">链接</label>\n                                <input type=\"text\" id=\"to-index\" value=\"\" v-model=\"link\">\n                            </div>\n                        </form>\n                    </div>\n                    <div class=\"modal-footer\">\n                        <button id=\"modal-submit\"  type=\"button\" class=\"btn btn-primary yellow-crusta\" @click=\"submit\">确定</button>\n                        <button id=\"modal-close\" type=\"button\" class=\"btn btn-default\" data-dismiss=\"\" @click=\"close\">取消</button>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n";

/***/ },
/* 29 */
/***/ function(module, exports) {

	module.exports = "\n    <table class=\"table table-striped table-bordered table-hover\">\n        <thead>\n        <tr>\n            <th class=\"checkbox-th\"></th>\n            <th>推荐</th>\n            <th v-if=\"viewPage =='focus'\">图片</th>\n            <th v-else>图片</th>\n            <th>标题</th>\n            <th>链接</th>\n            <th>推荐</th>\n            <th>编辑</th>\n        </tr>\n        </thead>\n        <tbody>\n        <tr v-for=\"(index,item) in dataList\" @click=\"mark(index)\">\n            <td><checkbox :is-checked=\"item.checked\"></checkbox></td>\n            <td v-if=\"index==0\"><button class=\"btn\" @click.stop=\"cancelTop(index)\">取消置顶</button></td>\n            <td v-else><button class=\"btn\" @click.stop=\"toTop(index)\">置顶</button></td>\n            <td v-if=\"viewPage =='focus'\"><img :src=\"item.poster\"/></td>\n            <td v-else>{{item.tname}}</td>\n            <td>{{item.title}}</td>\n            <td>{{item.link}}</td>\n            <td><button class=\"btn\" @click.stop=\"cancelPublish(index)\">取消推荐</button></td>\n            <td><button class=\"btn\" @click.stop=\"modifyItem(index)\">编辑</button></td>\n        </tr>\n        </tbody>\n    </table>\n    <nav class=\"handle-option\">\n        <div class=\"col-md-3\">\n            <button class=\"btn btn-circle\" @click=\"allSelect\">全选</button>\n            <button class=\"btn btn-circle\" @click=\"inverseSelect\">反选</button>\n        </div>\n        <div class=\"col-md-6\" style=\"text-align: center\"></div>\n        <div class=\"col-md-3\">\n            <button class=\"recheck btn btn-circle pull-right\" @click=\"deleteItem\">删除</button>\n        </div>\n    </nav>\n    <div v-show=\"false\">{{todoSearch }}</div>\n    <div v-show=\"false\">{{ changeView }}</div>\n    <div v-show=\"false\">{{ noSearch}}</div>\n    <add-dialog :is-show.sync=\"isShow\" :type.sync=\"dialogOptType\" :page-view=\"viewPage\" :title=\"dialogTitle\" :link=\"dialogLink\" :newsid=\"dialogNewid\" :poster-url=\"dialogPosterURL\"></add-dialog>\n";

/***/ }
/******/ ]);