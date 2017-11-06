<template>
    <div class="head-succ-list col-md-5-5">
        <userheadcheckbox :types="1" :title="'通过'" :check-list="checkList"></userheadcheckbox>
    </div>
    <div class="head-check-exchange col-md-1">
        <div class="option-group"><button type="button" class="btn btn-circle" @click="exchange"></button></div>
        <div class="option-group"><button type="button" class="btn btn-circle" @click="exchange"></button></div>
    </div>
    <div class="head-failed-list col-md-5-5">
        <userheadcheckbox :types="0" :title="'驳回'" :check-list="checkList"></userheadcheckbox>
    </div>
    <div class="head-check-submit col-md-12">
        <button type="submit" @click="submit" class="btn  submit">提交审核</button>
    </div>
</template>


<script>
    import headBox from './headBox.vue';
    export default {
        data:function () {
            return {
                checkList:[]
            }
        },
        ready:function(){
            var self = this;
            this.getUserInfo(function (arr) {
                for(var i in arr){
                    self.checkList.push(arr[i]);
                }
          });
        },
        methods: {
            exchange: function () {
                console.log(this.checkList);
                var list = this.checkList;
                for (var index in list) {
                    var stat = this.checkList[index].stat;
                    var imgUrl = this.checkList[index].imgUrl;
                    var uid = this.checkList[index].uid;
                    this.checkList.$set(index, {uid:uid,stat: !stat, imgUrl: imgUrl});
                }
            },
            submit: function () {
                var succList = [];
                var failedList = [];
                for (var i in this.checkList) {
                    if (this.checkList[i].stat) {
                        succList.push(this.checkList[i].uid);
                    } else {
                        failedList.push(this.checkList[i].uid);
                    }
                }
                console.log(succList);
                console.log(failedList);
                var self = this;
                var requestData = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie('admin_enc'),
                    succuid:succList.join(),
                    failuid:failedList.join()
                }
                //set pass and request new list
                ajaxRequest({url:$conf.user.api + 'setPicPass.php',data:requestData}, function () {
                    self.checkList.splice(0, self.checkList.length);//clear all
                    console.log(self.checkList);
                    self.getHeadInfo(function(d){
                        for(var i in d){
                            self.checkList.push(d[i]);
                        }
                    });
                });
            },
            getUserInfo:function (doCallBack) {
                this.checkList.splice(0, this.checkList.length);

                var url = $conf.user.api + 'getWaitPassList.php';
                var data = {uid:getCookie('admin_uid'), encpass:getCookie('admin_enc')}
                ajaxRequest({url:url,data:data}, function (d) {
                    var list = d.data;
                    var arr = [];
                    for(var i in list){
                        var data = list[i];
                        arr.push({
                            uid:data.uid,
                            stat:1,
                            imgUrl:data.pic
                        });
                    }
                    typeof doCallBack =='function' && doCallBack(arr);
                });
            }
        },
        components:{
            'userheadcheckbox':headBox
        }
    }
</script>