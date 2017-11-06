<template>
    <nav style="text-align: center;" v-if="elementId" id="page-{{elementId}}">
        <ul class="pageCross pagination"></ul>
    </nav>
    <div v-show="false">{{pageChange}}</div>
</template>


<script>
    export default{
        props:{
            elementId:{
                type:String,
            },
            pageSize:{
                type:Number,
                default:10
            },
            totalCounts: Number,
            currentPage:Number,
            visiblePages:{
                type:Number,
                default:6
            }

        },
        computed:{
            pageChange:function(){
                var self = this;
                $('#page-'+self.elementId + ' .pageCross').jqPaginator('option',{
                    totalCounts:self.totalCounts ? self.totalCounts : 1
                });
                return this.totalCounts;
            }
        },
        ready:function(){
            var self = this;
            var readyLoading = true;
            $.jqPaginator('#page-'+self.elementId + ' .pageCross',{
                totalCounts:this.totalCounts,
                pageSize:this.pageSize,
                visiblePages:this.visiblePages,
                currentPage:this.currentPage,
                onPageChange:function(num, type){
                    console.log('on change ' + num);
                    if(readyLoading){
                        readyLoading = false;
                        return;
                    }
                    self.$dispatch('pageChange',num,function(){
                        $('#page-'+self.elementId + ' .pageCross').jqPaginator('option',{
                            totalCounts:self.totalCounts
                        });
                    });
                }
            });
        }
    }
</script>