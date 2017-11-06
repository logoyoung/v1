alter table gift add thumb_poster varchar(255) not null default '' comment'送礼礼物展示图片'
alter table gift add thumb_poster_3x varchar(255) not null default '' comment'送礼礼物展示图片'

-- DELETE FROM gift_config_detail WHERE config_id = 1;
--
-- INSERT INTO gift_config_detail (gift_id,config_id,num,`order`) VALUES
-- (32,1,1,0),
-- (39,1,1,1),
-- (38,1,1,2),
-- (37,1,1,3),
-- (36,1,1,4),
-- (33,1,1,5),
-- (34,1,1,6),
-- (35,1,1,7),
-- (31,1,100,8),
-- (31,1,520,9)