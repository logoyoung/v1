<?php

/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/28
 * Time: 下午8:38
 */
class UserStatistic
{
    const bill_cost = 0;
    const bill_recharge = 1;


    const d_start = " 00:00:00";
    const d_end = " 23:59:59";

    private $today_start;
    private $today_end;

    private $yest_start;
    private $yest_end;

    private $db;

    public function __construct($db)
    {
        $this->today_start = date("Y-m-d") . self::d_start;
        $this->today_end = date("Y-m-d") . self::d_end;

        $this->yest_start = date("Y-m-d", strtotime('-1 day')) . self::d_start;
        $this->yest_end = date("Y-m-d", strtotime('-1 day')) . self::d_end;
        if ($db)
            $this->db = $db;
        else
            $this->db = new DBHelperi_admin();
    }


    public function newUsers($from, $to)
    {
        $sql = "select count(*) as num from userstatic where rtime between '$from' and '$to'";
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        return (int)$row['num'];
    }

    public function newCustomers($from, $to)
    {
        return 213;
    }

    public function totalRecharge($from, $to)
    {
        $sql = "select sum(purchase) as money from billdetail where ctime between '$from' and '$to' and type = " . self::bill_recharge;
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        return (int)$row['money'];
    }

    public function totalConsumption($from, $to)
    {
        $sql = "select sum(purchase) as money from billdetail where ctime between '$from' and '$to' and type =" . self::bill_cost;
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        return (int)$row['money'];
    }

    public function todayNewUsers()
    {
        return $this->newUsers($this->today_start, $this->today_end);
    }

    public function todayNewCustomers()
    {
        return $this->newCustomers($this->today_start, $this->today_end);
    }

    public function todayTotalRecharge()
    {
        return $this->totalRecharge($this->today_start, $this->today_end);
    }

    /*what is the consumption ?? only sum the send gift?*/
    public function todayTotalConsumption()
    {
        return $this->totalConsumption($this->today_start, $this->today_end);
    }

    public function yestNewUsers()
    {
        return $this->newUsers($this->yest_start, $this->yest_end);
    }

    public function yestNewCustomers()
    {
        return $this->newCustomers($this->yest_start, $this->yest_end);
    }

    public function yestTotalRecharge()
    {
        return $this->totalRecharge($this->yest_start, $this->yest_end);
    }

    public function yestTotalConsumption()
    {
        return $this->totalConsumption($this->yest_start, $this->yest_end);
    }
}