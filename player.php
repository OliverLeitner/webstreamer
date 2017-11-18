<?php
ini_set("display_errors","On");
error_reporting(E_ALL);
include("functions/functs.php");
include("templates/player.php");
include("config.php");

//initialize memcached support if possible
$m = "";
if(isset($memcservers) && $memcservers != ""){
	$m = init_memcache($memcservers,":"); 
}

//grab the full filepath...
$name = addslashes(ltrim(urldecode(str_replace("..", "", $_GET['name'])),"/"));
$type = $_GET['type'];
$title = urldecode($_GET['name']);
//for multiuser support we use client ip and chosen file
$uid = md5($_SERVER['REMOTE_ADDR'].$_GET['file']);

//commandline testing if we got our rtmp stream running or not
$cmd = "ps auxf |grep {$uid} |awk '{ print $13 }' |grep avconv";
$name_cmd = exec($cmd);

//reading the existing thumbnail for the file...
$filename = preg_replace("/[^A-Za-z0-9\_\-\.]/","",current(explode(".",$_GET['file'])));

//reading out the duration of a clip to have a scrollbar...
$fp = fopen($meta_dir.$filename.".txt","r");
$data = fread($fp,filesize($meta_dir.$filename.".txt"));

if($m != ""){
	$m->set('data',$data);
	$data = $m->get('data');
}

fclose($fp);
preg_match("#Duration: (.+), start#",$data,$duration);
$parsed = date_parse(trim($duration[1]));
$seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];

//check if we are streaming or not streaming...
if($name_cmd == ""){
	$name_cmd = $name;
} else {
	$name_cmd = $uid;
}

/* global vars possible to set... */
$style = "";
$headscript = "";
$contentscript = "";
$tag = "<div id=\"css-poster\" class=\"player minimalist is-splash\" data-rtmp=\"rtmp://".$crtmpserver.":".$crtmp_out_port."/flvplayback\" data-engine=\"flash\"><video id=\"container1\" class=\"player projekktor\" poster=\"".htmlentities($thumbs_dir.$filename)."_thumb.png\" data-engine=\"html5\" width=\"".$width."\" height=\"".$height."\" title=\"".htmlentities($title)."\" controls>";

/* global definitions for all other players but flowplayer */
if($name_cmd == $uid){
	$long_src = "rtmp://".$crtmpserver.":".$crtmp_out_port."/flvplayback/".$name_cmd;
	$short_src = $name_cmd;
	$default_src = "rtmp://".$crtmpserver.":".$crtmp_out_port."/flvplayback/".$name_cmd;

	$tag .= "<source src=\"".htmlentities($long_src)."\"/>";
	/*	load the desired player	*/
	if($player == "clappr" || !isset($player)){
		include_once("players/clappr/player.php");
		$clappr_tag = '<div id="container1"></div>';
	} else {
		include_once("players/".$player."/player.php");
	}
} else {
	$long_src = "http://".$storageserver.":".$storageport."/".$name_cmd;
	$short_src = $long_src;
	$default_src = $long_src;

	$tag .= "<source src=\"".htmlentities($long_src)."\" type=\"".htmlentities($type)."\" />";
}
$tag .= "</video></div>";

//special case for clappr player, cause he hates the video tag
//in this version...
if(($player == "clappr" || !isset($player)) && $name_cmd == $uid)
{
	$tag = $clappr_tag;
}

/*	writing our template... */
$body = doPlayer($style_main,$style,$headscript,$title,$tag,$contentscript,$js_dir);
echo $body;
