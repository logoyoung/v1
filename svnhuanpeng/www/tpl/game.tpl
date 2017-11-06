<!doctype html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="UTF-8">
    <title>游戏分类-欢朋直播-精彩手游直播</title>
    {config_load file="hpTPL.conf" section="setup"}
    {#commonMETASource#}
    {#commonCSSSource#}
    <link rel="stylesheet" href="{#hpCSS#}game_v4.css{#hpVersion#}">
    {#commonJSSource#}
    <script>
		var $head = {$header};
	  </script>
    {#commonJSSource#}
    <script>
        if (navigator.userAgent.toLowerCase().match(/(iphone|ipod|android|ios|ipad|ucweb)/i))  {
            location.href = '../mobile';
        }
    </script>
    <script type="text/javascript" src="{#hpJS#}game_v4.js{#hpVersion#}"></script>
</head>
<body>
{include file="header.tpl"}
<div id="gameType" class="w1180">
    <div class="game_block">
        <div class="title">游戏分类</div>
        <ul>
            {foreach $content.content.list as $item}
                <li class="h_item" data-index={$item@iteration}>
                    <a href="GameZone.php?gid={$item.gameid}" target="_blank">
                        <img class="lazy-img" data-error="./static/img/place_img/index_cate.png" src="./static/img/place_img/index_cate.png" hp-src={$item.poster}>
                        <div class="liveinfo">
                            <div class="gt">{$item.name}</div>
                            <div class="count_txt">{$item.liveCount}</div>
                            <div class="icon_set count"></div>
                        </div>
                    </a>
                </li>
            {/foreach}
        </ul>
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
