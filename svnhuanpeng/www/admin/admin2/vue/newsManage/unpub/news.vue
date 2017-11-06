<template>
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th class="checkbox-th"></th>
            <th>封面</th>
            <th>标题</th>
            <th>小编</th>
            <th>管理</th>
            <th>编辑</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(index,item) in dataList" @click="mark(index)">
            <td><checkbox :is-checked="item.checked"></checkbox></td>
            <td v-if="item.pic"><img :src="item.pic"/></td>
            <td v-else>无</td>
            <td>{{item.title}}</td>
            <td>{{item.nick}}</td>
            <td><button class="btn" @click.stop="prePublish(index)">发布</button></td>
            <td><a class="btn" @click.stop="turnTo(index)">编辑</a></td>
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
            <button class="recheck btn btn-circle pull-right" @click="deleteItem">删除</button>
        </div>
    </nav>
    <div v-show="false">{{todoSearch + changeView + noSearch}}</div>
    <publish-box :newsid.sync="newsid" :publish-option-show.sync="publishOptionShow"></publish-box>
</template>

<script>
    import crossPage from '../../components/crossPage.vue';
    import btnCheckBox from '../../components/inputCheckBox.vue';
    import publishDialog from './dialog.vue';

    export default{
        props:{
            elementId:{
                type:String,
            },
            pageSize:{
                type:Number,
            },
            sTime:{
                type:String
            },
            eTime:{
                type:String
            },
            tid:{
                type:Number,
                require:true
            },
            keyword:{
                type:String
            },
            goSearch:{
                type:Boolean,
                required:true
            }
        },
        data:function(){
            return {
                dataList:[],
                totalCounts:1,
                currentPage:1,
                params:['checked','title','pic','nick','id'],
                publishOptionShow:false,
                newsid:0,
                currentTid:5,
                currentKeyWord:''//防止第一次重复加载
            }
        },
        ready:function(){
            this.requestList(1);
        },
        computed:{
            todoSearch:function () {
                var self = this;
                if(this.goSearch){
                    this.requestList('',function(){

                    });
                    self.goSearch = false;
                }
                return this.goSearch;
            },
            changeView:function(){
                if(this.currentTid != this.tid){
                    this.requestList();
                    this.currentTid = this.tid;
                }
                return this.tid;
            },
            noSearch:function () {
                if(this.currentKeyWord != this.keyword && this.keyword==''){
                    console.log('no search event run');
                    this.requestList();
                }
                return this.keyword;
            }
        },
        events:{
            pageChange:function (num, doCallBack) {
                console.log('emit run here:' + num);
                this.requestList(num, doCallBack);
            },
            publish:function(newsid,type,callback){
                if(!newsid) return false;

                this.publishRequest(newsid,type,callback);
            }
        },
        methods:{
            requestList:function(page){
                var self = this;
                var doCallBack = typeof arguments[1] == 'function'? arguments[1] : function(){};
                var url = 'http://dev.huanpeng.com/admin2/api/information/info/getInformationList.php';

//                var keyword = '';
                this.currentKeyWord = this.keyword;
//                if(this.goSearch)
//                    keyword = this.keyword || ''


                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie("admin_enc"),
                    type:1,
                    size:this.pageSize,
                    page:page || self.currentPage || 1,
                    status:0,
                    tid:this.tid,
                    stime:this.sTime || '',
                    etime:this.eTime || '',
                    keyword:this.keyword
                }

                ajaxRequest({url:url,data:data},function(d){
                    self.dataList.splice(0, self.dataList.length);
                    self.totalCount = d.total
                    var list = d.list;
                    for(var i in list){
                        var data = {
                            checked:false,
                            title:list[i].title,
                            pic:list[i].poster,
                            nick:list[i].nick,
                            id:list[i].id,
                        }
                        self.dataList.push(data);
                    }
                    self.totalCounts = parseInt(d.total);
                    doCallBack();
                });
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
            deleteItem:function(){
                var list = [];
                for(var i in this.dataList){
                    if(this.dataList[i].checked)
                        list.push(this.dataList[i].id)
                }
                if(!list)
                    return;
                this.deleteItemkRequest(list)
            },
            deleteItemkRequest:function(arr,callback){
                var self = this;
                var url = 'http://dev.huanpeng.com/admin2/api/information/info/changeInformation.php';
                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie('admin_enc'),
                    type:getCookie('admin_type'),
                    status:2,
                    id:arr.join(),

                };
                ajaxRequest({url:url, data:data}, function () {
                    self.requestList();
                })
            },
            prePublish:function (index) {
                this.publishOptionShow = true;
                this.newsid = Number(this.dataList[index].id);
            },
            publishRequest:function(newsid,type,callback){
                var self = this;
                var url = 'http://dev.huanpeng.com/admin2/api/information/info/changeInformation.php';
                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie('admin_enc'),
                    type:getCookie('admin_type'),
                    id:newsid,
                    status:1,
                    isRecommend:Number(type)
                }
                ajaxRequest({url:url,data:data},function () {
                    self.requestList();
                    callback && typeof callback == 'function' && callback();
                },function (d) {
                    alert(d.desc);
                });
            },
            turnTo:function (index) {
                location.href = "http://dev.huanpeng.com/admin2/view/newsCreate.php?sidebar=8-0&nid="+this.dataList[index].id;
            }
        },
        components:{
            'pageCross':crossPage,
            'checkbox':btnCheckBox,
            publishBox:publishDialog
        }

    }
</script>