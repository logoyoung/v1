/**
 * Created by hantong on 16/12/2.
 */

import Vue from 'Vue';
import $ from 'jquery';

import recommend from './table.vue';


$(document).ready(function () {
	new Vue({
		el:'#newsRecommend',
		data:{
			viewPage:'focus',
			isShow:false,
			viewPageList:['focus','news'],
			dialogOptType:'create',
		},
		methods:{
			create:function(){
				this.isShow = true;
				this.dialogOptType = 'create';
			},
			changeView:function(index){
				this.viewPage = this.viewPageList[index];
			}
		},
		components:{
			recommend:{
				props:{
					isShow:Boolean,
					viewPage:{
						type:String,
						required:true
					},
					dialogOptType:{
						type:String,
					}
				},
				template:'<div class="tab-pane user-head-list active" id="newRecommendContent"> <recommend-table  :is-show.sync="isShow" :view-page="viewPage" :dialog-opt-type.sync="dialogOptType"></recommend-table> </div>',
				components:{
					recommendTable:recommend
				}
			}
		}
	})
});