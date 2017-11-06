<template>
  <div class="search">
  	<v-header>
  	  <a slot="main-nav" class="go-backbtn" @click="goBack"></a>
      <div slot="search-input" class="search-input">
        <input  type="text" placeholder="搜索主播，直播，视频" ref="searchInput" autofocus v-model="keywordModel">
      </div>
  	  
  	  <a slot="search" class="search-btn no-bg" @click="handleSearch">
        搜索
  	  </a>
  	</v-header>
  	<section class="wrapper anchor" v-if="allCount&&anchorList.length>0">
  	  <h2>主播</h2>
	  <v-slider :data="anchorList" :keyword="keyword">
	  	
	  </v-slider>
  	</section>
  	<section class="wrapper live" v-if="allCount&&liveList.length>0">
  	  <h2>直播</h2>
  	  <video-list-row :data="liveList" :keyword="keyword" :count="liveCount" type="live"></video-list-row>
  	</section>
  	<section class="wrapper video" v-if="allCount&&videoList.length>0">
  	  <h2>视频</h2>
  	  <video-list-row :data="videoList" :keyword="keyword" :count="videoCount" type="video"></video-list-row>
  	</section>
  	<div class="no-content" v-if="allCount<1">
  		<img src="../../assets/image/follow_page_noLive.png" alt="">
  		<p>暂无搜索结果</p>
  	</div>
    <main-nav></main-nav>
  </div>
</template>

<script>
import vHeader from 'components/header/header';
import vSlider from 'components/swiper/slider';
import videoListRow from 'components/videoList/videolist-row';
import mainNav from 'components/nav/main-nav';
import { mapGetters } from 'vuex';
import { homeSearch } from 'api/search';
import { filterColor } from 'assets/js/common';

export default {
  name: 'search',
  components: { vHeader,vSlider,videoListRow,mainNav },
  data() {
  	return {
  	  client: '1',
      keywordModel: '',
  	  keyword: '',

  	  anchorList: [],
  	  liveList: [],
  	  videoList: [],
  	  liveCount: 0,
  	  videoCount: 0,
  	  allCount: 1,

      num: 0
  	}
  },
  computed: {
  	...mapGetters([
  		'uid',
  		'encpass'
  	])
  },
  mounted() {
  	this.searchFocus();
  },
  activated() {
    if(this.$route.query.init === true) {
      this.searchFocus();
      this.anchorList = [];
      this.liveList = [];
      this.videoList = [];
    }
  },
  methods: {
  	goBack() {

  	  this.$router.go(-1);
  	},
  	handleSearch() {
  	  const self = this;
      self.keyword = this.$refs.searchInput.value;
      self.num++;
  	  const option = {
  	  	uid: self.uid,
  	  	encpass: self.encpass,
  	  	client: self.client,
  	  	keyword: self.keyword
  	  };
  	  homeSearch(option).then((res)=> {
  	  	// console.log(res);
  	  	self.allCount = 0;
  	  	if(res.status === '1') {
  	  	  
  	  	  self.anchorList = res.content.anchorList;
  	  	  self.liveList = res.content.liveList;
  	  	  self.videoList = res.content.videoList;
  	  	  self.liveCount = parseInt(res.content.liveCount,10);
  	  	  self.videoCount = parseInt(res.content.videoCount,10);
  	  	  self.allCount = parseInt(res.content.allCount,10);

  	  	}
        self.$route.query.init = false;
        
  	  })
  	},
  	toLiveRoom(uid) {
  	  const url = '/mobile/room/room.html?luid=' + uid;
  	  location.href = url;
  	},
    searchFocus() {
      this.$nextTick(()=> {
        this.keywordModel = '';
        this.$refs.searchInput.focus();
      })
    },
  	filterColor
  }
}
</script>

<style lang="less">
@import "~assets/css/common";

.search {
	padding: 88/@myrem 15/@myrem 0;
  .search-input {
    width: 70%;
    height: 70%;
    position: relative;
    border-bottom: 1px solid #e5e5e5;
    input {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: 0 none;
      outline: 0 none;
      font-size: 30/@myrem;
      color: #282828;
    }
  }
	h2 {
		font-size: 28/@myrem;
		margin: 16/@myrem 0;
    color: #282828;
	}
	.wrapper {
		padding-top: 20/@myrem;
	}
	
	.search-key {
		color: @hpColor;
	}
	.no-content {
		text-align: center;
		padding-top: 300/@myrem;
		img {
			width: 200/@myrem;
		}
		p {
			font-size: 30/@myrem;
			color: #666;
		}
	}
}

</style>