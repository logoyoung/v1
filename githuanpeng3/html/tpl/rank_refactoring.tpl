<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>排行榜-欢朋直播-精彩手游直播平台！</title>
    {config_load file="hpTPL.conf" section="setup"}
    {#commonMETASource#}
    {#commonCSSSource#}
    <link rel="stylesheet" href="{#hpCSS#}rank_v4.css{#hpVersion#}">
    <style>
      a,a:link,a:visited,a:hover,a:active{
        text-decoration: none;
      }
    </style>
  	<script>
  		var $head = {$header};
  	</script>
    {#commonJSSource#}
</head>
<body>
{include file="header.tpl"}
<div class="content of">
    <div class="rankBox">
        <div class="rank-block l">
            <div class="rank-title">
                <div class="rank-sub">
                    <i class="icon-income"></i>
                    <span class="rank-ttext">主播收入榜</span>
                </div>
                <div class="rank-tabBtn">
                    <a href="javascript:;" id="tabTitle1_1" onclick="tab(1,3,1)" class="cur">日榜</a>
                    <a href="javascript:;" id="tabTitle1_2" onclick="tab(1,3,2)" class="">周榜</a>
                    <a href="javascript:;" id="tabTitle1_3" onclick="tab(1,3,3)" class="">月榜</a>
                </div>
            </div>
            <div class="rank-main">
                <ul class="rank-incomeBox" id="tabMain1_1">
                    {foreach $anchorEarn.dayList as $item}
                        {if $item@iteration == 1}
                            <li class="rankLi-first">
                                <a href="./{$item.roomID}" class="rank-list">
                                    <i class="icon-rankCrown"></i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv anchorLvl_icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                    {if {$item.status} == 1}
                                        <i class="icon-up"></i>
                                    {elseif {$item.status} == 0}
                                        <i class="icon-keep"></i>
                                    {else}
                                        <i class="icon-down"></i>
                                    {/if}
                                </a>
                            </li>
                        {else}
                            <li class="rankLi-sec">
                                <a href="./{$item.roomID}" class="rank-list">
                                    <i class="icon-rankNum">{$item@iteration}.</i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv anchorLvl_icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                    {if {$item.status} == 1}
                                        <i class="icon-up"></i>
                                    {elseif {$item.status} == 0}
                                        <i class="icon-keep"></i>
                                    {else}
                                        <i class="icon-down"></i>
                                    {/if}
                                </a>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
                <ul class="rank-incomeBox" id="tabMain1_2" style="display: none;">
                    {foreach $anchorEarn.weekList as $item}
                        {if $item@iteration == 1}
                            <li class="rankLi-first">
                                <a href="./{$item.roomID}" class="rank-list">
                                    <i class="icon-rankCrown"></i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv anchorLvl_icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                </a>
                            </li>
                        {else}
                            <li class="rankLi-sec">
                                <a href="./{$item.roomID}" class="rank-list">
                                    <i class="icon-rankNum">{$item@iteration}.</i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv anchorLvl_icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                </a>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
                <ul class="rank-incomeBox" id="tabMain1_3" style="display: none;">
                    {foreach $anchorEarn.monthList as $item}
                        {if $item@iteration == 1}
                            <li class="rankLi-first">
                                <a href="./{$item.roomID}" class="rank-list">
                                    <i class="icon-rankCrown"></i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv anchorLvl_icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                </a>
                            </li>
                        {else}
                            <li class="rankLi-sec">
                                <a href="./{$item.roomID}" class="rank-list">
                                    <i class="icon-rankNum">{$item@iteration}.</i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv anchorLvl_icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                </a>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            </div>
        </div>
        <div class="rank-block l">
            <div class="rank-title">
                <div class="rank-sub">
                    <i class="icon-sentiment"></i>
                    <span class="rank-ttext">主播人气榜</span>
                </div>
                <div class="rank-tabBtn">
                    <a href="javascript:;" id="tabTitle2_1" onclick="tab(2,3,1)" class="cur">日榜</a>
                    <a href="javascript:;" id="tabTitle2_2" onclick="tab(2,3,2)" class="">周榜</a>
                    <a href="javascript:;" id="tabTitle2_3" onclick="tab(2,3,3)" class="">月榜</a>
                </div>
            </div>
            <div class="rank-main">
                <ul class="rank-incomeBox" id="tabMain2_1">
                    {foreach $anchorPop.dayList as $item}
                        {if $item@iteration == 1}
                            <li class="rankLi-first">
                                <a href="./{$item.roomID}" class="rank-list">
                                    <i class="icon-rankCrown"></i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv anchorLvl_icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                    {if {$item.status} == 1}
                                        <i class="icon-up"></i>
                                    {elseif {$item.status} == 0}
                                        <i class="icon-keep"></i>
                                    {else}
                                        <i class="icon-down"></i>
                                    {/if}
                                </a>
                            </li>
                        {else}
                            <li class="rankLi-sec">
                                <a href="./{$item.roomID}" class="rank-list">
                                    <i class="icon-rankNum">{$item@iteration}.</i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv anchorLvl_icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                    {if {$item.status} == 1}
                                        <i class="icon-up"></i>
                                    {elseif {$item.status} == 0}
                                        <i class="icon-keep"></i>
                                    {else}
                                        <i class="icon-down"></i>
                                    {/if}
                                </a>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
                <ul class="rank-incomeBox" id="tabMain2_2" style="display: none;">
                    {foreach $anchorPop.weekList as $item}
                        {if $item@iteration == 1}
                            <li class="rankLi-first">
                                <a href="./{$item.roomID}" class="rank-list">
                                    <i class="icon-rankCrown"></i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv anchorLvl_icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                </a>
                            </li>
                        {else}
                            <li class="rankLi-sec">
                                <a href="./{$item.roomID}" class="rank-list">
                                    <i class="icon-rankNum">{$item@iteration}.</i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv anchorLvl_icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                </a>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
                <ul class="rank-incomeBox" id="tabMain2_3" style="display: none;">
                    {foreach $anchorPop.monthList as $item}
                        {if $item@iteration == 1}
                            <li class="rankLi-first">
                                <a href="./{$item.roomID}" class="rank-list">
                                    <i class="icon-rankCrown"></i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv anchorLvl_icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                </a>
                            </li>
                        {else}
                            <li class="rankLi-sec">
                                <a href="./{$item.roomID}" class="rank-list">
                                    <i class="icon-rankNum">{$item@iteration}.</i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv anchorLvl_icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                </a>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            </div>
        </div>
        <div class="rank-block l">
            <div class="rank-title">
                <div class="rank-sub">
                    <i class="icon-level"></i>
                    <span class="rank-ttext">主播等级榜</span>
                </div>
            </div>
            <div class="rank-main">
                <ul class="rank-incomeBox">
                    {foreach $anchorLevel.list as $item}
                        {if $item@iteration == 1}
                            <li class="rankLi-first">
                                <a href="./{$item.roomID}" class="rank-list">
                                    <i class="icon-rankCrown"></i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv anchorLvl_icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>

                                </a>
                            </li>
                        {else}
                            <li class="rankLi-sec">
                                <a href="./{$item.roomID}" class="rank-list">
                                    <i class="icon-rankNum">{$item@iteration}.</i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv anchorLvl_icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>

                                </a>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            </div>
        </div>
        <div class="rank-block l">
            <div class="rank-title">
                <div class="rank-sub">
                    <i class="icon-contribution"></i>
                    <span class="rank-ttext">观众贡献榜</span>
                </div>
                <div class="rank-tabBtn">
                    <a href="javascript:;" id="tabTitle3_1" onclick="tab(3,3,1)" class="cur">日榜</a>
                    <a href="javascript:;" id="tabTitle3_2" onclick="tab(3,3,2)" class="">周榜</a>
                    <a href="javascript:;" id="tabTitle3_3" onclick="tab(3,3,3)" class="">月榜</a>
                </div>
            </div>
            <div class="rank-main l">
                <ul class="rank-incomeBox" id="tabMain3_1">
                    {foreach $userDevote.dayList as $item}
                        {if $item@iteration == 1}
                            <li class="rankLi-first">
                                <a href="javascript:;" class="rank-list">
                                    <i class="icon-rankCrown"></i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv userLvl-icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                    {if {$item.status} == 1}
                                        <i class="icon-up"></i>
                                    {elseif {$item.status} == 0}
                                        <i class="icon-keep"></i>
                                    {else}
                                        <i class="icon-down"></i>
                                    {/if}
                                </a>
                            </li>
                        {else}
                            <li class="rankLi-sec">
                                <a href="javascript:;" class="rank-list">
                                    <i class="icon-rankNum">{$item@iteration}.</i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv userLvl-icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                    {if {$item.status} == 1}
                                        <i class="icon-up"></i>
                                    {elseif {$item.status} == 0}
                                        <i class="icon-keep"></i>
                                    {else}
                                        <i class="icon-down"></i>
                                    {/if}
                                </a>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
                <ul class="rank-incomeBox" id="tabMain3_2" style="display: none;">
                    {foreach $userDevote.weekList as $item}
                        {if $item@iteration == 1}
                            <li class="rankLi-first">
                                <a href="javascript:;" class="rank-list">
                                    <i class="icon-rankCrown"></i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv userLvl-icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                </a>
                            </li>
                        {else}
                            <li class="rankLi-sec">
                                <a href="javascript:;" class="rank-list">
                                    <i class="icon-rankNum">{$item@iteration}.</i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv userLvl-icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                </a>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
                <ul class="rank-incomeBox" id="tabMain3_3" style="display: none;">
                    {foreach $userDevote.monthList as $item}
                        {if $item@iteration == 1}
                            <li class="rankLi-first">
                                <a href="javascript:;" class="rank-list">
                                    <i class="icon-rankCrown"></i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv userLvl-icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                </a>
                            </li>
                        {else}
                            <li class="rankLi-sec">
                                <a href="javascript:;" class="rank-list">
                                    <i class="icon-rankNum">{$item@iteration}.</i>
                                    <div class="icon-pic">
                                        <img class="user-pic" src="{$item.head}"/>
                                    </div>
                                    <i class="icon-lv userLvl-icon lv{$item.level}"></i>
                                    <span class="nick">{$item.nick}</span>
                                </a>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            </div>
        </div>
    </div>
</div>
{include file="toTop.tpl"}
{include file="footer.tpl"}
<script type="text/javascript">
    //滑动门切换
    function tab(a, b, c) {
        for (var i = 1; i <= b; i++) {
            document.getElementById("tabMain" + a + "_" + i).style.display = "none";
            document.getElementById("tabTitle" + a + "_" + i).className = "";

        }
        document.getElementById("tabMain" + a + "_" + c).style.display = "block";
        document.getElementById("tabTitle" + a + "_" + c).className = "cur";
    };
    window.onscroll = function () {
      if($(window).scrollTop() >= 50){
          $('.to_top').show();
      }else{
          $('.to_top').hide();
      }
    };

    $(".to_top").click(function () {
        var speed=200;
        $('body,html').animate({ scrollTop: 0 }, speed,function(){
            return;
        });

    });
</script>
</body>
<div class="hp-tools">
  {include file="loginModal.tpl"}
  {include file="webChat.tpl"}
</div>
</html>
