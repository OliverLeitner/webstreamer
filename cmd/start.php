<?php
//enable all error output weve got...
error_reporting("E_ALL");
ini_set("display_errors","on");

//this receives the var from the call...
include_once("../functions/functs.php");
include_once("../config.php");

$file=$_GET['file'];
$name=$_GET['name'];

//$init="sudo /etc/init.d/crtmpserver restart &&";
//$init="sudo sed -i '/Stream available/ d' /var/log/crtmpserver/main.log &&";

$source="avconv -re -i '{$file}'";
$target="tcp://127.0.0.1:6666?pkt_size=1613";

//$presets = "-threads 2";
$presets = "";

//$audio="-acodec libfaac -ab 12k -aq 6 -ar 48000 -ac 1 -async 2";
$audio="-acodec aac -ab 12k -aq 6 -ar 48000 -ac 1";

//$video="-vcodec libx264 -intra -bufsize {$buffer}k -maxrate {$buffer}k -minrate 200k -bf 10 -s {$width}:{$height} -vf scale={$width}:{$height} -vbsf h264_mp4toannexb";
$video="-vcodec flv -b:v {$buffer}k -s {$width}x{$height} -strict experimental -g 25 -me_method zero";

$output = "-f flv -metadata streamName=".md5($_SERVER['REMOTE_ADDR'].$name);

passthru("{$init} {$source} {$presets} {$audio} {$video} {$output} '{$target}'",$returnval);
?>
