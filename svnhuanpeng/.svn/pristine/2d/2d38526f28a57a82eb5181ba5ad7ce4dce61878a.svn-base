<?php

include '../../../include/init.php';
use service\live\LiveService;


function hlsStream( $stream )
{
    if( empty( $stream ) )
    {
        return '';
    }
    else
    {
        $st="liverecord/".$stream;
        $iparam = createHlsSecret($st);
        return  $stream . '?' . $iparam;
    }
}

/**
 * start
 */
$luid = isset($_POST['luid']) ? (int) $_POST['luid'] : " ";
if ( empty($luid) )
{
    error2(-4013);
}
$luid = checkInt($luid);
getLiveServerList($streamServer, $notifyServer);
$liveService = new LiveService();
$liveService->setCaller('api:'.__FILE__.';line:'.__LINE__);
$liveService->setLuid($luid);
$row         = $liveService->getLastLive();
if (!$liveService->isLiving())
{
    $orientation = '';
    $stream = '';
    $liveId='';
    $streamServer = array();
}
else
{
    $orientation = $row['orientation'];
    $stream = hlsStream( $row['stream'] );
    $liveId=$row['liveid'];
}
render_json(array('streamList' => array($streamServer), 'orientation' => $orientation, 'stream' => $stream,'liveID'=>$liveId));