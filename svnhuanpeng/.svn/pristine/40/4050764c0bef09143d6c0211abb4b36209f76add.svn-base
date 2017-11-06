(function ($) {
	var nextpage = {
		isInit : false,
		itemArray : [], // 待处理对象数组
		itemExists : function (item) { // 判断待处理对象是否存在，并返回对象所在数组位置
			if(!item || !item.obj) return -1;
			var length = this.itemArray.length;
			for (var i = 0; i < length ; i++) {
				if(item.obj.selector == this.itemArray[i].obj.selector) return i;
			}
			return -1;
		},
		addItem : function (obj, options) { // 添加待处理对象
			if(!obj) return false;
			var item = {
				obj : obj, // 当前处理对象
				options : options, // 配置参数
				isProcessing : false, // 是否正在处理
				isEnd : false, // 处理完成，无更多数据
				tempPreObj : {} // 预加载缓存数据对象
			};
			var index = this.itemExists(item);
			if(-1 == index) {
				this.itemArray.push(item); // 不存在时添加
			} else {
				this.itemArray[index] = item; // 存在时更新
			}
			return item;
		},
		scrollFn:function(triggerObj,options,iscrollObj){
			//debugger;
			var isTriggering = false,
			$win = $(window);
			if(isTriggering) return true;
			nextpage.log('trigger...', nextpage.itemArray);
			isTriggering = true;
			var windowHeight = $win.height(),
			scrollTop = $win.scrollTop(),
			objOffsetTop = triggerObj.offset().top;
			nextpage.log('objOffsetTop:' + objOffsetTop + ', windowHeight:' + windowHeight + ', scrollTop:' + scrollTop);
			if(!iscrollObj){
				if((windowHeight+scrollTop)<objOffsetTop-(options.offsetY?options.offsetY:0)){
					isTriggering = false;
					return true;
				}
			}else{
				if(iscrollObj.y>iscrollObj.maxScrollY){
					isTriggering = false;
					return true;
				}
			}

			for (var i in nextpage.itemArray) {
				var item = nextpage.itemArray[i];
				if(item.options.bPreLoad) { // 预加载
					nextpage.processPreData(item, item.options.pageCurrent + 1, false);
				} else {
					nextpage.process(item);
				}
			}
			
			isTriggering = false;
		},
		init : function (obj, options) { // 初始化函数
			var item = this.addItem(obj, options),
			$win = $(window),
			isBindIscroll = false;

			options.onInit(obj, options); // 触发初始化完成事件
			if(item.options.bPreLoad) { // 执行预加载
				nextpage.processPreLoad(item, item.options.pageCurrent + 1);
			}
			if(options.bLoadOnInit) { // 立即加载数据
				if(item.options.bPreLoad) {
					this.processPreData(item, item.options.pageCurrent + 1, false);
				} else {
					this.process(item);
				}
			}
			if(this.isInit) return false;
			this.isInit = true;
			var isTriggering = false;

			$win.on('scroll', function(){
				nextpage.scrollFn(obj,options,window.iscrollObj);
				if(window.iscrollObj){
					if(!isBindIscroll){
						iscrollObj.on('scrollEnd',  function(){
							nextpage.scrollFn(obj,options,iscrollObj);
						});
						isBindIscroll = true;
					}
				}else{
					isBindIscroll = false;
				}
			}).trigger('scroll'); // 模拟触发
			
		},
		process : function (item) { // 处理子程序
			//debugger;
			if(item.isProcessing || item.isEnd) return false ;
			nextpage.log('process...', item, nextpage.itemArray);
			item.isProcessing = true;
			item.options.onProcess(item.obj, item.options); // 触发开始处理事件
			$.ajax({
				url : item.options.urlFormater(item.options, item.options.url, ++item.options.pageCurrent),
				data : item.options.paramters(item.options.pageCurrent),
				dataType : null == item.options.jsonp ? 'json' : 'jsonp',
				jsonp : item.options.jsonp,
				success : function (data) {
					//debugger;
					nextpage.log('process data...', item, nextpage.itemArray);
					nextpage.processData(item, data, false);
					if(!item.isEnd) item.options.onPrcessDone(item.obj, data, item.options); // 触发处理结束事件
					item.isProcessing = false;
					nextpage.log('process done...', item, nextpage.itemArray);
					item.options.processCallback(item.obj, data, item.options); // 单次处理结束回调
				}
			});
		},
		/**
		 * 预加载数据
		 * @param page 预加载page页对应的数据
		 */
		processPreLoad : function (item, page) {
			item.tempPreObj[page] = {
				bTrigger : false, // 是否已被请求调用
				bLoaded : false, // 数据是否加载完成
				data : null // 预加载数据
			};
			$.ajax({
				url : item.options.urlFormater(item.options, item.options.url, page),
				data : item.options.paramters(page),
				dataType : null == item.options.jsonp ? 'json' : 'jsonp',
				jsonp : item.options.jsonp,
				success : function (data) {
					item.tempPreObj[page].bLoaded = true;
					item.tempPreObj[page].data = data;
					if(page - item.options.pageCurrent <= 1) { // 先处理数据，后触发中断
						nextpage.processData(item, data, true);
					}
					if(item.tempPreObj[page].bTrigger && !item.isEnd) { // 继续执行用户触发中断
						nextpage.processPreData(item, page, true);
					}
				}
			});
		},
		/**
		 * 预加载数据处理程序
		 * @param page 处理page页对应的数据
		 * @param bFromLoaded 是否来自加载完成调用
		 */
		processPreData : function (item, page, bFromLoaded) {
			var temp = item.tempPreObj[page];
			if(!temp) return false; // 程序异常，当前页未执行预加载
			temp.bTrigger = true;
			if(!bFromLoaded) {
				if(item.isProcessing || item.isEnd) return false ;
				nextpage.log('process...', item, nextpage.itemArray);
				item.isProcessing = true;
				item.options.onProcess(item.obj, item.options); // 触发开始处理事件
			}
			if(!temp.bLoaded) return false; // 数据未加载完成，等待加载完成后触发
			nextpage.log('process data...', item, nextpage.itemArray);
			nextpage.processData(item, temp.data, false);
			if(!item.isEnd) item.options.onPrcessDone(item.obj, temp.data, item.options); // 触发处理结束事件
			item.isProcessing = false;
			nextpage.log('process done...', item, nextpage.itemArray);
			item.options.processCallback(item.obj, temp.data, item.options); // 单次处理结束回调
			delete item.tempPreObj[page]; // 清除已完成缓存
			item.options.pageCurrent++; // 当前页加一
			nextpage.processPreLoad(item, page + 1);
		},
		isEmptyData : function (data) { // 判断数据是否为空
			if(!data || ($.isArray(data) && 0 == data.length) || $.isEmptyObject(data)) {
				return true;
			}
			return false;
		},
		processData : function (item, data, bJustCheck) { // 数据处理子程序
			data = item.options.dataFormater(data);
			if(this.isEmptyData(data)) { // 已处理完
				item.isEnd = true;
				item.options.onDone(item.obj, data, item.options); // 触发数据全部处理完成事件
				item.options.doneCallback(item.obj, data, item.options); // 数据全部处理完成回调
				return false;
			}
			if(bJustCheck) return true;
			var html = item.options.htmlFormater(data);
			$(item.options.containerSelector).append(html);

			window.iscrollObj?iscrollObj.refresh():false;
		},
		log : function () { // 输出日志
			if(!$.fn.nextpage.debug) return false;
			for (var i in arguments) {
				console.log(arguments[i]);
			}
		}
	};

	$.fn.nextpage = function (options) {
		options = $.extend({}, $.fn.nextpage.defaults, options);
		if(null == options.url) options.url = window.location.href;
		nextpage.init($(this), options);
	};

	$.fn.nextpage.debug = false;
	$.fn.nextpage.defaults = {
		url : null, // 请求地址，若为null则取当前页面地址
		paramters : function (page) { // 请求参数
			return {};
		},
		jsonp : null, // JSONP参数名称
		pageCurrent : 1, // 当前页码
		pageSize : 20, // 分页大小
		containerSelector : '#nextpage-container', // 容器选择器
		templateId : 'nextpage-template', // 模板ID
		offsetY : 100, //默认的Y轴偏移量
		urlFormatNum : 1, // 请求数据格式化类型
		/**
		 * 格式化请求地址
		 * @param url 页面地址
		 * @param pageNext 下一页页码
		 */
		urlFormater : function (options, url, pageNext) {
			var tmpUrl = url;
			var temUrlParamStr = '';
			var indexParamMark = url.indexOf('?');
			if(-1 != indexParamMark) {
				tmpUrl = url.substring(0, indexParamMark);
				temUrlParamStr = url.substring(indexParamMark);
			}
                        if(temUrlParamStr){
                            temUrlParamStr += '&page='+pageNext;
                        }else{
                            temUrlParamStr = '?page='+pageNext;
                        }
			return tmpUrl + temUrlParamStr;
		},
		dataFormater : function (data) { // 格式化返回数据
			return data.data;
		},
		htmlFormater : function (data) { // 渲染模板
			return baidu.template(this.templateId, data);
		},
		htmlTip : '向上滑动加载更多', // 操作提示
		htmlLoad : '正在加载更多信息', // 加载提示
		htmlDone : '没有更多内容了~', // 处理完成提示
		bLoadOnInit : false, // 初始化完成后立即加载数据
		bPreLoad : false, // 预加载下一页
		onInit : function (obj, options) { // 初始化完成
			obj.html(this.htmlTip);
		},
		onProcess : function (obj, options) { // 开始处理
			obj.html(this.htmlLoad);
		},
		onPrcessDone : function (obj, data, options) { // 处理结束
			obj.html(this.htmlTip);
		},
		onDone : function (obj, data, options) { // 数据全部加载完成
			obj.html(this.htmlDone);
		},
		processCallback : function (obj, data, options) {}, // 单次处理完成回调
		doneCallback : function (obj, data) {} // 数据全部加载完成回调
	};
})('undefined' == typeof(Zepto) ? jQuery : Zepto);