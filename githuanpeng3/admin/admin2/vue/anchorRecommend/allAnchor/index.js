/**
 * Created by hantong on 16/12/1.
 */
import Vue from 'Vue';
import $ from 'jquery';

import recommend from './table.vue';

$(document).ready(function () {
	new Vue({
		el:"#anchorRecommend",
		data:{
			viewPage:'recommend',
			keyword:'',
			goSearch:false,
			isShow:false,
			viewPageList:['recommend','all']
		},
		methods:{
			changeView:function (index) {
				this.viewPage = this.viewPageList[index];
			},
			create:function(){
				this.isShow = true;
			},
			search:function () {
				this.goSearch = true;
			}
		},
		components:{
			recommend:{
				props:{
					keyword:String,
					goSearch:{
						type:Boolean,
						required:true,
					},
					elementId:String,
					isShow:Boolean,
					viewPage:{
						type:String,
						required:true
					}
				},
				template:'<div class="tab-pane user-head-list active" id="news"><recommend-table :go-search.sync="goSearch" :keyword="keyword"  :element-id="elementId" :is-show.sync="isShow" :view-page="viewPage"></recommend-table></div>',
				components:{
					recommendTable:recommend
				}
			},
		}
	});
});