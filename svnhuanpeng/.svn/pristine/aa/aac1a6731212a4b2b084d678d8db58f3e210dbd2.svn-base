<!DOCTYPE html>
<html>
<head>
    <title>全部直播-欢朋直播-精彩手游直播平台！</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset='utf-8'>
    {config_load file="hpTPL.conf" section="setup"}
    {#commonMETASource#}
    {#commonCSSSource#}
    <link rel="stylesheet" href="{#hpCSS#}home_v3.css{#hpVersion#}">
    <link rel="stylesheet" href="{#hpCSS#}LiveHall_v4.css{#hpVersion#}">
    <link rel="stylesheet" href="{#hpCSS#}toTop.css{#hpVersion#}">
  	<script>
  		var $head = {$header};
  	</script>
    {#commonJSSource#}
    <script type="text/javascript" src="{#hpJS#}LiveHall_v4.js{#hpVersion#}"></script>
</head>
<body>
{include file="header.tpl"}
<div id="liveHall" class="w1180">

    <div id="block5" class="contain">

        <div class="block">
            <div class="block_title">
                <span id="game" class="fl">{$content.content.hotList.ref}</span>
                <span class="livecount">目前有&nbsp;<p class="num">{$content.content.hotList.total}</p>&nbsp;个主播正在直播</span>
                <span class="fr livecount" id="liveHall_follow_btn">最多关注</span>
                <span class="fr livecount" id="liveHall_new_btn">最新</span>
                <span class="fr livecount cur" id="liveHall_hot_btn">最热</span>
            </div>
            <div class="block_live">
                <ul class="hp_hotList block_list">
                    {foreach $content.content.hotList.list as $item}
                        <li class="h_item">
                            <a href="./{$item.roomID}" target="_blank">
                                <div class="img_block"><i></i><b></b>
                                    {if $item.orientation == 0}
                                        <img class="angle_class"src="{$item.poster}">
                                        {else}
                                        <img src="{$item.poster}">
                                    {/if}
                                </div>
                                <div class="liveinfo">
                                    <p>{$item.title}</p>
                                    <div class="icon1"></div>
                                    <span class="fl nick" data-uid={$item.uid}>{$item.nick}</span>
                                    <div class="icon2"></div>
                                    <span class="fl">{$item.viewCount}</span>
                                    <span class="fr last">{$item.gameName}</span>
                                </div>
                            </a>
                        </li>
                    {/foreach}
                </ul>
                <ul class="hp_newList block_list none">
                    {foreach $content.content.newList.list as $item}
                        <li class="h_item">
                            <a href="./{$item.roomID}" target="_blank">
                                <div class="img_block"><i></i><b></b>
                                  {if $item.orientation == 0}
                                      <img class="angle_class"src="{$item.poster}">
                                      {else}
                                      <img src="{$item.poster}">
                                  {/if}
                                </div>
                                <div class="liveinfo">
                                    <p>{$item.title}</p>
                                    <div class="icon1"></div>
                                    <span class="fl nick" data-uid={$item.uid}>{$item.nick}</span>
                                    <div class="icon2"></div>
                                    <span class="fl">{$item.viewCount}</span>
                                    <span class="fr last">{$item.gameName}</span>
                                </div>
                            </a>
                        </li>
                    {/foreach}
                </ul>
                <ul class="hp_followList block_list none">
                    {foreach $content.content.maxfollowList.list as $item}
                        <li class="h_item">
                            <a href="./{$item.roomID}" target="_blank">
                                <div class="img_block"><i></i><b></b>
                                  {if $item.orientation == 0}
                                      <img class="angle_class"src="{$item.poster}">
                                      {else}
                                      <img src="{$item.poster}">
                                  {/if}
                                </div>
                                <div class="liveinfo">
                                    <p>{$item.title}</p>
                                    <div class="icon1"></div>
                                    <span class="fl nick" data-uid={$item.uid}>{$item.nick}</span>
                                    <div class="icon2"></div>
                                    <span class="fl">{$item.viewCount}</span>
                                    <span class="fr last">{$item.gameName}</span>
                                </div>
                            </a>
                        </li>
                    {/foreach}
                </ul>
            </div>

        </div>
        <div class="more_live" data-type="0">
            加载更多直播
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
