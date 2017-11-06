<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/29
 * Time: 下午3:36
 */
include_once 'init.php';

class AreaSearch
{
    const db_ip = "admin_ip";
    const db_position = "admin_position";
    const db_user_site = "admin_user_position";

    private $db;

    public function __construct($db = null)
    {
        if ($db)
            $this->db = $db;
        else
            $this->db = new DBHelperi_admin();
    }

    public function searchIP($ip)
    {
        $sql = "select positionid from " . self::db_ip . " where ip = $ip";
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        return (int)$row['positionid'];
    }

    public function setIpPosition($ip, $positionid)
    {
        $sql = "insert into " . self::db_ip . "(ip, positionid) value($ip, $positionid)";

        return $this->db->query($sql);
    }

    public function searchPosition($c_id, $a_id, $r_id, $ct_id)
    {
        $sql = "select id from " . self::db_position . " where country_id='$c_id' and area_id=$a_id and region_id=$r_id and city_id=$ct_id";

        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        return (int)$row['id'];
    }

    public function setPositoon($c_id, $c_name, $a_id, $a_name, $r_id, $r_name, $ct_id, $ct_name)
    {
        $data = array(
            'country_id' => $c_id,
            'country' => $c_name,
            'area_id' => $a_id,
            'area' => $a_name,
            'region_id' => $r_id,
            'region' => $r_name,
            'city_id' => $ct_id,
            'city' => $ct_name
        );

        if ($this->db->insert(self::db_position, $data))
            return $this->db->insertID;

        return false;
    }

    public function isSetPosition($uid)
    {
        $sql = "select uid from " . self::db_user_site . " where uid='$uid'";
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        return (int)$row['uid'];
    }

    public function setUserPosition($uid, $positionid, $region_id)
    {
        $sql = "insert into " . self::db_user_site . "(uid, positionid, region_id) value($uid, $positionid, $region_id)";

        return $this->db->query($sql);
    }

    public function getUserRegionList(){
        $sql = "select region_id, region from ". self::db_position;
        $res = $this->db->query($sql);
        while($row = $res->fetch_assoc()){
            $ret[$row['region_id']] = $row['region'];
        }

        return $ret;
    }

}