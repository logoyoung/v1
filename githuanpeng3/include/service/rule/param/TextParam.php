<?php
namespace service\rule\param;

class TextParam
{

    private $_param = [
        'accessKey' => 'ZpG1mFofk3TGp6BtUYDZ',
        'type'      => 'ZHIBO',
        'data' => [
            'channel' => 'COMMENT',
            'role'    => 'USER',
        ],
    ];

    public function setAccessKey($accessKey)
    {
      $this->_param['accessKey'] = $accessKey;
      return $this;
    }

    public function setType($type)
    {
      $this->_param['type'] = $type;
      return $this;
    }

    /**
     * 要检测的文本
     * @param string $text [description]
     */
    public function setText($text)
    {
      $this->_param['data']['text'] = $text;
      return $this;
    }

    public function getText()
    {
        return $this->_param['data']['text'];
    }

    /**
     *  用户唯一标识，用于用户行为分析，建议采用用户的账号
     * @param int |string
     */
    public function setTokenId($tokenId)
    {
      $this->_param['data']['tokenId'] = $tokenId;
      return $this;
    }

    /**
     *  发帖人的昵称，我们发现几乎所有平台的恶意用户都会通过昵称散播垃圾文本，
     *  为了彻底地拦截垃圾信息，请务必传递此参数
     * @param string $nickname
     */
    public function setNickname($nickname)
    {
      $this->_param['data']['nickname'] = $nickname;
      return $this;
    }

    /**
     *
     *  用户客户端IP，用于用户行为分析。相比tokenId，IP更难被更换，恶意用户很容易注册使用多个不同的账户进行恶意行为，
     *  但使用不同IP进行恶意行为的成本高很多，根据IP就能发现不同账户同一IP的恶意行为，同时，可用于比对数美IP黑名单，因此建议传递
     * @param string $ip [description]
     */
    public function setIp($ip)
    {
      $this->_param['data']['ip'] = $ip;
      return $this;
    }

    /**
     * 头像ocr识别出来的文字，可以联合文本一起对黑产进行拦截。
     * @param   string $nickocr [description]
     * @return           [description]
     */
    public function setNickocr($nickocr)
    {
      $this->_param['data']['nickocr'] = $nickocr;
      return $this;
    }

    /**
     * 文本渠道
     * COMMENT：直播间评论和弹幕 DYNAMIC_COMMENT：动态评论及个人发帖
     * NICKNAME:昵称变更
     * GROUP_CHAT：全局群聊
     * TEAM_CHAT：战队群聊
     * MESSAGE：私信聊天
     * PROFILE：描述变更
     * SIGNATURE：签名变更
     * THEME： 主题变更
     * PRODUCT：商品描述
     *  默认值为COMMENT
     * @param string $channel [description]
     */
    public function setChannel($channel)
    {
      $this->_param['data']['channel'] = $channel;
      return $this;
    }

    public function getChannel()
    {
        return isset($this->_param['data']['channel']) ? $this->_param['data']['channel'] : false;
    }

    /**
     *   发帖人角色
     * 针对不同的角色有不同的控制策略。在直播领域“ADMIN”表示房管，
     *      “HOST”表示主播，
     *      “SYSTEM”系统角色；
     *      在游戏领域“ADMIN”表示管理员，
     *      “USER”表示普通用户。
     *      缺失或者“USER”默认普通用户。
     *
     * @param string $role [description]
     */
    public function setRole($role)
    {
      $this->_param['data']['role'] = $role;
      return $this;
    }

    /**
     *   私信中两者是否是好友关系
     *   针对好友与否有不同的控制策略。
     *   “stranger”表示两人为非好友，
     *   “friend”表示两人为好友关系
     * @param srring $relationship [description]
     */
    public function setRelationship($relationship)
    {
      $this->_param['data']['relationship'] = $relationship;
      return $this;
    }

    /**
     *   标题信息
     * @param string $title [description]
     */
    public function setTitle($title)
    {
      $this->_param['data']['title'] = $title;
      return $this;
    }

    /**
     * 直播间编号
     *  可针对单个房间制定不同的策略，比如黑名单等。
     * @param string $room
     */
    public function setRoom($room)
    {
      $this->_param['data']['room'] = $room;
      return $this;
    }

    /**
     *  数美设备指纹标识
     *  由于用户行为分析。相比mac、imei等，数美设备指纹更难被篡改
     *  当恶意用户篡改mac、imei等设备信息时，使用deviceId能够发现和识别此类恶意行为，
     *  同时，可用于比对数美设备黑名单，因此建议传递
     * @param  string  $deviceId [description]
     * @return [type]           [description]
     */
    public function setDeviceId($deviceId)
    {
      $this->_param['data']['deviceId'] = $deviceId;
      return $this;
    }

    /**
     * 注册使用的手机号，用于用户行为分析，可用于比对数美恶意手机号库
     * @param string $phone [description]
     */
    public function setPhone($phone)
    {
      $this->_param['data']['phone'] = $phone;
      return $this;
    }

    /**
     * 用户android设备唯一标识，用于用户行为分析
     * @param string $imei [description]
     */
    public function setImei($imei)
    {
      $this->_param['data']['imei'] = $imei;
      return $this;
    }

    /**
     * 用户android设备唯一标识，
     * 用于用户行为分析
     * @param string $mac [description]
     */
    public function setMac($mac)
    {
      $this->_param['data']['mac'] = $mac;
      return $this;
    }

    /**
     * 用户ios应用唯一标识，用于用户行为分析。
     * @param  string $idfv [description]
     * @return [type]       [description]
     */
    public function setIdfv($idfv)
    {
      $this->_param['data']['idfv'] = $idfv;
      return $this;
    }

    /**
     * 用户ios应用唯一标识，用于用户行为分析。
     * @param [type] $idfa [description]
     */
    public function setIdfa($idfa)
    {
      $this->_param['data']['idfa'] = $idfa;
      return $this;
    }

    public function getParam()
    {
      return hp_json_encode(array_values_to_string($this->_param));
    }

}