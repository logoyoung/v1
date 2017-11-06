<template>
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th class="checkbox-th"></th>
            <th>头像</th>
            <th>昵称</th>
            <th>UID</th>
            <th>提交时间</th>
            <th>状态</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(index,item) in dataList" @click="mark(index)">
            <td><checkbox :is-checked="item.checked"></checkbox></td>
            <td><img alt="" :src="item.imgUrl"></td>
            <td>{{item.nick}}</td>
            <td>{{item.uid}}</td>
            <td>{{item.ctime}}</td>
            <td><span class="icon" :class="item.pass? pass : unpass"></span>{{item.pass ? '通过' : '未通过'}}</td>
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
        <div class="col-md-3">
            <button class="recheck btn btn-circle pull-right" @click="reCheck">重新审核</button>
        </div>
    </nav>
</template>

<script>
    import crossPage from '../../components/crossPage.vue';
    import btnCheckBox from '../../components/inputCheckBox.vue';

    export default{
        props:{
            elementId:{
                type:String,
            },
            pageSize:{
                type:Number,
            },
            status:{//0:wait, 1:pass, 2:unpass
                type:Number,
                default:1
            },
            sTime:{
                type:String
            },
            eTime:{
                type:String
            }
        },
        data:function(){
            return {
                dataList:[],
                totalCounts:1,
                currentPage:1,
                params:['checked','uid','imgUrl','nick','status']
            }
        },
        ready:function(){
            this.requestList(1);
        },
        events:{
            pageChange:function (num, doCallBack) {
                console.log('emit run here:' + num);
                this.requestList(num, doCallBack);
            }
        },
        methods:{
            requestList:function(page){
                var self = this;
                var doCallBack = typeof arguments[1] == 'function'? arguments[1] : function(){};
                var url = $conf.user.api + 'getWaitPassList.php';
                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie("admin_enc"),
                    type:1,
                    size:this.pageSize,
                    page:page || 1,
                    status:this.status,
                    stime:this.sTime || '',
                    etime:this.eTime || ''
                }

                ajaxRequest({url:url,data:data},function(d){
                    self.dataList.splice(0, self.dataList.length);
                    self.totalCount = d.total
                    var list = d.data;
                    var statusList = [0,1,0];
                    for(var i in list){
                        var data = {
                            checked:false,
                            imgUrl:list[i].pic,
                            uid:list[i].uid,
                            nick:list[i].nick,
                            ctime:list[i].ctime,
                            pass:statusList[list[i].status]
                        }
                        self.dataList.push(data);
                    }
                    self.totalCounts = parseInt(d.total);
                    doCallBack();
                })
            },

            mark:function(index){
                var data = rebuildVueData(this.params, this.dataList[index]);
                data.checked = !data.checked;
                this.dataList.$set(index, data);
            },
            allSelect:function(){
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
            reCheck:function(){
                var list = [];
                for(var i in this.dataList){
                    if(this.dataList[i].checked)
                    list.push(this.dataList[i].uid)
                }
                if(!list)
                    return;
               this.reCheckRequest(list)
            },
            reCheckRequest:function(arr){
                var self = this;
                var url = $conf.user.api +'xxx.php';
                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie('admin_enc'),
                    uids:arr.join()
                };
                ajaxRequest({url:url, data:data}, function () {
                    self.requestList()
                })
            }
        },
        components:{
            'pageCross':crossPage,
            'checkbox':btnCheckBox
        }

    }
</script>