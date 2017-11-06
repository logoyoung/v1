<template>
  <transition leave-active-class="animated fadeOut">
    <div class="dialog" v-show="isShow">
      <transition name="dialogShow" enter-active-class="animated bounceIn" leave-active-class="animated bounceOut">
        <div class="dialog-container" v-show="isShow">
          <!-- <div class="dialog-img">
                          <img src="./dialog.png" alt="">
                        </div> -->
          <slot></slot>
          <div class="button-wrapper" v-if="dialogType === 'affirm'">
            <button class="btn btn-cancel" @click="hide">取消</button>
            <button class="btn btn-yes" @click="emitSubmit">确定</button>
          </div>
          <div class="button-wrapper" v-if="dialogType==='goLogin'">
            <button class="btn btn-cancel" @click="hide">再想想</button>
            <a class="btn btn-yes" href="/mobile/h5login/index.html">去登录</a>
          </div>
        </div>
      </transition>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'dialog',
  props: {
    dialogType: {
      type: String,
      default: 'affirm'
    }
  },
  data() {
    return {
      isShow: false
    }
  },
  methods: {
    show() {
      this.isShow = true;
    },
    hide() {
      this.isShow = false;
    },
    emitSubmit() {
      this.isShow = false;
      this.$emit('emitSubmit');
    }
  }
}
</script>

<style lang="less">
@import '~assets/css/animate';
@import '~assets/css/common';


.dialog {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0);
  display: flex;
  justify-content: center;
  align-items: center;
  /* fix bug on ios 8.x */
  display: -webkit-flex;
  -webkit-justify-content: center;
  -webkit-align-items: center;
}

.dialog-container {
  width: 90%;
  background: #fff;
  padding: 40/@myrem 0;
  border-radius: 6px;
  box-shadow: 0 0 40/@myrem #ccc;
  .dialog-img {
    text-align: center;
    img {
      width: 30%;
    }
  }
  p {
    font-size: 28/@myrem;
    padding-left: 40/@myrem;
    margin-bottom: 60/@myrem;
  }
}

.button-wrapper {
  text-align: right;
  font-size: 0;
  .btn {
    font-size: 28/@myrem;
    background: #fff;
    padding-top: 18/@myrem;
    padding-right: 40/@myrem;
    outline: 0 none;
    &.btn-yes {
      color: @hpColor;
    }
    &.btn-cancel {
      color: #666;
    }
  }
}
</style>