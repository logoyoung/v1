
<template>
	<div class="head-check-box">
		<div class="head-box-head">
			<i></i>
			<span>{{title}}</span>
		</div>
		<div class="head-box-body">
			<div class="user-head-table">
				<div class="user-head-one col-md-4" v-for="(index,item) in checkList" v-if="item" v-show="types==1?item.stat:!item.stat"  @click="checkHead(index)">
					<img alt="" class="user-head" :src="item.imgUrl">
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {

		props: {
			types: {
				type: Number,
				default: 1
			},
			title: {
				type: String,
				default: '通过'
			},
			checkList:{
				type:[Array, Object],
				twoWay:true,
				default:function () {
					return {};
				}
			}
		},
		methods:{
			checkHead:function (index) {
				if (this.checkList[index]) {
					var stat = this.checkList[index].stat;
					var imgUrl = this.checkList[index].imgUrl;
					var uid = this.checkList[index].uid;
					this.checkList.$set(index, {uid:uid,stat:!stat,imgUrl:imgUrl});
				}
			}
		}
	}
</script>