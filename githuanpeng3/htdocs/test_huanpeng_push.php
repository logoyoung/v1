<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/2/9
 * Time: 15:26
 */

exit();

    $data = [
				'prod'=>51,
                'tk'=>'6ed1d241fb5edeac82612e296c0055f5a3053bb562721ac93479badcdadf8e22',
//				'tk'=>'ee514c4241fbac806e91960c8740b52b01cdefa0d9d5bae99e7d23d1fde6c007',
				'content'=>'123123',
				'mid' => '2240-1885-'.time(),
				'title'=>'查看',
				'custom'=>'',
				'image'=>'Default.png',
				'sound'=>'default'
			];

$url = 'http://applepie/push.php?'.http_build_query($data);
$url = 'http://applepie/push.php?prod=51&tk=6ed1d241fb5edeac82612e296c0055f5a3053bb562721ac93479badcdadf8e22&content=%E4%B8%BB%E6%92%AD%3A%E5%AE%89%E5%8D%93%E6%AC%A2%E6%9C%8B%E7%AC%AC%E4%B8%80%E5%AF%8C+%E5%BC%80%E5%A7%8B%E7%9B%B4%E6%92%AD%E5%95%A6%EF%BC%8C%E5%BF%AB%E7%82%B9%E5%89%8D%E5%8E%BB%E5%9B%B4%E8%A7%82%E5%90%A7%7E&mid=2240-1885-1486631666&title=%E6%9F%A5%E7%9C%8B&custom=%7B%22type%22%3A%221%22%2C%22data%22%3A%7B%22luid%22%3A%222240%22%2C%22nick%22%3A%22%5Cu5b89%5Cu5353%5Cu6b22%5Cu670b%5Cu7b2c%5Cu4e00%5Cu5bcc%22%2C%22pic%22%3A%22http%3A%5C%2F%5C%2Fimg.huanpeng.com%5C%2F%5C%2FuserPic%5C%2F2240%5C%2F4e92107ef9290c5931e61b3e042f3d2c.png%22%7D%7D&image=Default.png&sound=default';
echo $url;

var_dump(`curl -Ss '$url'`);
var_dump(file_get_contents($url));