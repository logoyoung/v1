<template>
  <header class="header fast-click">
    <slot name="main-nav"></slot>
    <div class="title-container">
      <slot name="title"></slot>
    </div>
    <slot name="search"></slot>
  </header>
</template>

<script>
import fastclick from 'fastclick';
fastclick.attach(document.body, 'fast-click');

export default {
  name: 'header',
  props: {
    // title: {}
  }
}
</script>

<style lang="less">
@myrem: 75rem;
.header {
  position: fixed;
  width: 100%;
  height: 88/@myrem;
  background: #ffffff;
  left: 0;
  top: 0;
  z-index: 99;
  box-shadow: 0 2.5/@myrem 2/@myrem 1/@myrem rgba(0, 0, 0, 0.2);
  .title-container {
    height: 100%;
    display: flex;
    align-items: center;
    /* fix bug on ios 8.x */
    display: -webkit-flex;
    -webkit-align-items:center;
    .title {
      font-size: 40/@myrem;
      color: rgb(40, 40, 40);
      width: 100%;
      text-align: center;
    }
  }
}

.main-navbtn {
  width: 56/@myrem;
  height: 56/@myrem;
  position: absolute;
  left: 32/@myrem;
  top: 16/@myrem;
  background: url(../../assets/image/icon_menu.png) no-repeat;
  background-size: 56/@myrem;
}

.main-nav:active {
  background: url(../../assets/image/icon_menu_h.png) no-repeat;
}
</style>
