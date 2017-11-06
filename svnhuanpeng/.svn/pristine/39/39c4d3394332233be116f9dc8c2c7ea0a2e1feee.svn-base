<?php

    require('../../include/init.php');

    function getLiveList($db)
	{
		$sql = "select * from live where status=".LIVE;
		$res = $db->query($sql);
		if (!$res )
		{
			$t = 'Query Error (' . $db->errno() . ') '. $db->errstr();
			mylog($t);
			return false;
		}
		if (!$res->num_rows) return false;
		$ret = array();
		while ($row = $res->fetch_assoc())
			$ret[] = $row;
		return $ret;
	}

    $db = new DBHelperi_huanpeng();
    $lives = getLiveList($db);

	if(is_array($lives)){
		foreach ($lives as $live)
		{
			list($server, $dummy) = explode('/', $live['server']);
			echo "{$live['stream']} {$server}\n";
		}
	}

    echo "getAllLiveList\n";
    exit;

?>