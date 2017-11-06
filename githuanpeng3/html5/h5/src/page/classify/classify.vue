<template>
	<div>
		<v-header>
			<a slot="main-nav" class="main-navbtn" @click="showMainNav"></a>
			<p slot="title" class="title">分类</p>
		</v-header>

		<div class="classify-container">
			<ul class="game-classify">
				<li v-for="gameclass in Games">
					<a @click="clickTo(gameclass.gameID,gameclass.gameName)" class="game-classifybtn">
						<div class="game-poster">
							<img v-lazy="gameclass.poster" data-type="classify" />
						</div>
						<p class="game-name">{{ gameclass.gameName }}</p>
						<p class="live-number">
							<span id="live-num">{{ gameclass.liveTotal }}</span>个直播</p>
					</a>
				</li>
			</ul>
		</div>

		<main-nav ref="mainNav"></main-nav>
		<download-tip></download-tip>
	</div>
</template>

<script>
import vHeader from '@/components/header/header';
import mainNav from '@/components/nav/main-nav';
import downloadTip from '@/components/downloadTip/download-tip';
export default {
	name: 'classify',
	components: { mainNav, vHeader, downloadTip },
	data() {
		return {
			Games: {}
		}
	},
	mounted() {
		this.init();
	},
	methods: {
		init() {
			this.$ajax.post('/api/app/gameClassifyForApp.php').then(response => {
				//	  		console.log(response);
				this.Games = response.data.content.list;

			});
		},
		// 展示用户导航
		showMainNav() {
			this.$refs.mainNav.show();
		},
		clickTo(id, gameName) {
			this.gameName = '';
			this.$router.push({ path: 'zone', query: { id: id, gameName: gameName } });
			window.scrollTo(0, 0);
		},
	}
}
</script>


<style lang="less" scoped>
@myrem :75rem;
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

.classify-container {
	width: 100%;
	height: 100%;
	padding-top: 92/@myrem;
	overflow-y: scroll;
	.game-classify {
		height: auto;
		margin: 0 9/@myrem;
		li {
			width: 31%;
			display: inline-block;
			margin: 1%;
			background: #fff;
			text-align: center;
			border-radius: 8/@myrem;
			.game-classifybtn {
				width: 100%;
				display: inline-block;
				.game-poster {
					text-align: center;
					width: 100%;
					img {
						width: 100%;
						height: 306/@myrem;
						border-top-left-radius: 8/@myrem;
						border-top-right-radius: 8/@myrem;
					}
				}
				.game-name {
					padding: 15/@myrem auto;
					font-size: 28/@myrem;
					color: #282828;
					padding-bottom: 10/@myrem;
				}
				.live-number {
					font-size: 20/@myrem;
					color: #919191;
					padding-bottom: 16/@myrem;
				}
			}
		}
	}
}
</style>
