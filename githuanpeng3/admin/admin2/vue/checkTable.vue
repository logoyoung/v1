<template>
<div id="{{elementId}}" class="tabbable">
    <slot name="tab"></slot>
    <slot name="content"></slot>
</div>
</template>

<script>
    export default{
        props:{
            elementId:{
                type:String,
            },
            currentView:{
                type:String,
                required:true
            }
        }
    }
</script>