<?php
include_once INCLUDE_DIR . 'Anchor.class.php';
use lib\Anchor;



$sidebarCenter[0]['url']        = WEB_PERSONAL_URL;
$sidebarCenter[0]['text']       = '个人资料';
$sidebarCenter[0]['icon'] = 'anchorIcon';
$sidebarCenter[0]['li-class'] = 'li-personal';

$sidebarCenter[1]['url'] = WEB_PERSONAL_URL . "pm";
$sidebarCenter[1]['text'] = '我的消息';
$sidebarCenter[1]['icon'] = 'msgIcon';
$sidebarCenter[1]['li-class'] = 'li-msg';

$sidebarCenter[2]['url'] = WEB_PERSONAL_URL . "follow";
$sidebarCenter[2]['text'] = '我的关注';
$sidebarCenter[2]['icon'] = 'followIcon';
$sidebarCenter[2]['li-class'] = 'li-follow';

$sidebarCenter[3]['url'] = WEB_PERSONAL_URL . 'beanchor.php';
$sidebarCenter[3]['text'] = '我做主播';
$sidebarCenter[3]['icon'] = 'beAnchorIcon';
$sidebarCenter[3]['li-class'] = 'li-beanchor';

$sidebarCenter[4]['url'] = WEB_PERSONAL_URL . 'giftRecord';
$sidebarCenter[4]['text'] = '送礼记录';
$sidebarCenter[4]['icon'] = 'giftHistoryIcon';
$sidebarCenter[4]['li-class'] = 'li-gift';
$isAnchor = FALSE;
if ((int) $_COOKIE['_uid'] && $_COOKIE['_enc']) {
    $db = new DBHelperi_huanpeng();
    $anchor = new Anchor($_COOKIE['_uid'], $db);
    $isAnchor = $anchor->isAnchor($anchor->uid ,$db) ;
    if ($isAnchor === TRUE ) {
        $sidebarCenter[5]['url'] = WEB_PERSONAL_URL . 'zone';
        $sidebarCenter[5]['text'] = '我的空间';
        $sidebarCenter[5]['icon'] = 'zoneIcon';
        $sidebarCenter[5]['li-class'] = 'li-zone';

        $sidebarCenter[6]['url'] = WEB_PERSONAL_URL . 'property';
        $sidebarCenter[6]['text'] = '我的收益';
        $sidebarCenter[6]['icon'] = 'myCoinIcon';
        $sidebarCenter[6]['li-class'] = 'li-property';

        $sidebarCenter[7]['url'] = WEB_PERSONAL_URL . 'roomadmin.php';
        $sidebarCenter[7]['text'] = '我的房管';
        $sidebarCenter[7]['icon'] = 'roomManageIcon';
        $sidebarCenter[7]['li-class'] = 'li-admin';

        $sidebarCenter[3]['url'] = WEB_PERSONAL_URL . "homepage";
        $sidebarCenter[3]['text'] = '主播资料';
        $sidebarCenter[3]['icon'] = 'homePageIcon';
        $sidebarCenter[3]['li-class'] = 'li-homepage';
    }
}

$sidebarCenter[8]['url'] = WEB_PERSONAL_URL . 'recharge.php';
$sidebarCenter[8]['text'] = '充值';
$sidebarCenter[8]['icon'] = 'rechargeIcon';
$sidebarCenter[8]['li-class'] = 'li-recharge';

$sidebar_beAnchor['url'] = $sidebarCenter[3]['url'];
$sidebar_beAnchor['text'] = "我要做主播";

if ($isAnchor === TRUE) {
    $roomid = $anchor->getRoomID();
    $sidebar_beAnchor['url'] = WEB_ROOT_URL . $roomid;
    $sidebar_beAnchor['text'] = "进入直播间";
}
?>

<div class="sidebar_center">
    <div class="title">个人中心</div>
    <ul>
        <?php
        foreach ($sidebarCenter as $key => $val) {
            echo "<li class='{$val['li-class']}'>";
            echo "<a href='{$val['url']}'>";
            echo "<span class='personal_icon {$val['icon']}'></span>";
            echo "<span class='text'>{$val['text']}</span>";
            echo "</a>";
            echo "</li>";
        }
        ?>
    </ul>
    <div class="footer">
        <div class="title">想成为万众瞩目的主播</div>
        <a href="<?php echo $sidebar_beAnchor['url']; ?>">
            <div class="beAnchor"><span class="anchor_icon <?php echo $anchor->isAnchor($anchor->uid,$db) === TRUE ? 'beAnchorIcon-anchor' : 'beAnchorIcon-user'; ?>"></span><?php echo $sidebar_beAnchor['text']; ?></div>
        </a>
    </div>
</div>