<?php
/**
 * stopping a running encoding
 */
require_once "../loader.php";
$params['uid'] = md5($_SERVER['REMOTE_ADDR'].$_GET['name']);
$check_running = buildCmd($params,$commands['ps_kill']);
$cmd = buildCmd($params,$commands['stop_avconv']);
while(1){
    passthru($cmd,$returnval);
    $check = exec($check_running);
    if($check != "avconv")
    {
        break;
    }
}
echo "command stopped";
