<template>
    <div v-show="isShow">
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
                            <div class="row">
                                <label for="anchorRecommendNick">昵称</label>
                                <input type="text" id="anchorRecommendNick" value="" v-model="nick">
                            </div>
                            <div class="row">
                                <input type="checkbox" id="to-index" value="false" v-model="toIndex">
                                <label for="to-index">是否推荐到首页</label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button id="modal-submit"  type="button" class="btn btn-primary yellow-crusta" @click="publish">确定</button>
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
            isShow:{
                type:Boolean,
                required:true
            }
        },
        data:function () {
            return{
                nick:'',
                toIndex:false
            }
        },
        methods:{
            publish:function () {
                var self = this;
                this.$dispatch('addAnchor',self.nick,self.toIndex,function(d){
                    self.close();
                });
            },
            close:function () {
                this.isShow = false;
                this.nick='';
                this.toIndex = false;
            }
        }
    }
</script>