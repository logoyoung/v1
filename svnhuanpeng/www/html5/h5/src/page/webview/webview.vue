<template>
  <div>
    <v-header>
      <p slot="title" class="back-home">
        <router-link to="/home/Hot">返回首页</router-link>
      </p>
      <p slot="title" class="title">{{title}}</p>
    </v-header>
    <iframe name="ifr" id="ifr" class="iframe" :src="webUrl" frameborder="0"></iframe>
  </div>
</template>

<script>
  import vHeader from '@/components/header/header';

  export default {
    name: 'webview',
    components: { vHeader},
    data (){
      return {
        title: this.$route.query.title
      };
    },
    computed: {
        webUrl(){
          if(this.$route.query.showType == 2){
            return this.$route.query.url;
          }else if(this.$route.query.showType == 3 || this.$route.query.showType == 1){
            location.href = this.$route.query.url;
          }else{
            location.href = this.$route.query.url;
          }
        }
    },
    created() {
        console.log(this.$route.query);
    }

  }
</script>

<style lang="less" scoped>
  body{
    position: relative;
    height:100%;
  }
  .header{
    z-index: 999;

    .back-home{
      position:absolute;
      font-size:0;
      left:32/75rem;
      width: 56/75rem;
      height: 56/75rem;
      top: 16/75rem;
      z-index: 9999;
      display: block;
      a{
        display: block;
        height: 100%;
        background: url("../../assets/image/icon_back.png") no-repeat;
        background-size:contain;
        &:active{
          background: url("../../assets/image/icon_back_h.png") no-repeat;
          background-size:contain;
        }
      }
    }
  }
  .iframe{
    position: fixed;
    top:88/75rem;
    left:0;
    margin: 0;
    padding: 0;
    border: 0;
    box-sizing: border-box;
    width: 100%;
    height: 100%;
    min-height: 90vh;
  }
</style>
