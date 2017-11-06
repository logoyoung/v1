<?php
$maxLevel = 60;
$source = '/Users/hantong/Downloads/userLevel';
$dest = '/Users/hantong/Sites/website/mobileGame/huanpeng/htdocs/main/static/img/level/user';

for($i = 29; $i <= $maxLevel; $i++){
    if($i < 30 ){
            $cmd = "cp $source/userlevel.png  $dest/1-30.png";
                }else{
                        $cmd = "cp $source/userlevelgif/$i.gif $dest/$i.gif";
                            }
                                `$cmd`;
                                }
                                $cmd ="svn add $dest";
                                `$cmd`;

                                $cmd = "svn ci -m ''";
                                `$cmd`;
                                ?>

