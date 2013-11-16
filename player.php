<?php
ini_set("display_errors","Off");
include("functions/functs.php");
include("templates/player.php");
include("config.php");

//define jquery full path
$jq_dir = $js_dir.$jquery;

//grab the full filepath...
$name = addslashes(ltrim(urldecode($_GET['name']),"/"));
$type = $_GET['type'];
$title = urldecode($_GET['name']);
$file = addslashes(urldecode($_GET['file']));
//for multiuser support we use client ip and chosen file
$uid = md5($_SERVER['REMOTE_ADDR'].$_GET['file']);

//commandline testing if we got our rtmp stream running or not
$cmd = "ps auxf |grep {$uid} |awk '{ print $13 }' |grep avconv";
$name_cmd = exec($cmd);

//creating thumbnail for the player on player load
$filename = preg_replace("/ /","_",$_GET["file"]);
$dirname = dirname("/".$name);
$cmd_thumb = "avconv -ss 00:3:00 -t 1 -i '/".$name."' -r 16 -qscale 1 -s 320x240 -f image2 '".$thumbs_dir.$filename."_thumb.png'";
if(!file_exists($thumbs_dir.$filename."_thumb.png")){
	exec($cmd_thumb);
	compress_image($thumbs_dir.$filename."_thumb.png", $thumbs_dir.$filename."_thumb.png", 60);
	gzcompress($thumbs_dir.$filename."_thumb.png");
}

//check if we are streaming or not streaming...
if($name_cmd == ""){
	$name_cmd = $name;
} else {
	$name_cmd = $uid;
}

/* global vars possible to set... */
$tag = "<div id=\"css-poster\" class=\"player minimalist is-splash\" data-rtmp=\"rtmp://".$crtmpserver."/live\" data-engine=\"flash\">
<video id=\"container1\" class=\"player projekktor\" poster=\"".$thumbs_dir.$filename."_thumb.png\" data-engine=\"html5\" width=\"".$width."\" height=\"".$height."\" title=\"".$title."\" controls>";

/* global definitions for all other players but flowplayer */
if($name_cmd == $uid){
	$long_src = "rtmp://".$crtmpserver."/live/flv:".$name_cmd;
	$short_src = $name_cmd;
	$default_src = "rtmp://".$crtmpserver."/live/".$name_cmd;

  	$tag .= "<source src=\"".$long_src."\" type=\"".$type."\" />";
} else {
	$long_src = "https://".$storageserver.":".$storageport."/".$name_cmd;
	$short_src = $long_src;
	$default_src = $long_src;
       	$tag .= "<source src=\"".$long_src."\" type=\"".$type."\" />";
}
$tag .= "</video>
</div>";

/*	load the desired player	*/
if(!isset($player)){
	include_once("players/projekktor/player.php");
} else {
	include_once("players/".$player."/player.php");
}

/*	writing our template... */
$body = doPlayer($style_main,$style,$headscript,$title,$tag,$contentscript,$js_dir,$jq_dir);
echo $body;
?>
