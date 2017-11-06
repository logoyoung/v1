<template>
    <div v-if="isShow">
        <div class="modal-backdrop fade in" style="z-index:10050"></div>
        <div  class="modal fade" :class="isShow ? 'in' : ''" :style="isShow ? 'display:block;':''" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="" aria-hidden="true" @click="close"></button>
                        <h4 class="modal-title">发布</h4>
                    </div>
                    <div class="modal-body form">
                        <form action="#" class="form-horizontal form-row-seperated" style="padding: 20px 0px 20px 160px;">
                            <div v-if="pageView=='focus'" class="row">
                                <label>自定义推荐</label>
                                <div class="face-box" style="width:260px;height: 116px;">
                                    <img :src="posterUrl" alt="">
                                    <input type="file" id="poster-upload" v-model="fileURL">
                                </div>
                            </div>
                            <div class="row">
                                <label for="anchorRecommendNick">标题</label>
                                <input type="text" id="anchorRecommendNick" value="" v-model="title">
                            </div>
                            <div class="row">
                                <label for="to-index">链接</label>
                                <input type="text" id="to-index" value="" v-model="link">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button id="modal-submit"  type="button" class="btn btn-primary yellow-crusta" @click="submit">确定</button>
                        <button id="modal-close" type="button" class="btn btn-default" data-dismiss="" @click="close">取消</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default{
        props:{
            posterUrl:{
                type:String,
                default:''
            },
            title:{
                type:String,
                default:''
            },
            link:{
                type:String,
                default:''
            },
            type:{
                type:String,
                default:'create'
            },
            newsid:{
                type:Number,
                default:0
            },
            pageView:{
                type:String,
                default:'focus',
            },
            isShow:{
                type:Boolean,
                required:true
            }
        },
        data:function () {
            return {
                fileURL:'',
                backImgUrl:''
            }
        },
        computed:{
            changeImg:function(){
                this.posterURL = this.getFileURL();
                return this.fileURL;
            }
        },
        methods:{
            getFileURL:function(){
                var url;
                if (navigator.userAgent.indexOf("MSIE")>=1) { // IE
                    url = this.fileURL;
                } else if(navigator.userAgent.indexOf("Firefox")>0) { // Firefox
                    url = window.URL.createObjectURL(document.getElementById('poster-upload').files.item(0));
                } else if(navigator.userAgent.indexOf("Chrome")>0) { // Chrome
                    url = window.URL.createObjectURL(document.getElementById('poster-upload').files.item(0));
                }
                return url;
            },
            submit:function(){
                var self = this;
                if(this.type == 'create'){
                    this.$dispatch('create',this.viewPage,this.title,this.link,this.backImgUrl,function(){self.close();});
                }else{
                    this.$dispatch('modify',this.viewPage,this.newsid,this.title,this.link,this.backImgUrl,function(){self.close();});
                }

            },
            close:function () {
                this.isShow = false;
                this.newsid = 0;
                this.backImgUrl = this.fileURL = this.link = this.title = '';
            }
        }
    }
</script>