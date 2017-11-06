<template>
  <div class="swiper-container" ref="swiperContainer">
    <slot></slot>
    <!-- 如果需要分页器 -->
    <div class="swiper-pagination swiper-pagination-white" ref="swiperPagination"></div>
  </div>
</template>

<script>
  export default {
  	name: 'vSwiper',
  	mounted() {
      this.init();
  	},
  	methods: {
  	  init() {
        this.$nextTick(()=> {
          this.swiper = new Swiper(this.$refs.swiperContainer, {
            loop: true,
            pagination: this.$refs.swiperPagination,
            autoplay: 3000,
            preventClicks: false
          });
        })
  	  }
  	}
  }
</script>

<style lang="less">
  .swiper-container{
    width: 100%;
    height: 192/75rem;
    margin-bottom:20/75rem;
    .swiper-pagination-bullet {
      width: 12/75rem;
      height: 12/75rem;
      margin: 0 6/75rem;
    }
    .web-link,img{
      width: 100%;
      height: 100%;
      display: block;
    }
  }
</style>
