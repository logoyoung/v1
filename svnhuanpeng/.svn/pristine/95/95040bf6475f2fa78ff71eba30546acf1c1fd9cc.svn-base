<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>欢朋直播-精彩手游直播</title>
    {config_load file="hpTPL.conf" section="setup"}
    {#commonMETASource#}
    {#commonCSSSource#}
    <link rel="stylesheet" href="{#hpCSS#}toTop.css{#hpVersion#}">
    <link rel="stylesheet" href="{#hpCSS#}index_v4.css{#hpVersion#}">
    <link rel="stylesheet" href="{#hpCSS#}swiper-3.4.2.min.css{#hpVersion#}">
    <script>
      	 var $head = {$header};
    </script>
    {#commonJSSource#}
    <script>
        if (navigator.userAgent.toLowerCase().match(/(iphone|ipod|android|ios|ipad|ucweb)/i))  {
            location.href = '../mobile';
        }
    </script>
    <script type="text/javascript" src="{#hpJS#}swiper-3.4.2.min.js{#hpVersion#}"></script>
    <script type="text/javascript" src="{#hpJS#}index_v4.js?v=1.0.7"></script>
</head>
<body>
{include file="header.tpl"}
<div class="playBox trans">
    <div class="play-content trans"  style="visibility: hidden;">
        <div class="player trans">
            <div class="flashBox trans">
                <div id="rtmpplayer">
                    <div id="install-flash" style="display: none;">
                        <a href="https://get.adobe.com/cn/flashplayer/" style="color: #FFF;">安装或者启用FLASH播放器</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="player-nav trans">
            <span class="pre-btn trans"></span>
            <span class="next-btn trans"></span>
            <div class="mt10 trans">
                <ul class="recommendBox trans">
                    {foreach $content.content.getStreamList as $item}
                        {* {if $item@iteration == 1}
                            <li data-uid={$item.uid}
                                data-stream={$item.stream}
                                data-streamList={$item.streamlist}
                                data-roomID="{$item.roomID}">
                                <div class="img-nav">
                                    {if $item.orientation == 0}
                                        <img class="angle_class" src="{$item.poster}">
                                        {else}
                                        <img src="{$item.poster}">
                                    {/if}

                                </div>
                            </li>
                        {else} *}
                            <li data-uid={$item.uid} data-roomID={$item.roomID}>
                                <div class="img-nav">

                                    <img src="{$item.poster}"/>

                                </div>
                            </li>
                        {* {/if} *}

                    {/foreach}
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="content trans of">
    <div class="fav-box of">
        <div class="likeBox trans l">
            <div class="likeBox-title">
                <i class="icon-like"></i>
                <span class="box-title l">猜你喜欢</span>
                <span class="box-titlesmall l">一定有你想看的</span>
                <a href="javascript:;" class="change-btn of r"><i class="icon-change trans"></i><span>换一组</span></a>
            </div>
            <div class="likeBox-live of">
                <ul class="likeBox-ul">
                    {foreach $content.content.guessYouLike.list as $item}
                        <li>
                            <a href="./{$item.roomID}" class="">
                                <div class="img-block">
                                    <i class="icon-play"></i>
                                    <b class="mask"></b>
                                    {if $item.orientation == 0}
                                        <img class="angle_class" src="{$item.poster}">
                                        {else}
                                        <img src="{$item.poster}">
                                    {/if}
                                </div>
                                <div class="live-info">
                                    <p>{$item.title}</p>
                                    <div class="live-text">
                                        <i class="icon-p1"></i>
                                        <span class="live-infon">{$item.nick}</span>
                                        <i class="icon-p2"></i>
                                        <span class="live-infor">{$item.userCount}</span>
                                        <span class="livesm-gm fc-orange r">{$item.gameName}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
        <div class="rankBox trans">
            <div class="rankBoxtitle">
                <i class="icon-rank"></i>
                <span class="rank-title-text">排行榜</span>
                <div class="rank-titler">
                    <span class="rank-d cur">日</span>
                    <span class="rank-w">周</span>
                    <span class="rank-m">月</span>
                </div>
            </div>
            <div class="rank-tabBox of">
                <a href="javascript:;" class="rank-tabBtn1 cur" usertype="anchor">
                    <i class="icon-rank1"></i>
                    <span>主播排行榜</span>
                </a>
                <a href="javascript:;" class="rank-tabBtn2" usertype="wealth">
                    <i class="icon-rank2"></i>
                    <span>财富排行榜</span>
                </a>
                <div class="underline"></div>
            </div>
            <div class="day-box ranktabBox" date-type="day">
                <ul class="ranklist" user-type="anchor">
                    {foreach $content.content.homeRanking.anchorList.dayList as $item}
                        <li class="rank-li">
                            <a href="./{$item.roomID}" class="">
                                <i class=""></i>
                                <img src="{$item.head}"/>
                                <span class="username">{$item.nick}</span>
                            </a>
                        </li>
                    {/foreach}
                </ul>
                <ul class="ranklist" user-type="wealth" style="display: none;">
                    {foreach $content.content.homeRanking.moneyList.dayList as $item}
                        <li class="rank-li">
                            <a href="javascript:;" class="">
                                <i class=""></i>
                                <img src="{$item.head}"/>
                                <span class="username">{$item.nick}</span>
                            </a>
                        </li>
                    {/foreach}
                </ul>
            </div>
            <div class="week-box ranktabBox" date-type="week" style="display: none;">
                <ul class="ranklist" user-type="anchor">
                    {foreach $content.content.homeRanking.anchorList.weekList as $item}
                        <li class="rank-li">
                            <a href="./{$item.roomID}" class="">
                                <i class=""></i>
                                <img src="{$item.head}"/>
                                <span class="username">{$item.nick}</span>
                            </a>
                        </li>
                    {/foreach}
                </ul>
                <ul class="ranklist" user-type="wealth" style="display: none;">
                    {foreach $content.content.homeRanking.moneyList.weekList  as $item}
                        <li class="rank-li">
                            <a href="javascript:;" class="">
                                <i class=""></i>
                                <img src="{$item.head}"/>
                                <span class="username">{$item.nick}</span>
                            </a>
                        </li>
                    {/foreach}
                </ul>
            </div>
            <div class="month ranktabBox" date-type="month" style="display: none;">
                <ul class="ranklist" user-type="anchor">
                    {foreach $content.content.homeRanking.anchorList.monthList  as $item}
                        <li class="rank-li">
                            <a href="./{$item.roomID}" class="">
                                <i class=""></i>
                                <img src="{$item.head}"/>
                                <span class="username">{$item.nick}</span>
                            </a>
                        </li>
                    {/foreach}
                </ul>
                <ul class="ranklist" user-type="wealth" style="display: none;">
                    {foreach $content.content.homeRanking.moneyList.monthList  as $item}
                        <li class="rank-li">
                            <a href="javascript:;" class="">
                                <i class=""></i>
                                <img src="{$item.head}"/>
                                <span class="username">{$item.nick}</span>
                            </a>
                        </li>
                    {/foreach}
                </ul>
            </div>
            <a href="./rank.php" class="rank-more">查看完整榜单</a>
        </div>
    </div>
    <div class="game-listBox of">
        <div class="game-list trans l">
            <div class="game-list-t">
                <i class="icon-game-list"></i>
                <span class="game-list-title l">游戏分类</span>
                <div class="r of">
                    <span class="game-listall l">共 <span
                                class="fc-orange">{$content.content.gameInfoList.total}</span> 款游戏</span>
                    <a href="./game.php" class="glist-more r">更多>> </a>
                </div>
            </div>
            <ul class="game-li">
                {foreach $content.content.gameInfoList.list as $item}
                    <li class="game-item">
                        <a href="./GameZone.php?gid={$item.gameid}" target="_blank">
                            <img src="{$item.poster}"/>
                            <div class="gametext of">
                                <span class="gametext-l">{$item.name}</span>
                                <div class="gameAnchor">
                                    <i class="icon-p"></i>
                                    <span class="gametext-r">{$item.liveCount}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
        <div class="adBox r">
            <div class="ad-img">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                    {foreach $content.content.getInformation.plist as $item}
                        <div class="swiper-slide">
                            <a href="{$item.url}" class="">
                                <img src="{$item.poster}"/>
                            </a>
                        </div>
                    {/foreach}
                    </div>
                    <div class="swiper-pagination"></div>
                </div>   
            </div>
            <ul class="news-list">
                {foreach $content.content.getInformation.tlist as $item}
                    <li>
                        <span>{$item.type}</span>
                        <span>|</span>
                        <span><a href="{if $item.url == ''}
                                        ./news.php?id={$item.id}
                                        {else}
                                        ./news/{$item.url}
                                        {/if}" class="">{$item.title}</a></span>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>

    {if $content.content.homePageGameList.wzry.new.total == 0}
      <div class="game-block" style="display:none;">
      {else}
      <div class="game-block">
    {/if}
        <div class="game-title of">
            <i class="icon-wzry l"></i>
            <div class="game-name l">王者荣耀</div>
            <div class="gamebtnBox l">
                <a href="javascript:;" class="game-new cur">最热</a>
                <a href="javascript:;" class="game-hot">最新</a>
                <a href="javascript:;" class="game-maxFollow">最多关注</a>
                <div class="underline-min"></div>
            </div>
            <div class="gamer-btnbox r">
                <span class="gamer-l">共<span
                            class="fc-orange">{$content.content.homePageGameList.wzry.hot.total}</span>场直播</span>
                <a href="./GameZone.php?gid=190" target="_blank" class="gamer-r">更多>> </a>
            </div>
        </div>
        <div class="gamer-live of">
            <ul class="gameBox-ul trans" game-tab="hot">
                {foreach $content.content.homePageGameList.wzry.hot.list as $item}
                    <li>
                        <a href="./{$item.roomID}" class="">
                            <div class="img-block">
                                <i class="icon-play"></i>
                                <b class="mask"></b>
                                {if $item.orientation == 0}
                                        <img class="angle_class" src="{$item.poster}">
                                        {else}
                                        <img src="{$item.poster}">
                                    {/if}
                            </div>
                            <div class="live-info of">
                                <p>{$item.title}</p>
                                <div class="live-text">
                                    <i class="icon-p1"></i>
                                    <span class="live-infon">{$item.nick}</span>
                                    <i class="icon-p2"></i>
                                    <span class="live-infor">{$item.viewCount}</span>
                                    <span class="livesm-gm fc-orange r">{$item.gameName}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                {/foreach}
            </ul>
            <ul class="gameBox-ul trans" game-tab="new" style="display: none;">
                {foreach $content.content.homePageGameList.wzry.new.list as $item}
                    <li>
                        <a href="./{$item.roomID}" class="">
                            <div class="img-block">
                                <i class="icon-play"></i>
                                <b class="mask"></b>
                                {if $item.orientation == 0}
                                        <img class="angle_class" src="{$item.poster}">
                                        {else}
                                        <img src="{$item.poster}">
                                    {/if}
                            </div>
                            <div class="live-info of">
                                <p>{$item.title}</p>
                                <div class="live-text">
                                    <i class="icon-p1"></i>
                                    <span class="live-infon">{$item.nick}</span>
                                    <i class="icon-p2"></i>
                                    <span class="live-infor">{$item.viewCount}</span>
                                    <span class="livesm-gm fc-orange r">{$item.gameName}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                {/foreach}
            </ul>
            <ul class="gameBox-ul trans" game-tab="maxFollow" style="display: none;">
                {foreach $content.content.homePageGameList.wzry.maxfollow.list as $item}
                    <li>
                        <a href="./{$item.roomID}" class="">
                            <div class="img-block">
                                <i class="icon-play"></i>
                                <b class="mask"></b>
                                {if $item.orientation == 0}
                                        <img class="angle_class" src="{$item.poster}">
                                        {else}
                                        <img src="{$item.poster}">
                                    {/if}
                            </div>
                            <div class="live-info of">
                                <p>{$item.title}</p>
                                <div class="live-text">
                                    <i class="icon-p1"></i>
                                    <span class="live-infon">{$item.nick}</span>
                                    <i class="icon-p2"></i>
                                    <span class="live-infor">{$item.viewCount}</span>
                                    <span class="livesm-gm fc-orange r">{$item.gameName}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
    {if $content.content.homePageGameList.qqdzz.hot.total == 0}
      <div class="game-block" style="display:none;">
      {else}
      <div class="game-block">
    {/if}
        <div class="game-title of">
            <i class="icon-qqdzz l"></i>
            <div class="game-name l">球球大作战</div>
            <div class="gamebtnBox l">
                <a href="javascript:;" class="game-new cur">最热</a>
                <a href="javascript:;" class="game-hot">最新</a>
                <a href="javascript:;" class="game-maxFollow">最多关注</a>
                <div class="underline-min"></div>
            </div>
            <div class="gamer-btnbox r">
                <span class="gamer-l">共<span
                            class="fc-orange">{$content.content.homePageGameList.qqdzz.hot.total}</span>场直播</span>
                <a href="./GameZone.php?gid=150" target="_blank" class="gamer-r">更多>> </a>
            </div>
        </div>
        <div class="gamer-live of">
            <ul class="gameBox-ul trans" game-tab="hot">
                {foreach $content.content.homePageGameList.qqdzz.hot.list as $item}
                    <li>
                        <a href="./{$item.roomID}" class="">
                            <div class="img-block">
                                <i class="icon-play"></i>
                                <b class="mask"></b>
                                {if $item.orientation == 0}
                                        <img class="angle_class" src="{$item.poster}">
                                        {else}
                                        <img src="{$item.poster}">
                                    {/if}
                            </div>
                            <div class="live-info of">
                                <p>{$item.title}</p>
                                <div class="live-text">
                                    <i class="icon-p1"></i>
                                    <span class="live-infon">{$item.nick}</span>
                                    <i class="icon-p2"></i>
                                    <span class="live-infor">{$item.viewCount}</span>
                                    <span class="livesm-gm fc-orange r">{$item.gameName}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                {/foreach}
            </ul>
            <ul class="gameBox-ul trans" game-tab="new" style="display: none;">
                {foreach $content.content.homePageGameList.qqdzz.new.list as $item}
                    <li>
                        <a href="./{$item.roomID}" class="">
                            <div class="img-block">
                                <i class="icon-play"></i>
                                <b class="mask"></b>
                                {if $item.orientation == 0}
                                        <img class="angle_class" src="{$item.poster}">
                                        {else}
                                        <img src="{$item.poster}">
                                    {/if}
                            </div>
                            <div class="live-info of">
                                <p>{$item.title}</p>
                                <div class="live-text">
                                    <i class="icon-p1"></i>
                                    <span class="live-infon">{$item.nick}</span>
                                    <i class="icon-p2"></i>
                                    <span class="live-infor">{$item.viewCount}</span>
                                    <span class="livesm-gm fc-orange r">{$item.gameName}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                {/foreach}
            </ul>
            <ul class="gameBox-ul trans" game-tab="maxFollow" style="display: none;">
                {foreach $content.content.homePageGameList.qqdzz.maxfollow.list as $item}
                    <li>
                        <a href="./{$item.roomID}" class="">
                            <div class="img-block">
                                <i class="icon-play"></i>
                                <b class="mask"></b>
                                {if $item.orientation == 0}
                                        <img class="angle_class" src="{$item.poster}">
                                        {else}
                                        <img src="{$item.poster}">
                                    {/if}
                            </div>
                            <div class="live-info of">
                                <p>{$item.title}</p>
                                <div class="live-text">
                                    <i class="icon-p1"></i>
                                    <span class="live-infon">{$item.nick}</span>
                                    <i class="icon-p2"></i>
                                    <span class="live-infor">{$item.viewCount}</span>
                                    <span class="livesm-gm fc-orange r">{$item.gameName}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
    {if $content.content.homePageGameList.cyhx.hot.total == 0}
      <div class="game-block" style="display:none;">
      {else}
      <div class="game-block">
    {/if}
        <div class="game-title of">
            <i class="icon-cyhx l"></i>
            <div class="game-name l">穿越火线</div>
            <div class="gamebtnBox l">
                <a href="javascript:;" class="game-new cur">最热</a>
                <a href="javascript:;" class="game-hot">最新</a>
                <a href="javascript:;" class="game-maxFollow">最多关注</a>
                <div class="underline-min"></div>
            </div>
            <div class="gamer-btnbox r">
                <span class="gamer-l">共<span
                            class="fc-orange">{$content.content.homePageGameList.cyhx.hot.total}</span>场直播</span>
                <a href="./GameZone.php?gid=215" target="_blank" class="gamer-r">更多>> </a>
            </div>
        </div>
        <div class="gamer-live of">
            <ul class="gameBox-ul trans" game-tab="hot">
                {foreach $content.content.homePageGameList.cyhx.hot.list as $item}
                    <li>
                        <a href="./{$item.roomID}" class="">
                            <div class="img-block">
                                <i class="icon-play"></i>
                                <b class="mask"></b>
                                {if $item.orientation == 0}
                                        <img class="angle_class" src="{$item.poster}">
                                        {else}
                                        <img src="{$item.poster}">
                                    {/if}
                            </div>
                            <div class="live-info of">
                                <p>{$item.title}</p>
                                <div class="live-text">
                                    <i class="icon-p1"></i>
                                    <span class="live-infon">{$item.nick}</span>
                                    <i class="icon-p2"></i>
                                    <span class="live-infor">{$item.viewCount}</span>
                                    <span class="livesm-gm fc-orange r">{$item.gameName}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                {/foreach}
            </ul>
            <ul class="gameBox-ul trans" game-tab="new" style="display: none;">
                {foreach $content.content.homePageGameList.cyhx.new.list as $item}
                    <li>
                        <a href="./{$item.roomID}" class="">
                            <div class="img-block">
                                <i class="icon-play"></i>
                                <b class="mask"></b>
                                {if $item.orientation == 0}
                                        <img class="angle_class" src="{$item.poster}">
                                        {else}
                                        <img src="{$item.poster}">
                                    {/if}
                            </div>
                            <div class="live-info of">
                                <p>{$item.title}</p>
                                <div class="live-text">
                                    <i class="icon-p1"></i>
                                    <span class="live-infon">{$item.nick}</span>
                                    <i class="icon-p2"></i>
                                    <span class="live-infor">{$item.viewCount}</span>
                                    <span class="livesm-gm fc-orange r">{$item.gameName}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                {/foreach}
            </ul>
            <ul class="gameBox-ul trans" game-tab="maxFollow" style="display: none;">
                {foreach $content.content.homePageGameList.cyhx.maxfollow.list as $item}
                    <li>
                        <a href="./{$item.roomID}" class="">
                            <div class="img-block">
                                <i class="icon-play"></i>
                                <b class="mask"></b>
                                {if $item.orientation == 0}
                                        <img class="angle_class" src="{$item.poster}">
                                        {else}
                                        <img src="{$item.poster}">
                                    {/if}
                            </div>
                            <div class="live-info of">
                                <p>{$item.title}</p>
                                <div class="live-text">
                                    <i class="icon-p1"></i>
                                    <span class="live-infon">{$item.nick}</span>
                                    <i class="icon-p2"></i>
                                    <span class="live-infor">{$item.viewCount}</span>
                                    <span class="livesm-gm fc-orange r">{$item.gameName}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
</div>
{include file="toTop.tpl"}
{include file="footer.tpl"}
</body>
<div class="hp-tools">
  {include file="loginModal.tpl"}
  {include file="webChat.tpl"}
</div>
</html>
