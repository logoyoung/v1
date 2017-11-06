<template>
    <div v-show="publishOptionShow">
        <div class="modal-backdrop fade in" style="z-index:10050"></div>
        <div  class="modal fade" :class="publishOptionShow ? 'in' : ''" :style="publishOptionShow ? 'display:block;':''" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="" aria-hidden="true" @click="close"></button>
                        <h4 class="modal-title">发布</h4>
                    </div>
                    <div class="modal-body form">
                        <form action="#" class="form-horizontal form-row-seperated" style="padding: 20px 0px 20px 160px;">
                            <div class="row">
                                <input type="radio" id="to-index-point" value="1" v-model="selectType">
                                <label for="to-index-point">推荐到首页焦点（需要有封面）</label>
                            </div>
                            <div class="row">
                                <input type="radio" id="to-index" value="2" v-model="selectType">
                                <label for="to-index">推荐到首页列表</label>
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
    export default {
        props:{
            newsid:{
                type:Number,
                required:true
            },
            publishOptionShow:{
                type:Boolean,
                required:true
            }
        },
        data:function () {
            return {
                selectType:2,
            }
        },
        methods:{
            publish:function () {
                var self = this;
                this.$dispatch('publish',self.newsid, self.selectType, function(d){
                    self.close();
                });
            },
            close:function () {
                this.publishOptionShow = false;
                this.selectType=2;
//                this.newsid=0;
            }
        }

    }
</script>