<template>
  <div id="app">
    <keep-alive>
    <router-view name="keepAliveRouter"></router-view>
    </keep-alive> 
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'app',
  created() {
    // this.init();
  },
  methods: {
    init() {

    }
  }
}
</script>


<style lang="less" scoped>
#app {
  height: 100%;
  width: 100%;
  div {
    height: 100%;
  }
}
</style>
