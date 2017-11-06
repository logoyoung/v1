<template>
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th class="checkbox-th"></th>
            <th>推荐</th>
            <th v-if="viewPage =='focus'">图片</th>
            <th v-else>图片</th>
            <th>标题</th>
            <th>链接</th>
            <th>推荐</th>
            <th>编辑</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(index,item) in dataList" @click="mark(index)">
            <td><checkbox :is-checked="item.checked"></checkbox></td>
            <td v-if="index==0"><button class="btn" @click.stop="cancelTop(index)">取消置顶</button></td>
            <td v-else><button class="btn" @click.stop="toTop(index)">置顶</button></td>
            <td v-if="viewPage =='focus'"><img :src="item.poster"/></td>
            <td v-else>{{item.tname}}</td>
            <td>{{item.title}}</td>
            <td>{{item.link}}</td>
            <td><button class="btn" @click.stop="cancelPublish(index)">取消推荐</button></td>
            <td><button class="btn" @click.stop="modifyItem(index)">编辑</button></td>
        </tr>
        </tbody>
    </table>
    <nav class="handle-option">
        <div class="col-md-3">
            <button class="btn btn-circle" @click="allSelect">全选</button>
            <button class="btn btn-circle" @click="inverseSelect">反选</button>
        </div>
        <div class="col-md-6" style="text-align: center"></div>
        <div class="col-md-3">
            <button class="recheck btn btn-circle pull-right" @click="deleteItem">删除</button>
        </div>
    </nav>
    <div v-show="false">{{todoSearch }}</div>
    <div v-show="false">{{ changeView }}</div>
    <div v-show="false">{{ noSearch}}</div>
    <add-dialog :is-show.sync="isShow" :type.sync="dialogOptType" :page-view="viewPage" :title="dialogTitle" :link="dialogLink" :newsid="dialogNewid" :poster-url="dialogPosterURL"></add-dialog>
</template>

<script>
    import  btnCheckBox from '../../components/inputCheckBox.vue';
    import addDialog from './dialog.vue';

    export default{
        props:{
//            sTime:{
//                type:String
//            },
//            eTime:{
//                type:String
//            },
            viewPage:{
                type:String,
                require:true
            },
//            keyword:{
//                type:String
//            },
//            goSearch:{
//                type:Boolean,
//                required:true
//            },
            isShow:{
                typ:Boolean,
                required:true
            },
            dialogOptType:{
                type:String,
                default:'create'
            }
        },
        data:function () {
            return {
                dataList:[],
                params:['checked','id','tname','title','poster','url','type'],
                currentView:'focus',
                dialogTitle:'',
                dialogLink:'',
                dialogNewid:0,
                dialogPosterURL:''
            }
        },
        ready:function(){
            this.requestList(1);
        },
        methods:{
            requestList:function(page){
                var self = this;
                var doCallBack = typeof arguments[1] == 'function'? arguments[1] : function(){};
                var url = 'http://dev.huanpeng.com/admin2/api/information/info/recommendList.php';

                this.currentKeyWord = this.keyword;
                this.currentView = this.viewPage;
                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie("admin_enc"),
                    type:1,
                    id:this.itype
                }

                ajaxRequest({url:url,data:data},function(d){
                    self.dataList.splice(0, self.dataList.length);
                    self.totalCount = d.total
                    var list = d.list;
                    for(var i in list){
                        var data = {
                            checked:false,
                            id:list[i].id,
                            tname:list[i].tname,
                            title:list[i].title,
                            poster:list[i].poster,
                            url:list[i].url,
                            type:list[i].type
                        }
                        self.dataList.push(data);
                    }
                    self.totalCounts = parseInt(d.total);
                    doCallBack();
                });
            },
            modifyItem:function(index){
                if(this.dataList[index].type==0){
                    location.href = "http://dev.huanpeng.com/admin2/view/newsCreate.php?sidebar=8-0&nid="+this.dataList[index].id;
                }else{
                    this.dialogOptType='modify';
                    this.dialogTitle = this.dataList[index].title;
                    this.dialogNewid = Number(this.dataList[index].id);
                    this.dialogLink = this.dataList[index].url;
                    this.dialogPosterURL = this.dataList[index].poster;
                    this.isShow = true;
                }
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
                console.log(arr);
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
            cancelPublish:function(index){
                var id = this.dataList[index].id;

                this.cancelPublishRequest(id);

            },
            cancelPublishRequest:function(id){
                if(!id) return;
                var self = this;
                var url = 'http://dev.huanpeng.com/admin2/api/information/info/changeInformation.php';
                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie('admin_enc'),
                    type:getCookie('admin_type'),
                    status:3,
                    id:id,

                };
                ajaxRequest({url:url, data:data}, function () {
                    self.requestList();
                });
            },
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
                var idList = [];

                delete list[index];

                newList.push(newTop);
                for(var i in list){
                    if(list[i])
                        newList.push(list[i]);
                }
                for(var i in newList){
                    idList.push(newList[i].id);
                }

                var url = 'http://dev.huanpeng.com/admin2/api/information/info/upOrCancel.php';
                var data = {
                    uid:getCookie('admin_uid'),
                    encpass:getCookie('admin_enc'),
                    type:getCookie('admin_type'),
                    itype:this.itype,
                    list:idList.join()
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
        computed:{
//            todoSearch:function () {
//                var self = this;
//                if(this.goSearch){
//                    this.requestList('',function(){
//
//                    });
//                    self.goSearch = false;
//                }
//                return this.goSearch;
//            },
//            noSearch:function () {
//                if(this.currentKeyWord != this.keyword && this.keyword==''){
//                    console.log('no search event run');
//                    this.requestList();
//                }
//                return this.keyword;
//            },
            changeView:function () {
                if(this.viewPage != this.currentView){
                    this.requestList(1);
                }
                return this.viewPage;
            },
            itype:function(){
                if(this.viewPage == 'focus'){
                    return 1;
                }else{
                    return 2;
                }
            }
        },
        events:{
            create:function(view,title,link,backImgURL,callback){

            },
            modify:function(view,newsid,title,link,backImgURL,callback){

            }
        },
        components:{
            checkbox:btnCheckBox,
            addDialog:addDialog
        }
    }
</script>