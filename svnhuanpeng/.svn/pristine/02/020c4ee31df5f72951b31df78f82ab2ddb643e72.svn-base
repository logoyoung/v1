<template>
    <div class="input-checkbox-div">
        <label for="" class="input-checkbox" :class="isChecked ? 'checked' : ''" @click="check"></label>
        <slot></slot>
    </div>
</template>

<script>
    export default{
        props:['isChecked'],
        methods:{
            check:function(){
                this.isChecked = !this.isChecked;
            }
        }
    }
</script>