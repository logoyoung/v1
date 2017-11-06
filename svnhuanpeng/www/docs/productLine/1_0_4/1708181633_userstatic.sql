### 添加索引
ALTER TABLE `userstatic` ADD KEY  phone(`phone`) ;
### 无法添加
ALTER TABLE `three_side_user` ADD  KEY  unionid (`unionid`) ;