<?php
/**
 * 
 *   状态配置文件
 *   
 *   */
function hp_conf($key){
    $conf = array(
        //录像合成错误状态
        'videomerge_err'              =>     0,
        'videomerge_empty'            =>     1,
        'videomerge_downerr'          =>     2,
        'videomerge_liveiderr'        =>     3,
        
        //录像合成状态
        'videomerge_suc'              =>     1,
        'videomerge_err'              =>     0, 
        
        //TODO
        
    );
    
    return $conf[$key]?$conf[$key]:'';
}
