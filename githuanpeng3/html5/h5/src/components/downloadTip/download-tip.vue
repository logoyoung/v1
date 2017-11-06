<template>
    <footer class="download-tip" v-if="showFooter" @click="jumpToDownloadPage()">
        <button class="close-btn" @click="hide($event)">
            <img class="close-img" src="../../assets/image/icon_close.png">
        </button>
        <div class="logo">
            <img class="logo-img" src="../../assets/image/logo.png">
        </div>
        <div class="info">
            <p class="font-big">下载欢朋APP</p>
            <p class="font-small">手游直播、约玩下单，尽在欢朋！</p>
        </div>
    </footer>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex';

export default {
    computed: {
        ...mapGetters([
            'showFooter'
        ])
    },
    methods: {
        ...mapMutations({
            hideFooter: 'HIDE_FOOTER'
        }),
        jumpToDownloadPage() {
            window.location.href = "http://a.app.qq.com/o/simple.jsp?pkgname=com.mizhi.huanpeng";
        },
        hide(e) {
            e.stopPropagation();
            this.hideFooter();
        }
    },
    mounted() {
        if (window.huanpengShare || window.appAndroid || window.appCloseWebPage || window.appLogin || window.appSendCommand) {
            // 未在浏览器打开时，隐藏footer
            this.hideFooter();
        }
    }
}
</script>

<style lang="less" scoped>
@myrem : 75rem;
.download-tip {
    height: 96/@myrem;
    position: fixed;
    left: 20/@myrem;
    right: 20/@myrem;
    bottom: 1%;
    border-radius: 48/@myrem;
    background: -webkit-linear-gradient(left, #ffaf61, #ff7800);
    display: flex;
    justify-content: flex-start;
    align-items: center;
}

.close-btn {
    width: 36/@myrem;
    height: 36/@myrem;
    padding: 0;
    margin-left: 30/@myrem;
    background-color: transparent;
    .close-img {
        width: 100%;
        height: 100%;
    }
}

.logo {
    width: 76/@myrem;
    height: 76/@myrem;
    margin: 0 32/@myrem;
    border-radius: 20/@myrem;
    background-color: rgb(255, 255, 255);
    display: flex;
    justify-content: center;
    align-items: center;
    .logo-img {
        width: 56/@myrem;
        height: 56/@myrem;
    }
}

.info {
    height: 100%;
    display: flex;
    flex: 1;
    flex-direction: column;
    justify-content: center;
    color: rgb(255, 255, 255);
    .font-big {
        font-size: 36/@myrem;
        padding-bottom: 6/@myrem;
    }
    .font-small {
        font-size: 28/@myrem;
        padding-top: 6/@myrem;
    }
}
</style>
