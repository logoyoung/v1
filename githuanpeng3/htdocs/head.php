<?php
include_once (dirname(dirname(__FILE__)).'/include/init.php');

function isAnchor($uid, $db){
    if( !(int)$uid||!$db ) return false;
    $sql = "select `uid` from anchor where uid={$uid}";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();
    return $row['uid']?$row['uid']:false;
}
function getUserInfo2($uid, $enc, $db, $conf)
{
    $userInfo = array(
        'loginStatus' => 0
    );
    if (CheckUserIsLogIn($uid, $enc, $db) !== true)
        return $userInfo;
    $row = getUserBaseInfo($uid, $db);
    $levelIntegral = getLevelIntegral($row['level'], $db);
    $userInfo['loginStatus'] = 1;
    $userInfo['_uid'] = $uid;
    $userInfo['_enc'] = $enc;
    $userInfo['nickName'] = $row['nick'];
    
    $url = DOMAIN_PROTOCOL . $conf['domain-img'] . '/';
    $userInfo['pic'] = $row['pic'] ? $url . $row['pic'] : DEFAULT_PIC;
    
    $userInfo['level'] = $row['level'];
    $userInfo['integral'] = $row['integral'];
    $userInfo['readsign'] = $row['readsign'];
    $userInfo['hpbean'] = $row['hpbean'];
    $userInfo['hpcoin'] = $row['hpcoin'];
    $userInfo['levelIntegral'] = $levelIntegral;
    return $userInfo;
}

$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$uid = isset($_COOKIE['_uid']) ? (int) $_COOKIE['_uid'] : 0;
$enc = isset($_COOKIE['_enc']) ? trim($_COOKIE['_enc']) : '';
$userInfo = getUserInfo2($uid, $enc, $db, $conf);
$userInfo = json_encode($userInfo);

?>

<?php echo "<script>var user = {$userInfo}</script>"; ?>

	<div id="topfixed">
		<div class="navi">
			<div id="size" class="w1180">
				<ul class="left">
					<li class="logo"><a href="<?php echo WEB_ROOT_URL;?>index.php"><img
							src="<?php echo WEB_MEDIA_URL;?>img/logo_v2.png"
						></a></li>
					<li class="txt "><a href="<?php echo WEB_ROOT_URL;?>">首页</a></li>
					<li class="txt"><a href="<?php echo WEB_ROOT_URL;?>LiveHall.php">直播</a></li>
					<li class="txt h_sup"><div style="padding-bottom: 15px">
							<a href="<?php echo WEB_ROOT_URL;?>game.php">分类</a>
						</div>
						<div class="gametypePop h_pop">
							<div class="pop"></div>
							<span class="more"><a href="<?php echo WEB_ROOT_URL;?>game.php">
									更多游戏</a> </span>
						</div></li>
					<li class="txt"><a href="<?php echo WEB_ROOT_URL;?>rank.php">排行榜</a></li>
					<li class="last">
						<div class="search_icon">
							<div class="icon_set icon"></div>
						</div> <input class="search" type="text" placeholder="主播、直播"
						onfocus="if(this.placeholder=='主播、直播'){this.placeholder='';this.style.color='#444';}"
						onblur="if(this.placeholder=='') {this.placeholder='主播、直播';this.style.color='#999'; }"
					>
					</li>
				</ul>
				<div class="right">
					<a class="rightspan"
						href="<?php echo isAnchor($uid, $db)?WEB_ROOT_URL."room.php?luid={$uid}&to_open_live=1":WEB_ROOT_URL."personal/beanchor.php"; ?>"
					> <span class="icon_set icon <?php echo isAnchor($uid, $db)?'icon_anchor':'icon_user' ?>"></span><span class="txt"><?php echo isAnchor($uid, $db)?'发直播':'做主播' ?></span>
					</a> <div class="rightspan h_sup" href="javascript: void(0)"> <span
						class="icon_set icon fir icon2"
					><div class="appdown h_pop">
								<div class="slider-down-list">
                                    <a href="<?php echo WEB_ROOT_URL;?>download.php?reftype=app"><div class="slider-download bottom-line">App下载</div></a>
                                    <a href="<?php echo WEB_ROOT_URL;?>download.php?reftype=pc"><div class="slider-download">直播助手下载</div></a>

								</div>
							</div></span> <span class="txt">下载</span></div> <a
						class="rightspan h h_sup" href="javascript: void(0)"
					> <span class="icon_set icon icon3"><div class="history h_pop">
								<div id="history"
									style="float: left; margin-left: 111px; margin-top: 10px; margin-left: 70px; padding-left: 20px; font-size: 12px; color: #666; position: absolute; display: none"
								>数据加载中.....</div>
								<ul>
									<li class="more no_login">
										<div class="img_no_login">
											<img
												src="<?php echo WEB_MEDIA_URL;?>img/logo/home_no_login.png"
											>
										</div>
										<div class="txt_div">
											还没登录？点击<s class="to_reg">注册</s>
											<l></l>
											<s class="to_log ">登录</s>
										</div>
									</li>
								</ul>
							</div></span> <span class="txt">历史</span></a> <a
						class="rightspan f h_sup" href="javascript: void(0)"
					> <span class="icon_set icon icon4"><div class="lovelist h_pop">
								<div id="follow"
									style="float: left; margin-left: 111px; margin-top: 10px; margin-left: 70px; padding-left: 20px; font-size: 12px; color: #666; position: absolute; display: none"
								>数据加载中.....</div>
								<ul>
									<li class="more no_login">
										<div class="img_no_login">
											<img
												src="<?php echo WEB_MEDIA_URL;?>img/logo/home_no_login.png"
											>
										</div>
										<div class="txt_div">
											还没登录？点击<s class="to_reg">注册</s>
											<l></l>
											<s class="to_log ">登录</s>
										</div>
									</li>
								</ul>
							</div></span> <span class="txt">关注</span></a>
					
				</div>
			</div>
		</div>
	</div>
	<?php  include (WEBSITE_TPL.'webChat.php');?>
<script type="text/javascript" src="<?php echo STATIC_JS_PATH;?>hover.js"></script>
<script src="<?php echo DOMAIN_PROTOCOL; ?>static.geetest.com/static/tools/gt.js"></script>
<?php  include WEBSITE_TPL."loginModal.php"; ?>