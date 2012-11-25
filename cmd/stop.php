<?php
//killing running ffmpeg processes per file...
//this receives the var from the call...
include_once("../functions/functs.php");
include_once("../config.php");

$file=escapeshellcmd($_GET['file']);
$name=escapeshellcmd($_GET['name']);

passthru("kill -9 $(ps aux | grep '".$name."' |awk '{print $2}') |sudo /etc/init.d/crtmpserver restart",$returnval);
?>
