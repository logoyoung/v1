/**
 * Created by hantong on 16/11/29.
 */
import Vue from 'Vue';
import $ from 'jquery';

import unpublish from  './news.vue';


$(document).ready(function () {

	new Vue({
		el:"#unpublishBody",
		data:{
			currentView:'news',
			viewList:['news','events','announcement'],
			typeList:{
				news:5,
				events:8,
				announcement:13
			},
			keyword:'',
			goSearch:false,
			tid:5
		},
		ready:function(){
			console.log('indexs goSearch' + this.goSearch);
		},
		computed:{
			lisKeyWord:function () {
				return this.keyword;
			}
		},
		methods:{
			changeView:function (index) {
				this.currentView = this.viewList[index];
				this.tid = this.typeList[this.currentView];
				console.log(this.tid);
			},
			create:function(){
				location.href = 'http://dev.huanpeng.com/admin2/view/newsCreate.php?sidebar=8-0&type=' + this.typeList[this.currentView];
			},
			search:function () {
				console.log('pre do search and set goSearch=true');
				this.goSearch = true;
			}
		},
		components:{
			news:{
				props:{
					keyword:String,
					goSearch:{
						type:Boolean,
						required:true
					},
					tid:{
						type:Number,
						required:true
					},
					elementId:String
				},
				ready:function(){
					console.log('news' + this.gosearch);
				},
				computed:{
					listenTid:function(){
						return this.tid;
					}
				},
				template:'<div class="tab-pane user-head-list active" id="news"><unpublish :go-search.sync="goSearch" :keyword="keyword" :tid="tid" :element-id="elementId"></unpublish></div>',
				components:{
					unpublish:unpublish
				}
			},
		}
	});
});