<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 17/2/26
 * Time: 下午12:19
 */
class Anchor
{
    private $uid;
    private $db;

    function __construct($db = null){
        if ($db){
            $this->db = $db;
        } else{
            $this->db = new DB();
        }
        return true;
    }

    /**批量获取用户昵称头像
     * @param $uids  用户id列表
     * @param $db
     */
 function anchorInfo($uids){
     if(is_array($uids)){
        $uids=implode(',',$uids);
     }
     $res=$this->db->field("uid,nick,pic")->where("uid in ($uids)")->select('userstatic');
     $list=array();
	 if(false !==$res){
         
         if(!empty($res)){
            foreach ($res  as $v){
                $list[$v['uid']]=$v;
            }
         }
         
     }
	 return $list;
 }

    /**批量获取主播房间id
     * @param $uids  主播id列表
     * @return array|bool
     */
 function  anchorRoomID($uids){
     if(is_array($uids)){
         $uids=implode(',',$uids);
     }
     $res=$this->db->field("uid,roomid")->where("uid in ($uids)")->select('roomid');
     if(false !==$res){
         $list=array();
         if(!empty($res)){
             foreach ($res  as $v){
                 $list[$v['uid']]=$v;
             }
         }
         return $list;
     }else{
         return array();
     }
 }

    /**批量获取主播在线时长
     * @param $uids
     * @param $month 月份  2017-02
     * @return array|bool
     */
 function  anchorLiveLength($uids,$month){
     if(empty($uids) || empty($month)){
         return false;
     }
     if(is_array($uids)){
         $uids=implode(',',$uids);
     }
     $mstart=$month.'-01';
     $mend=$month.'-31';
     $res=$this->db->field("uid,sum(length) as length")->where("uid in ($uids)  and date >='$mstart' and date <='$mend'  group by uid")->select('live_length');
     if(false !==$res){
          if(!empty($res)){
              foreach ($res as $v){
                  $list[$v['uid']]=$v;
              }
              return $list;
          }else{
              return array();
          }
     }else{
         return array();
     }
 }

    /**批量获取主播人气
     * @param $uids
     * @param $month
     * @return array|bool
     */
    function  anchorPopular($uids,$month){
        if(empty($uids) || empty($month)){
            return false;
        }
        if(is_array($uids)){
            $uids=implode(',',$uids);
        }
        $mstart=$month.'-01 00:00:00';
        $mend=$month.'-31 23:59:59';
        $res=$this->db->field("uid,max(popular) as popular")->where("uid in ($uids)  and ctime >='$mstart' and ctime <='$mend' group  by uid ")->select('anchor_most_popular');
        if(false !==$res){
            if(!empty($res)){
                foreach ($res as $v){
                    $list[$v['uid']]=$v;
                }
                return $list;
            }else{
                return array();
            }
        }else{
            return array();
        }
    }

}