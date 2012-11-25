<?php
//killing running ffmpeg processes per file...
//this receives the var from the call...
include_once("../functions/functs.php");
include_once("../config.php");

$file=escapeshellcmd($_GET['file']);
$name=escapeshellcmd(md5($_SERVER['REMOTE_ADDR'].$_GET['name']));

//echo $name;
//die();

passthru("kill -9 $(ps aux | grep '".$name."' |awk '{print $2}')",$returnval);
?>
