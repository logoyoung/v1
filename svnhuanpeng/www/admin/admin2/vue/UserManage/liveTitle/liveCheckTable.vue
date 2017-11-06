<template>
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th class="checkbox-th"></th>
            <th>直播标题</th>
            <th>昵称</th>
            <th>UID</th>
            <th>提交时间</th>
            <th>状态</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(index ,item) in dataList" @click="mark(index)">
            <td><checkbox :is-checked="item.checked"></checkbox></td>
            <td>{{item.liveTitle}}</td>
            <td>{{item.nick}}</td>
            <td>{{item.uid}}</td>
            <td>{{item.ctime}}</td>
            <td><span class="icon" :class="checkStatus(item.status)"></span>{{checkText(item.status)}}</td>
        </tr>
        </tbody>
    </table>
    <nav class="handle-option">
        <div class="col-md-3">
            <button class="btn btn-circle" @click="allSelect">全选</button>
            <button class="btn btn-circle" @click="inverseSelect">反选</button>
        </div>
        <div class="col-md-6" style="text-align: center">
            <page-cross :element-id="elementId" :page-size="pageSize" :total-counts="totalCounts" :current-page="currentPage"></page-cross>
        </div>
        <div class="col-md-3" style="text-align: right" v-if="checkedPage">
            <button class="btn unpassed" @click="unpassed">驳回</button>
            <button class="btn passed" @click="passed">通过</button>
        </div>
        <div class="col-md-3" style="text-align: right" v-if="!checkedPage">
            <button class="btn recheck" @click="reCheck">重新审核</button>
        </div>
    </nav>
</template>
<script>
    import crossPage from '../../components/crossPage.vue';
    import checkbox from '../../components/inputCheckBox.vue';

    export default{
        props:{
            elementId:{
                type:String,
            },
            pageSize:{
                type:Number,
                default:10,
            },
            sTime:{
                type:String,
            },
            eTime:{
                type:String
            },
            checkedPage:{
                type:Boolean,
                default:false
            },
            status:{
                type:Number,
                default:0
            }
        },
        data:function () {
            return{
                dataList:[],
                totalCounts:1,
                currentPage:1,
                params:['checked','liveTitle','nick','uid','ctime','status','liveid'],
                url:$conf.live.api +'getLiveTitle.php',
                passUrl:$conf.live.api + 'checkLiveTitle.php',
                unpassUrl:$conf.live.api + 'checkLiveTitle.php',
                reCheckUrl:$conf.live.api + ''
            }
        },

        ready:function () {
            this.requestList(1);
        },
        events:{
            pageChange:function (num, doCallBack) {
                this.requestList(num, doCallBack);
            }
        },
        methods:{
            requestList:function(page){
                var self = this;
                var doCallBack = typeof arguments[1] == 'function' ? arguments[1] : function () {};
                var url = this.url;
                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie('admin_enc'),
                    type:1,
                    page:page,
                    size:this.pageSize,
                    status:this.status,
                    stime:this.stime||'',
                    etime:this.etime||''
                };

                ajaxRequest({url:url, data:data}, function (d) {
                    self.dataList.splice(0,self.dataList.length);
                    self.totalCount = d.total;
                    var list = d.data;
                    for(var i in list){
                        var data = {
                            checked:false,
                            liveTitle:list[i].title,
                            nick:list[i].nick,
                            uid:list[i].uid,
                            ctime:list[i].ctime,
                            status:list[i].status,
                            liveid:list[i].liveid
                        }
                        self.dataList.push(data);
                    }
                    self.totalCounts = parseInt(d.total);
                    doCallBack();
                })
            },

            mark:function (index) {
                var data = rebuildVueData(this.params, this.dataList[index]);
                data.checked = !data.checked;
                this.dataList.$set(index, data);
            },

            allSelect:function () {
                for(var i in this.dataList){
                    var data = rebuildVueData(this.params, this.dataList[i]);
                    data.checked = true;
                    this.dataList.$set(i, data);
                }
            },
            inverseSelect:function(){
                for(var i in this.dataList){
                    this.mark(i);
                }
            },
            reCheck:function () {
                var uids = this.getCheckedliveIds();

            },
            passed:function () {
                var self = this;
                var liveids = this.getCheckedliveIds();
                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie('admin_enc'),
                    type:getCookie('admin_type'),
                    succList:liveids['select'].join(),
                    failedList:liveids['unselect'].join()
                };
                ajaxRequest({url:this.passUrl, data:data},function(d){
                    self.requestList();
                },function (d) {
                   alert(d.desc);
                });
            },
            unpassed:function(){
                var self = this;
                var liveids = this.getCheckedliveIds();
                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie('admin_enc'),
                    type:getCookie('admin_type'),
                    succList:liveids['unselect'].join(),
                    failedList:liveids['select'].join()
                };
                ajaxRequest({url:this.unpassUrl, data:data},function(d){
                    self.requestList();
                },function (d) {
                   alert(d.desc);
                });

            },
            getCheckedliveIds:function(){
                var unselect = [];
                var select = [];
                for(var i in this.dataList){
                    if(this.dataList[i].checked){
                        if(this.dataList[i].liveid)
                            select.push(this.dataList[i].liveid);
                    }else{
                        if(this.dataList[i].liveid)
                            unselect.push(this.dataList[i].liveid);
                    }

                }
                return {select:select,unselect:unselect};
            },
            clearList:function () {
                this.dataList.splice(0,this.dataList.length);
            },
            checkStatus:function(status){
                return ['wait','pass','unpsas'][status];
            },
            checkText:function (status) {
                return ['审核中','已通过','未通过'][status];
            }
        },
        components:{
            checkbox:checkbox,
            pageCross:crossPage
        }
    }
</script>