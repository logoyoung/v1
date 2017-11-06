<?php
/*
 * 读取访问日志插入数据库
 */
namespace Cli\Controller;

class ViewRecordController extends \Think\Controller
{
   
   /**
    * 定时执行插入日志数据
    * 每小时执行一次
    */ 
   public function timingInsertData()
   {
        $dir = ["/data/logs/118/", "/data/logs/119/"];
        $dao_view = D("Userviewrecord");
        $dao_device = D("Userdevice");
		$dao_channel = D("channelUser");
        $date = date('Ymd H:00:00', strtotime('-1 hour'));
        foreach($dir as $k=>$v) {
            $name = 'device-' . $date . '.log';
            $path = $v . $name;
            if(file_exists($path)) {
                $log = fopen($path, "r");
                while(!feof($log)) {
                    $line = trim(fgets($log));
                    if($line != '' || $line != '[]') {
                        $data = json_decode($line, true);
						if(!isset($data['udid'])) {
							continue;
						}
                        $data['md5'] = md5($line);
                        //如果有重复的就更新下udid防止报错退出
                        $dao_view->add($data, array('udid'=>$data['udid']), true);
                        //var_dump($dao->_sql());
                        
						//插入设备表
						$deviceData = array(
							'channel' => $data['channel'],
							'udid' => $data['udid'],
							'ctime' => $data['ctime'],
							'cdate' => date('Ymd', strtotime($data['ctime'])),
							'deviceModel' => $data['deviceModel']
						);
                        $sql = 'INSERT INTO ' . $dao_device->getTableName() 
                            . joinInsertSql($deviceData) . ' ON DUPLICATE KEY UPDATE `udid`="' . $data['udid'] . '"';
                        $dao_device->execute($sql);
						
                        /*
						//插入用户来源渠道表
                        if($data['channel'] != 0) {
    						if($data['uid']) {
    							$channelData = array(
    								'uid' => $data['uid'],
    								'channel' => $data['channel'],
    								'ctime' => $data['ctime']
    							);
    							$sql = 'INSERT INTO ' . $dao_channel->getTableName() 
    								. joinInsertSql($channelData) . 'ON DUPLICATE KEY UPDATE `uid`="' . $channelData['uid'] . '"';
    							$dao_channel->execute($sql);
    						}
                        }
                        */
                    }
                }
            } else {
                //文件不存在，写日志记录
                file_put_contents('/data/logs/viewlog.log', '缺少日志文件-' . $v . $name.chr(10), FILE_APPEND);
            }
        }
   }
   
   /**
    * 定时执行插入日志数据
    * 每小时执行一次
    */ 
   public function insertData()
   {
        $dir = ["/data/logs/118/", "/data/logs/119/"];
        $dao_view = D("Userviewrecord");
        $dao_device = D("Userdevice");
		$dao_channel = D("channelUser");
		for($i = 80; $i>=1; $i--) {
        $date = date('Ymd H:00:00', strtotime('-'.$i.' hour'));
        foreach($dir as $k=>$v) {
            $name = 'device-' . $date . '.log';
            $path = $v . $name;
            if(file_exists($path)) {
                $log = fopen($path, "r");
                while(!feof($log)) {
                    $line = trim(fgets($log));
                    if($line != '' || $line != '[]') {
                        $data = json_decode($line, true);
						if(!isset($data['udid'])) {
							continue;
						}
                        $data['md5'] = md5($line);
                        //如果有重复的就更新下udid防止报错退出
                        $dao_view->add($data, array('udid'=>$data['udid']), true);
                        //var_dump($dao->_sql());
                        
						//插入设备表
						$deviceData = array(
							'channel' => $data['channel'],
							'udid' => $data['udid'],
							'ctime' => $data['ctime'],
							'cdate' => date('Ymd', strtotime($data['ctime'])),
							'deviceModel' => $data['deviceModel']
						);
                        $sql = 'INSERT INTO ' . $dao_device->getTableName() 
                            . joinInsertSql($deviceData) . ' ON DUPLICATE KEY UPDATE `udid`="' . $data['udid'] . '"';
                        $dao_device->execute($sql);
						
						//插入用户来源渠道表
						if($data['uid']) {
							$channelData = array(
								'uid' => $data['uid'],
								'channel' => $data['channel'],
								'ctime' => $data['ctime']
							);
							$sql = 'INSERT INTO ' . $dao_channel->getTableName() 
								. joinInsertSql($channelData) . 'ON DUPLICATE KEY UPDATE `uid`="' . $channelData['uid'] . '"';
							$dao_channel->execute($sql);
						}
                    }
                }
            } else {
                //文件不存在，写日志记录
                file_put_contents('/data/logs/viewlog.log', '缺少日志文件-' . $v . $name.chr(10), FILE_APPEND);
            }
        }
		}
   }
   
   /*
   public function insertData()
   {
        $dir = ["/data/logs/118/", "/data/logs/119/"];
        $dao = D("Userviewrecord");
        foreach($dir as $k=>$v) {
            $file = scandir($v);
            foreach($file as $kk=>$vv) {
                $path = $v.$vv;
                if(is_file($path)) {
                    $log = fopen($path, "r");
                    while(!feof($log)) {
                        $line = fgets($log);
                        if($line != '') {
                            $data = json_decode($line, true);
                            //早期一些日志没有设置ctime，取文件名中的日期
                            if(!isset($data['ctime'])) {
                                $data['ctime'] = explode('-', explode('.', $vv)[0])[1];
                            }
                            $data['md5'] = md5($line);
                            //如果有重复的就更新下udid防止报错退出
                            $dao->add($data, array('udid'=>$data['udid']), true);
                            //var_dump($dao->_sql());
                        }
                    }
                }
            }
        }
   }
   public function insertDeviceData()
   {
		$dao_view = D("Userviewrecord");
        $dao_device = D("Userdevice");
		$size = 1000;
		for($page = 0; $page < 1000; $page++) 
		{
			$results = $dao_view
				->limit(($page*$size).','.$size)
				->order('ctime')
				->field('udid,ctime,deviceModel,channel')
				->select();
			if(!$results) {
				break;
			}
			foreach($results as $k=>$v) {
				if(!isset($v['udid'])) {
					continue;
				}
                $sql = 'INSERT INTO ' . $dao_device->getTableName() 
                    . ' (`channel`,`udid`,`ctime`,`cdate`,`deviceModel`) 
						VALUES ("' . $v['channel'] . '","' . $v['udid'] . '","' . $v['ctime'] . '","' . date('Ymd', strtotime($v['ctime'])) . '","' . $v['devicemodel'] . '") 
						ON DUPLICATE KEY UPDATE `udid`="' . $v['udid'] . '"';
                $dao_device->execute($sql);
                //$dao->fetchSql(true);
                //$sql = $dao_device->add($insert, array('udid'=>$insert['udid']), true);
			}
		}
   }
   */
}
