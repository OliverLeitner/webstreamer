<?php
/**
 * stopping a running encoding
 */
require_once "../loader.php";
$params['uid'] = md5($_SERVER['REMOTE_ADDR'].$_GET['name']);
$cmd = buildCmd($params,$commands['stop_avconv']);
passthru($cmd,$returnval);
echo "command stopped";
