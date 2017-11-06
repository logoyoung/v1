<?php

/**
 * ���ֱ������
 * yandong@6rooms.com
 * date 2016-10-20 17:15
 * 
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

/**
 * ���ͨ�� ����live���е��ֶ�
 * @param type $row
 * @param type $db
 * @return boolean
 */
function changeLiveTitle($row, $db) {
    if (empty($row)) {
        return false;
    }
    foreach ($row as $v) {
        $data[$v['liveid']] = $v['title'];
    }
    $ids = implode(',', array_keys($data));
    $sql = "UPDATE live SET title = CASE liveid ";
    foreach ($data as $id => $nick) {
        $sql .= "WHEN $id THEN '$nick' ";
    }
    $sql .= "END WHERE liveid IN ($ids)";
    $res = $db->query($sql);
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * �����˶�������admin_wait_live_title���Ӧ����
 * @param string $liveid  //ֱ��id
 * @param int $adminid  //�����id
 * @param type $db
 * @return boolean
 */
function upWaitLiveTitle($liveid, $adminid, $db) {
    if (empty($liveid) || empty($adminid)) {
        return false;
    }
    $res = $db->where("liveid in ($liveid) and  adminid=$adminid")->update('admin_wait_live_title', array('status' =>1));
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * ����δͨ����
 * @param string $failluid  δͨ���û�uid ����ö��Ÿ���
 * @param type $db
 * @return boolean
 */
function failLiveTitle($failluid, $uid, $db,$status) {
    $row = $db->field('liveid,title')->where("liveid in ($failluid)")->select('admin_live_title');
    if (!empty($row) && (false !== $row)) {
        $update = $db->where("liveid in ($failluid)")->update('admin_live_title', array('status' => $status, 'utime' => date('Y-m-d H:i:s', time())));
        if ($update !== false) {
            $res = upWaitLiveTitle($failluid, $uid, $db);
            if ($res) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * ֱ���������
 * @param int $uid  //�����id
 * @param type $succluid  //��˳ɹ�
 * @param type $failluid  //���ʧ��
 * @param type $db
 * @return boolean
 */
function UpdateLiveTitleStatus($uid, $succluid, $failluid, $db) {
    if (empty($succluid) && empty($failluid)) {
        return false;
    }
    $checkTitleMode=checkMode(CHECK_TITLE,$db);//
    if($checkTitleMode){
         //�ȷ�����
        $succStatus=LIVE_TITLE_AUTO_PASS;
        $fileStatus=LIVE_TITLE_AUTO_UNPASS;
    }else{
        //�����
        $succStatus=LIVE_TITLE_PASS;
        $fileStatus=LIVE_TITLE_UNPASS;
    }
    if ($succluid) {
        $row = $db->field('liveid,title')->where("liveid in ($succluid)")->select('admin_live_title');
        if (!empty($row) && (false !== $row)) {
            $update = upWaitLiveTitle($succluid, $uid, $db); //����admin_wait_user_nick��״̬
            if ($update !== false) {
                $tostatic = $db->where("liveid in ($succluid) ")->update('admin_live_title', array('status' => $succStatus, 'utime' => date('Y-m-d H:i:s', time()))); //�޸����״̬ͨ��
                if ($tostatic !== false) {
                    if ($failluid) {
                        failLiveTitle($failluid, $uid, $db,$fileStatus); //����δͨ����
                    }
                    changeLiveTitle($row, $db); //����live��
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        if ($failluid) {
            $res = failLiveTitle($failluid, $uid, $db,$fileStatus); //ǰһ��Ӧ���ж�������󷢻����ȷ�����
            if ($res) {
                return true;
            } else {
                return false;
            }
        }
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 1;
$succluid = isset($_POST['succList']) ? trim($_POST['succList']) : ''; //����id�б��������ö��Ÿ���(��ͨ����)
$failluid = isset($_POST['failedList']) ? trim($_POST['failedList']) : ''; //����id�б��������ö��Ÿ���(���ϸ��)
if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}
if (empty($succluid) && empty($failluid)) {
    error(-1007);
}

$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

$res = UpdateLiveTitleStatus($uid, $succluid, $failluid, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}