/**
 * Created by hantong on 16/11/1.
 */
import Vue from 'Vue';
import $ from 'jquery';

import HeadCheckBox from './headCheckBox.vue';
import headCheckTable from './headCheckTable.vue';

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

$(document).ready(function(){

   new Vue({
       el:'#headCheckBody',
       data:{
           currentView:'check',
           viewList:['check','pass','unpass']
       },
       methods:{
           changeView:function(index){
               this.currentView = this.viewList[index];
           },
       },
       components:{
           check:{
               template:'<div class="tab-pane user-head-list active" id="head-wait"> <div id="userCheckBox"> <head-check-box ></head-check-box> </div>',//:check-list="checkList"
               components:{
                   headCheckBox:HeadCheckBox
               }
           },
           pass:{
               template:'<div class="tab-pane user-head-list" id="head-pass"> <head-table-show :element-id="\'head-pass\'" :status="1"></head-table-show> </div>',
               components:{
                   'headTableShow':headCheckTable
               }
           },
           unpass:{
               template:'<div class="tab-pane user-head-list" id="head-pass"> <head-table-show :element-id="\'head-unpass\'" :status="0"></head-table-show> </div>',
               components:{
                   'headTableShow':headCheckTable
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