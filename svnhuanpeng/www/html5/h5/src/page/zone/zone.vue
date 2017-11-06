<template>
	<div>
		<v-header>
			<a slot="main-nav" class="go-backbtn" @click="goBack"></a>
			<p slot="title" class="title">{{ gameName }}</p>
		</v-header>
		<div class="zone-container" id="scroller" @touchstart="touchStart($event)" @touchmove="touchMove($event)">
			<video-list :videoList="gameList" :hasGameTitle="hasGameTitle">
			</video-list>
			<div class="pagebtn-box" v-bind:class="{ 'status':isShow,'status':isHide }">
				<!--<button @click="nextPage" class="next-page" :disabled="disabledBtn">{{ message }}</button>-->
				<p class="next-page">{{ message }}</p>
			</div>
		</div>
		<main-nav ref="mainNav"></main-nav>
	</div>
</template>

<script>
import vHeader from '@/components/header/header';
import mainNav from '@/components/nav/main-nav';
import videoList from '@/components/videoList/videoList';
import qs from 'qs';

export default {
	name: 'zone',
	components: { vHeader, mainNav, videoList },
	data() {
		return {
			gameList: [],
			gameName: '',
			hasGameTitle: false,
			page: 2,
			message: "",
			disabledBtn: false,
			isShow: false,
			isHide: true,
		}
	},
	mounted() {
		this.init();
		// this.loadMore();
	},
	methods: {
		init() {
			const self = this;
			self.gameName = self.$route.query.gameName;
			this.$ajax.post('/api/app/gameInfoForApp.php', qs.stringify({
				'gameID': self.$route.query.id,
				'type': '1'
			})).then((res) => {
				self.gameList = res.data.content.liveList;
				// console.log('gameList', self.gameList);
				if (self.gameList.length <= 6) {
					self.isShow = false;
					self.isHide = true;
				};
			});
		},
		nextPage(gameList) {
			var i = this.page++;
			const self = this;
			self.message = "数据加载中...";
			this.$ajax.post('/api/app/gameInfoForApp.php', qs.stringify({
				'gameID': self.$route.query.id,
				'type': '1',
				'page': i
			})).then((res) => {
				self.ListAdd = res.data.content.liveList;
				var liveTotal = res.data.content.total;
				var pageTotal = Math.ceil(liveTotal / 8);
				var gameListAdd = self.ListAdd;
				console.log(gameListAdd.length);
				if (gameListAdd.length != 0) {
					for (var j = 0; j < gameListAdd.length; j++) {
						if (i <= pageTotal) {
							self.gameList.push(gameListAdd[j]);
						}
					}
				}
				else {
					self.message = "没有更多了！";
				}
			});
		},
		// loadMore() {
		// 	const self = this;
		// 	//获取滚动条当前的位置 
		// 	function getScrollTop() {
		// 		return document.querySelector('#scroller').scrollTop;
		// 	};
		// 	//获取文档完整的高度 
		// 	function getScrollHeight() {
		// 		return (document.querySelector('#scroller').clientHeight) / 2;
		// 	};
		// 	document.querySelector('#scroller').onscroll = function() {
		// 		//	        	console.log(getScrollTop());
		// 		if (getScrollTop() + 1500 >= getScrollHeight()) {
		// 			function nextPage(gameList) {
		// 				var i = self.page++;
		// 				var flag = false;
		// 				self.$ajax.post('/api/app/gameInfoForApp.php', qs.stringify({
		// 					'gameID': self.$route.query.id,
		// 					'type': '1',
		// 					'page': i
		// 				})).then((res) => {
		// 					self.ListAdd = res.data.content.liveList;
		// 					var liveTotal = res.data.content.total;
		// 					var pageTotal = Math.ceil(liveTotal / 8);
		// 					var gameListAdd = self.ListAdd;
		// 					if (gameListAdd.length != 0) {
		// 						for (var j = 0; j < gameListAdd.length; j++) {
		// 							if (i <= pageTotal) {
		// 								self.message = "数据加载中...";
		// 								self.gameList.push(gameListAdd[j]);
		// 								flag = false;
		// 							} else {
		// 								flag = true;
		// 							};
		// 						};
		// 					} else {
		// 						flag = true;
		// 					};
		// 					if (flag) {
		// 						self.message = "没有更多了！";
		// 					};
		// 				});
		// 			};
		// 			nextPage();
		// 		};
		// 	};
		// },
		goBack() {
			this.$router.go(-1);
		},
		touchStart(e) {

		},
		touchMove(e) {
			let bodyClientHeight = document.body.clientHeight;
			let bodyScrollTop = document.body.scrollTop || document.documentElement.scrollTop;
			let bodyHeight = document.body.scrollHeight || document.documentElement.scrollHeight;
			console.log(bodyHeight);
			if (bodyClientHeight + bodyScrollTop >= bodyHeight + 150) {
				this.nextPage();
			}
		}
	}
}

</script>

<style lang="less" scoped>
@myrem :75rem;
.go-backbtn {
	width: 56/@myrem;
	height: 56/@myrem;
	position: absolute;
	left: 32/@myrem;
	top: 16/@myrem;
	background: url(../../assets/image/icon_back.png) no-repeat;
	background-size: 56/@myrem;
}

.go-backbtn:active {
	background: url(../../assets/image/icon_back_h.png) no-repeat;
	background-size: 56/@myrem;
}

.zone-container {
	width: 100%;
	/*margin: 92/@myrem auto 0;*/
	padding-top: 92/@myrem;
	height: 100%;
	overflow: hidden;
	overflow-y: scroll;
	.recommend-list {
		padding-top: 20/@myrem;
	}
	.pagebtn-box {
		text-align: center;
		margin: 40/@myrem auto;
		padding-bottom: 40/@myrem;
		.next-page {
			background: none;
			font-size: 28/@myrem;
		}
	}
	.status {
		display: block;
	}
}
</style>