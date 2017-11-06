<template>
  <div class="videolist-row">
  	<ul>
  		<li v-for="item in data" class="clearfix" @click="toLiveRoom(item.uid)">
  		  <div class="poster left">
  		  	<img v-lazy="item.poster" data-type="live">
  		  </div>
  		  <div class="video-item">
  		  	<h3 class="title" v-html="filterColor(item.nick)"></h3>
  		  	<p class="nick">{{item.nick}}</p>
  		  	<p class="info clearfix">
  		  		<span class="left">
  		  		  <!-- <i class="fa fa-gamepad"></i> -->
  		  		  <img src="../../assets/image/Search_icon_game@2x.png" alt="">
  		  		{{item.gameName}}
  		  		</span>
  		  		<span class="right">
				  <!-- <i class="fa fa-user" v-if="type=='live'"></i>
				  <i class="fa fa-eye" v-if="type=='video'"></i> -->
				  <img src="../../assets/image/Search_icon_viewerNumber@2x.png" v-if="type==='live'">
				  <img src="../../assets/image/Search_icon_viewNumber@2x.png" v-if="type==='video'">
  		  		  {{item.fansCount || item.viewCount}}
  		  		</span>
  		  	</p>
  		  </div>
  		</li>
  	</ul>
  	<div class="more" v-if="count>4">
  		<a @click="checkMore(type,keyword)">查看更多</a>
  	</div>
  </div>
</template>

<script>
import { filterColor } from 'assets/js/common';

export default {
  props: {
  	data: {},
  	keyword: {},
  	count: {},
  	type: {}
  },
  methods: {
	filterColor,
  	toLiveRoom(uid) {
  		const url = '/mobile/room/room.html?luid=' + uid;
  	    location.href = url; 
  	},
  	checkMore(type,keyword) {
  	  this.$router.push({
  	  	path: '/searchmore',
  	  	query: {type,keyword}
  	  })
  	}
  }
}
</script>

<style lang="less">
@import "~assets/css/common";

.videolist-row {
	li {
		border-bottom: 1px solid #ddd;
		margin-bottom: 16/@myrem;
		&:last-child {
			border-bottom: 0 none;
		}
	}
	.poster {
		margin-right: 16/@myrem;
		img {
			width: 256/@myrem;
			height: 192/@myrem;
		}
	}
	.video-item {
		position: relative;
		height: 192/@myrem;
		overflow: hidden;
		.title {
			font-size: 26/@myrem;
			padding-top: 6/@myrem;
			color: #282828;
		}
		.nick {
			font-size: 24/@myrem;
			color: #787878;
			margin: 20/@myrem 0;
		}
		.info {
			position: absolute;
			width: 100%;
			left: 0;
			bottom: 0;
			font-size: 20/@myrem;
			color: #929292;
			i {
				font-size: 32/@myrem;
			}
			img {
				vertical-align: middle;
				margin-top: -4/@myrem;
				margin-right: 8/@myrem;
				width: 32/@myrem;
				height: 32/@myrem;
			}
			.right img {
				margin-right: 0;
			}
		}
	}
	.more {
		margin: 15/@myrem;
		text-align: center;
		font-size: 0;
		a {
			display: inline-block;
			border: 1px solid #929292;
			width: 240/@myrem;
			height: 66/@myrem;
			line-height: 66/@myrem;
			color: #929292;
			font-size: 20/@myrem;
			border-radius: 66/@myrem;
		}
	}
}
</style>