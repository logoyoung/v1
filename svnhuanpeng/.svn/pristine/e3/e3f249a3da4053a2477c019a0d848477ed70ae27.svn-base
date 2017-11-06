/**
 * Created by hantong on 16/11/14.
 */

import Vue from 'Vue';
import $ from "jquery";

import titleCheckTable from './liveCheckTable.vue';

$(document).ready(function () {
	new Vue({
		el:"#titleCheckBody",
		data:{
			currentView:'check',
			viewList:['check','pass','unpass']
		},
		methods:{
			changeView:function (index) {
				this.currentView = this.viewList[index];
			}
		},
		components:{
			check:{
				template:'<div class="tab-pane user-head-list active" id="wait"><title-check-table :element-id="\'title-check\'" :checked-page="true" :status="0"></title-check-table> </div>',//:check-list="checkList"
				components:{
					titleCheckTable:titleCheckTable
				}
			},
			pass:{
				template:'<div class="tab-pane user-head-list" id="pass"> <title-check-table :element-id="\'title-pass\'" :checked-page="false" :status="1"></title-check-table> </div>',
				components:{
					titleCheckTable:titleCheckTable
				}
			},
			unpass:{
				template:'<div class="tab-pane user-head-list" id="unpass"> <title-check-table :element-id="\'title-unpass\'" :checked-page="false" :status="2"></title-check-table> </div>',
				components:{
					titleCheckTable:titleCheckTable
				}
			}
		}
	})
});