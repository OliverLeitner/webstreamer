<?php
/**
 * stopping a running encoding
 */
require_once "../loader.php";
$file=escapeshellcmd($_GET['file']);
$name=escapeshellcmd(md5($_SERVER['REMOTE_ADDR'].$_GET['name']));
passthru("kill -9 $(ps aux | grep '".$name."' |awk '{print $2}') 2>&1",$returnval);
echo "command stopped";
