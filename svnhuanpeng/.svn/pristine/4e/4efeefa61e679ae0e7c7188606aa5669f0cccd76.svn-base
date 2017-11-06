<template>
    <table v-if="viewPage=='all'" class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th class="checkbox-th"></th>
            <th>顺序</th>
            <th>头像</th>
            <th>昵称</th>
            <th>UID</th>
            <th>直播状态</th>
            <th>管理</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(index,item) in dataList" @click="mark(index)">
            <td><checkbox :is-checked="item.checked"></checkbox></td>
            <td>{{index + 1}}</td>
            <td><img :src="item.pic"/></td>
            <td>{{item.nick}}</td>
            <td>{{item.uid}}</td>
            <td>{{item.liveStatus}}</td>
            <td><button class="btn" @click.stop="publish(index)">推荐</button></td>
        </tr>
        </tbody>
    </table>
    <table v-else class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th>顺序</th>
            <th>头像</th>
            <th>昵称</th>
            <th>UID</th>
            <th>直播状态</th>
            <th>管理</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(index,item) in dataList" >
            <td v-if="index==0"><button class="btn" @click="cancelTop(index)">取消置顶</button></td>
            <td v-else><button class="btn" @click="toTop(index)">置顶</button></td>
            <td><img :src="item.pic"/></td>
            <td>{{item.nick}}</td>
            <td>{{item.uid}}</td>
            <td>{{item.liveStatus}}</td>
            <td><button class="btn" @click.stop="cancelPublish(index)">取消推荐</button></td>
        </tr>
        </tbody>
    </table>
    <nav v-if="viewPage=='all'" class="handle-option">
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
    <div v-show="false">{{todoSearch }}</div>
    <div v-show="false">{{ changeView }}</div>
    <div v-show="false">{{ noSearch}}</div>
    <add-dialog :is-show.sync="isShow"></add-dialog>
</template>

<script>
    import crossPage from '../../components/crossPage.vue';
    import btnCheckBox from '../../components/inputCheckBox.vue';
    import addDialog from '../dialog.vue';
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
            viewPage:{
                type:String,
                require:true
            },
            keyword:{
                type:String
            },
            goSearch:{
                type:Boolean,
                required:true
            },
            isShow:{
                typ:Boolean,
                required:true
            }

        },
        data:function () {
            return {
                dataList:[],
                totalCounts:1,
                currentPage:1,
                params:['checked','pic','nick','uid','ctime','liveStatus'],
                publishOptionShow:false,
                currentView:'recommend',
                currentKeyWord:''//防止第一次重复加载
            }
        },
        ready:function () {
            this.requestList();
            console.log(this.viewPage);
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
            noSearch:function () {
                if(this.currentKeyWord != this.keyword && this.keyword==''){
                    console.log('no search event run');
                    this.requestList();
                }
                return this.keyword;
            },
            changeView:function () {
                if(this.viewPage != this.currentView){
                    this.requestList(1);
                }
                return this.viewPage;
            }
        },
        events:{
            pageChange:function (num, doCallBack) {
                console.log('emit run here:' + num);
                this.requestList(num, doCallBack);
            },
            addAnchor:function(nick,toIndex,callBack){
                if(!nick){
                    alert('昵称不能为空');
                    return;
                }
                var self = this;
                var url = 'http://dev.huanpeng.com/admin2/api/recommend/live/addToWaitList.php';
                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie('admin_enc'),
                    type:getCookie('admin_type'),
                    addType:1,
                    nick:nick
                };
                ajaxRequest({url:url,data:data},function (d) {
                    var uid = d.luid;
                    alert('添加成功');
                    if(toIndex){
                        self.publishRequest(uid,callBack);
                    }else{
                        self.requestList();
                        callBack();

                    }
                },function (d) {
                    alert(d.desc);
                });
            }
        },
        methods:{
            requestList:function(page){
                var self = this;
                var doCallBack = typeof arguments[1] == 'function'? arguments[1] : function(){};
                var url = '';

                this.currentView = this.viewPage;

                if(self.currentView == 'recommend'){
                    url = 'http://dev.huanpeng.com/admin2/api/recommend/live/getRecommentList.php';
                }else{
                    url = 'http://dev.huanpeng.com/admin2/api/recommend/live/getWaitList.php';
                }

                this.currentKeyWord = this.keyword;

                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie('admin_enc'),
                    type:getCookie('admin_type'),
                    size:this.pageSize,
                    page:page || self.currentPage || 1,
                    searchType:1,
                    keyword:this.keyword
                }

                ajaxRequest({url:url,data:data},function(d){
                    self.dataList.splice(0, self.dataList.length);
                    var list = d.list;
                    for(var i in list){
                        var data = {
                            checked:false,
//                            title:list[i].title,
                            pic:list[i].head,
                            nick:list[i].nick,
                            uid:list[i].uid,
                            ctime:list[i].ctime,
                            liveStatus:list[i].isLiving == 1 ? '正在直播' : '暂未直播'
                        }
                        self.dataList.push(data);
                    }
                    self.totalCounts = parseInt(d.total) || 1;
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
                        list.push(this.dataList[i].uid)
                }
                if(!list)
                    return;
                this.deleteItemkRequest(list)
            },
            deleteItemkRequest:function(arr,callback){
                var self = this;
                var url = 'http://dev.huanpeng.com/admin2/api/recommend/live/removeWaitList.php';
                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie('admin_enc'),
                    type:getCookie('admin_type'),
                    luid:arr.join(),
                };
                ajaxRequest({url:url, data:data}, function () {
                    self.requestList();
                })
            },
            cancelPublish:function(index){
                var uid = this.dataList[index].uid;
                if(!uid) return;

                var self = this;
                var url = 'http://dev.huanpeng.com/admin2/api/recommend/live/removeRecommend.php';
                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie('admin_enc'),
                    type:getCookie('admin_type'),
                    luid:uid
                };
                ajaxRequest({url:url,data:data},function () {
                    self.requestList();
                },function (d) {
                    alert(d.desc);
                })
            },
            publish:function (index,callBack) {
                var uid = this.dataList[index].uid;
                this.publishRequest(uid,callBack);
            },
            publishRequest:function (uid,callBack) {
                if(!uid)
                    return;
                var self = this;
                var url = 'http://dev.huanpeng.com/admin2/api/recommend/live/addToRecommend.php';
                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie('admin_enc'),
                    type:getCookie('admin_type'),
                    luid:uid,
                    client:2
                };
                ajaxRequest({url:url,data:data}, function () {
                    self.requestList();
                    callBack && typeof callBack == 'function' && callBack(1);
                },function(d){
                    alert(d.desc);
                    callBack && typeof callBack == 'function' && callBack(0);
                });
            },
            //recommend
            cancelTop:function(index){
                this.toTop(1);
            },
            toTop:function(index){
                var self = this;
                var list = this.getTempList();
                if(list.length < index+1)
                    return;

                var newTop = list[index];
                var newList = [];
                var uidList = [];

                delete list[index];

                newList.push(newTop);
                for(var i in list){
                    if(list[i])
                        newList.push(list[i]);
                }
                for(var i in newList){
                    uidList.push(newList[i].uid);
                }

                var url = 'http://dev.huanpeng.com/admin2/api/recommend/live/changeOrder.php';
                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie('admin_enc'),
                    type:getCookie('admin_type'),
                    list:uidList.join()
                }

                ajaxRequest({url:url,data:data},function(){
                    self.dataList.splice(0, self.dataList.length);
                    for(var i in newList){
                        self.dataList.push(newList[i]);
                    }
                });
            },
            getTempList:function(){
                var list = [];
                for(var i in this.dataList){
                    list.push(rebuildVueData(this.params,this.dataList[i]));
                }
                return list;
            }
        },
        components:{
            pageCross:crossPage,
            checkbox:btnCheckBox,
            addDialog:addDialog
        }
    }
</script>