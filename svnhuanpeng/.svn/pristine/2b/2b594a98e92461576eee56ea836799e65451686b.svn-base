<template>
  <div class="loadmore" @touchstart="touchStart($event)" @touchmove="touchMove($event)" @touchend="touchEnd($event)">
    <slot name="list"></slot>
    <div class="bottom-area">
      <slot name="bottom"></slot>
    </div>
  </div>
</template>

<script type="es6">
export default {
  props: {
    pullup: {
      // pull up and load more
      type: Function,
      default: undefined,
      required: false
    },
    // dropdown: {
    //   // drop down and refresh
    //   type: Function,
    //   default: undefined,
    //   required: false
    // },
    touchEvent: {
      type: Boolean,
      default: true
    },
  },
  data: function() {
    return {
      startY: 0,
      endY: 0,
      currentY: 0,
      scrollTop: 0,
      offsetHeight: 0,
      elHeight: 0,
      diff: 0
    }
  },
  computed: {
  },
  methods: {
    touchStart(e) {
      this.startY = this.currentY = e.touches.item(0).pageY;
      this.offsetHeight = this.$el.offsetHeight;// 可视高度
      this.elHeight = this.$el.scrollHeight;// 总高度
    },
    touchMove(e) {

    },
    touchEnd(e) {
      this.endY = e.changedTouches.item(0).pageY;
      let offset = Math.floor(this.endY - this.startY);
      if (offset <= -88 / 75 * win.rem && this.scrollTop + this.offsetHeight >= this.elHeight) {
        // 上拉加载
        if (this.touchEvent === true) {
          this.pullup();
        }
      }
    },
  },
  components: {}
}
</script>

<style lang="less" scoped>
@import '~assets/css/animate';
@myrem: 75rem;
.loadmore {
  width: 100%;
  height: 100%;
  min-height: 92vh;
  box-sizing: border-box;
}

p {
  width: 100%;
  font-size: 30/@myrem;
}

.bottom-area {
  width: 100%;
  height: 40/@myrem;
}
</style>
