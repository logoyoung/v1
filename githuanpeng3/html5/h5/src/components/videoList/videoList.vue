<template>
  <div class="recommend-list">
    <ul class="live-list">
      <li class="live-one" v-for="(item,index) in videoList" :key=index>
        <a :href="'/mobile/room/room.html?luid='+item.uid" v-if="item.uid">
          <div class="poster">
            <div class="snapshot">
              <img :class="{horizontal:item.orientation === '1',vertical:item.orientation === '0'}" v-lazy="item.poster" data-type="live">
              <p class="game-title" v-if="hasGameTitle">{{item.gameName}}</p>
            </div>
            <div class="profile-photo">
              <img v-lazy="item.head" data-type="userface">
            </div>
            <div class="poster-info">
              <p class="poster-name">{{item.nick}}</p>
              <p class="audience" v-if='item.viewCount'>{{item.viewCount}}人</p>
            </div>
            <section class="room-name">{{item.title}}</section>
          </div>
        </a>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    videoList: {
      type: Array,
      default() {
        return [];
      }
    },
    hasGameTitle: {
      type: Boolean,
      default: true
    }
  },
  mounted() {
  },
  methods: {
    loadImage(image, e) {
      // const imageUrl = 'assets/image/lazyload-2.png';
      // console.log(image);
      // console.log(e.target);
      // e.target.src = imageUrl;
    },
    beforeload() {
      console.log('beforeload');
    }
  }
}
</script>

<style lang="less" scoped>
.live-list {
  display: flex;
  flex-wrap: wrap;
  /* fix bug on ios 8.x */
  display:-webkit-flex;
  -webkit-flex-wrap: wrap;
  .live-one {
    width: 46%;
    height: 294/75rem;
    margin: 0 2% 20/75rem;
    &:nth-child(2n) {
      margin: 0 0 20/75rem 2%;
    }
    a {
      display: block;
      width: 100%;
      height: 100%;
      border-radius: 10/75rem;
      position: relative;
      background-color: rgb(255, 255, 255);
    }
  }
}

.poster {
  width: 100%;
  height: 100%;
  .snapshot {
    position: relative;
    width: 100%;
    height: 194/75rem;
    overflow: hidden;
    border-top-left-radius: 10/75rem;
    border-top-right-radius: 10/75rem;

    .horizontal {
      // 横屏截图样式
      display: block;
      width: 100%;
      height: 100%;
      margin-top: 0;
    }
    .vertical {
      // 竖屏截图样式
      display: block;
      height: auto;
      width: 100%;
      margin-top: -20%;
    }
    .game-title {
      background-color: rgba(0, 0, 0, 0.3);
      font-size: 26/75rem;
      color: rgb(255, 150, 60);
      position: absolute;
      right: 0;
      bottom: 0;
      height: 40/75rem;
      line-height: 40/75rem;
      padding: 0 16/75rem;
    }
  }
  .profile-photo {
    img {
      width: 78/75rem;
      height: 78/75rem;
      border: 3/75rem solid #ffffff;
      border-radius: 50%;
      position: absolute;
      left: 8/75rem;
      bottom: 60/75rem;
    }
  }
  .room-name {
    width: 100%;
    padding: 5/75rem 16/75rem 0 16/75rem;
    font-size: 28/75rem;
    color: rgb(40, 40, 40);
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    clear: both;
  }
}

.poster-info {
  font-size: 24/75rem;
  margin-top: 12/75rem;
  .poster-name {
    width: 70%;
    height: 36/75rem;
    line-height: 36/75rem;
    float: left;
    text-indent: 100/75rem;
    color: rgb(86, 86, 86);
  }
  .audience {
    width: 30%;
    float: right;
    text-align: right;
    line-height: 36/75rem;
    padding-right: 16/75rem;
    box-sizing: border-box;
    color: rgb(146, 146, 146);
  }
  p {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }
}
</style>
