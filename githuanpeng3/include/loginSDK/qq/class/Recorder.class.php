<?php
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 */

require_once(CLASS_PATH."ErrorCase.class.php");
class Recorder{
    private static $data;
    private $inc;
    private $error;
	private $redis = null;

	const REDIS_DATA_EXPIRE = 7200;

    public function __construct(RedisHelp $redis = null){
        $this->error = new ErrorCase();

		if(!$redis)
			$this->redis = new RedisHelp();
		else
			$this->redis = $redis;

        //-------读取配置文件
        $incFileContents = file(ROOT."comm/inc.php");
        $incFileContents = $incFileContents[1];
        $this->inc = json_decode($incFileContents);
        if(empty($this->inc)){
            $this->error->showError("20001");
        }

		$_SESSION['QC_userData'] = $this->getQC_userData($_GET['state']);

        if(empty($_SESSION['QC_userData'])){
            self::$data = array();
        }else{
            self::$data = $_SESSION['QC_userData'];
        }
		$this->delQc_userData($_GET['state']);
    }

    public function write($name,$value){
        self::$data[$name] = $value;
    }

    public function read($name){
        if(empty(self::$data[$name])){
            return null;
        }else{
            return self::$data[$name];
        }
    }

    public function readInc($name){
        if(empty($this->inc->$name)){
            return null;
        }else{
            return $this->inc->$name;
        }
    }

    public function delete($name){
        unset(self::$data[$name]);
    }

    public function getQC_userData($stat){
		if(!$stat)
			return array();
		$result = $this->redis->get($this->getQC_redisKey($stat));
		if($result){
			return json_decode($result, true);
		}

		return array();
	}

	public function setQC_userData($data){
		if(!empty($data)){
			$this->redis->set($this->getQC_redisKey($data['state']), json_encode($data), self::REDIS_DATA_EXPIRE);
		}
	}


	public function delQc_userData($stat){
		if(!$stat)
			return true;
		return $this->redis->del($this->getQC_redisKey($stat));
	}

	public function getQC_redisKey($stat){
		return "qq_conn:".$stat;
	}

    function __destruct(){
        $_SESSION['QC_userData'] = self::$data;
		$this->setQC_userData(self::$data);
    }
}
