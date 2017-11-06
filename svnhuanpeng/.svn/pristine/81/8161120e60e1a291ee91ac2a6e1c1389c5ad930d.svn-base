
<div class="topfixed">
	<div class="nav">
	    <div class="nav_main">
	        <div class="nav_l l">
	            <a href="/index.php" class="logo l"></a>
	            <ul class="nav_tab l">
					{if $headSign == 'index'}
						<li><a href="./index.php" class="nav_btn1 cur">首页</a></li>
						{else}
						<li><a href="./index.php" class="nav_btn1">首页</a></li>
					{/if}
					{if $headSign == 'LiveHall'}
						<li><a href="./LiveHall.php" class="nav_btn2 cur">直播</a></li>
						{else}
						<li><a href="./LiveHall.php" class="nav_btn2">直播</a></li>
					{/if}

					<li class="xl">
						<div class="">
							{if $headSign == 'game'}
								<a href="./game.php" class="nav_btn3 cur" id="cate_btn">分类</a>
								{else}
								<a href="./game.php" class="nav_btn3" id="cate_btn">分类</a>
							{/if}

							</div>
						<div class="tm"></div>
						<div class="drop_menu">
							<div class="triangle-t"></div>
							<div class="triangle-t2"></div>
							<ul class="game_list">
							</ul>
							<div class="moreBox">
								<a href="./game.php" class="">更多游戏</a>
							</div>
						</div>
					{if $headSign == 'rank'}
						<li><a href="./rank.php" class="nav_btn4 cur">排行榜</a></li>
						{else}
						<li><a href="./rank.php" class="nav_btn4">排行榜</a></li>
					{/if}
	            </ul>
	            <div class="searchBox l of">
	                <input type="text" id="search-hp" placeholder="主播、直播" onfocus="this.placeholder=''" onblur="this.placeholder='主播、直播'" />
	                <a id="search_btn" class="ser_btn r"></a>
	            </div>
	        </div>
	        <div class="nav_r">
	            <div class="nav_rbtn">
	                <a href="javascript:;" class="live_btn">
	                    <i class="live_btnbg icon-color"></i>
	                    <span class="r_btn_sm">做主播</span>
	                </a>
	                <div class="r_btnbox">
	                    <i class="r_btnbg1 icon-color"></i>
	                    <span class="r_btn_sm">下载</span>
	                    <div class="tm2">
	                    </div>
	                    <div class="drop_menu">
	                        <div class="triangle-t"></div>
	                        <div class="triangle-t2"></div>
	                        <ul class="dlBox">
	                            <li class="bbl"><a href="../download.php?reftype=app">APP下载</a></li>
	                            <li><a href="../download.php?reftype=pc">直播助手下载</a></li>
	                        </ul>
	                    </div>
	                </div>
	                <div class="r_btnbox" id="history_btn">
	                    <span class="r_btnbg2 icon-color"></span>
	                    <span class="r_btn_sm">历史</span>
	                    <div class="tm2">
	                    </div>
	                    <div class="drop_menu historyBox_wz">
	                        <div class="triangle-t triangle2"></div>
	                        <div class="triangle-3"></div>
							<ul class="historyBox" ></ul>
							<div id="history-login" class="no-loginBox">
								<div class="no-login">
									<img src="./static/img/logo/home_no_login.png" alt="" />
									<p class="no-loginText">
										<span>还没登录？点击</span>
										<a href="javascript:;" class="login_hp" onclick="loginFast.login(0)">登录</a>
										<span>|</span>
										<a href="javascript:;" onclick="loginFast.login(1)" class="reg_hp">注册</a>
									</p>
								</div>
							</div>
							<div class="loading-box">
								<img src="./static/img/load.gif" alt="">
								<span>数据加载中....</span>
							</div>
	                    </div>
	                </div>
	                <div class="r_btnbox" id="follow_btn">
	                    <span class="r_btnbg3 icon-color"></span>
	                    <span class="r_btn_sm">关注</span>
	                    <div class="tm2">
	                    </div>
	                    <div class="drop_menu focusBox_wz">
	                        <div class="triangle-t triangle3"></div>
	                        <div class="triangle-4"></div>
	                        <div class="follow_box">
								<div class="livecount">
									当前关注的有<span>0</span>个在直播
								</div>
								<ul class="focusBox"></ul>
								<div class="moreBox">
									<a href="/personal/follow/" class="">更多直播</a>
								</div>
							</div>
							<div id="follow-login" class="no-loginBox">
								<div class="no-login">
									<img src="./static/img/logo/home_no_login.png" alt="" />
									<p class="no-loginText">
										<span>还没登录？点击</span>
										<a href="javascript:;" class="login_hp">登录</a>
										<span>|</span>
										<a href="javascript:;" class="reg_hp">注册</a>
									</p>
								</div>
							</div>
							<div id="" class="no-focusBox">
								<div class="no-focus">
									<img src="./static/img/logo/home_no_login.png" alt="" />
									<p class="no-loginText">
										您还没有任何关注哦
									</p>
								</div>
							</div>
							<div class="loading-box">
								<img src="./static/img/load.gif" alt="" style="width: 16px;height: 16px;display: inline-block;">
								<span style="display: inline-block;">数据加载中....</span>
							</div>
	                    </div>
	                </div>
	            </div>
	            <div  id="userinfo" class="user_pic r">
	                <a href=""><img id="user_face" src="./static/img/place_img/userface.png" width="32" height="32"/></a>
	                <div class="tm3">
	                </div>
	                <div class="drop_menu presonBox_wz t40">
	                    <div class="triangle-t triangle4"></div>
	                    <div class="triangle-5"></div>
	                    <div class="personBox">
	                        <div class="person_line1 of">
	                            <div class="person_img l">
	                                <img src="./static/img/place_img/userface.png" width="44" height="44"/>
	                            </div>
	                            <div class="person_assets l">
	                                <p class="person_name"></p>
	                                <div class="assetsBox">
	                                    <div class="hpb l">
	                                        <i class="icon_hpb"></i>
	                                        <span class="hpb_num"></span>
	                                    </div>
	                                    <div class="hpd l">
	                                        <i class="icon_hpd"></i>
	                                        <span class="hpd_num"></span>
	                                    </div>
	                                </div>
	                            </div>
	                            <a href="/personal/recharge.php" target="_blank" class="pay_btn r">充值</a>
	                        </div>
	                        <div class="person_line2 of">
	                            <div class="levelBox">
	                                <div class="of">
	                                    <i class="icon_level"></i>
	                                    <span class="levelBarSpan">
											<strong id="levelBar" style="width:60%"></strong>
										</span>
	                                </div>
	                                <p class="level_text">距离升级还有<span class="fc_orange levelIntegral"></span>经验值</p>
	                            </div>
	                            <a href="/personal/pm/" class="news_btn fc_orange">我的新消息<span class="unreadMsg"></span>条</a>
	                        </div>
	                    </div>
	                    <div class="person_line3 of">
	                        <a href="../personal/" class="l">个人中心</a>
	                        <div class="lineheight"></div>
	                        <a href="javascript:;" class="r" id="delete_User">退出</a>
	                    </div>
	                </div>
	            </div>
				<div class="no-loginBtnBox">
					<i class="login-icon"></i>
					<a href="javascript:;" class="login_hp">登录</a><span>|</span><a href="javascript:;" class="reg_hp">注册</a>
				</div>
        	</div>
	    </div>
	</div>
</div>
