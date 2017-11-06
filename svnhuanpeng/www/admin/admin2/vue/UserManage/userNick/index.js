import Vue from 'Vue';
import $ from "jquery";

import nickCheckTable from './nickCheckTable.vue';

$(document).ready(function () {
	new Vue({
		el:"#nickCheckBody",
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
				template:'<div class="tab-pane user-head-list active" id="check-wait"><nick-check-table :element-id="\'nick-check\'" :checked-page="true" :status="0"></nick-check-table> </div>',//:check-list="checkList"
				components:{
					nickCheckTable:nickCheckTable
				}
			},
			pass:{
				template:'<div class="tab-pane user-head-list" id="check-pass"> <nick-check-table :element-id="\'nick-pass\'" :checked-page="false" :status="1"></nick-check-table> </div>',
				components:{
					nickCheckTable:nickCheckTable
				}
			},
			unpass:{
				template:'<div class="tab-pane user-head-list" id="check-unpass"> <nick-check-table :element-id="\'nick-unpass\'" :checked-page="false" :status="2"></nick-check-table> </div>',
				components:{
					nickCheckTable:nickCheckTable
				}
			}
		}
	})
});