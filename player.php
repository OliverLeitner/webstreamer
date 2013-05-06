<?php
ini_set("display_errors","On");
include_once("functions/functs.php");
include_once("templates/player.php");
include_once("config.php");

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
$tag = "<div id=\"css-poster\" data-ratio=\"1.0\" class=\"flowplayer minimalist is-splash\" data-rtmp=\"rtmp://".$crtmpserver."/live\">
<video id=\"container1\" class=\"projekktor\" width=\"".$width."\" height=\"".$height."\" 
poster=\"".$thumbs_dir.$filename."_thumb.png\" 
title=\"".$title."\" controls>";

/*	global definitions for all other players but flowplayer	*/
if($name_cmd == $uid){
	$file_src = "rtmp://".$crtmpserver."/live/".$name_cmd;
  	$tag .= "<source src=\"".$name_cmd."\" type=\"".$type."\" />";
} else {
	$file_src = "http://".$storageserver.":".$storageport."/".$name_cmd;
       	$tag .= "<source src=\"".$file_src."\" type=\"".$type."\" />";
}
$tag .= "</video></div>";

/*	flowplayer definitions	*/
if($player == "flowplayer"){
	$style = "<link rel=\"stylesheet\" href=\"".$js_dir."flowplayer/skin/minimalist.css\" type=\"text/css\" media=\"screen\" />
	<style>
	.flowplayer {
		width: ".$width."px;
		height: ".$height."px;
	}
	</style>";

	$headscript = "<script src=\"".$js_dir."flowplayer/flowplayer.min.js\"></script>";

	$contentscript ="
		<script>
			$('#container1').flowplayer({
   				swf: 'scripts/flowplayer/flowplayer.swf',
   				rtmp: 'rtmp://".$crtmpserver."/live'
			});
		</script>
	";
}

/* jwplayer and default defintions	*/
if($player == "jwplayer" || !isset($player)){
	$style = "";

	$headscript = "<script src=\"".$js_dir."jwplayer/jwplayer.js\"></script>";

	//if we are registered for statistics on jwplayer homepage...
	if($jw_key != ""){
		$headscript .= "<script>jwplayer.key=\"".$jw_key."\"</script>";
	}

	$contentscript = "
	<script>
		jwplayer('container1').setup({
    		streamer: 'rtmp://".$crtmpserver."/live',
    		provider: 'rtmp',
    		allowscriptaccess: 'always',
    		file: '".$file_src."',
		image: '".$thumbs_dir.$filename."_thumb.png',
    		width: '".$width."',
    		height: '".$height."',
    		autostart: 'false',
    		'rtmp.subscribe': 'false',
    		'modes': [
            	{type: 'html5'},
            	{type: 'flash', src: 'scripts/jwplayer/jwplayer.flash.swf'},
            	{type: 'download'}
    		],
    		primary: 'html5'
		});
	</script>
	";
}

/*	projekktor definitions	*/
if($player == "projekktor"){
	$style = "<link rel=\"stylesheet\" href=\"".$js_dir."projekktor/theme/style.css\" type=\"text/css\" media=\"screen\" />";
	$headscript = "<script type=\"text/javascript\" src=\"".$js_dir."projekktor/projekktor-1.2.24r229.min.js\"></script>";
	$headscript .= "
	<script type=\"text/javascript\">
		$(document).ready(function() {
			projekktor('#container1', {
    				volume: 0.8,
    				playerFlashMP4: '".$js_dir."projekktor/jarisplayer.swf',
    				playerFlashMP3: '".$js_dir."projekktor/jarisplayer.swf',
    				title: '".$title."',
    				controls: true,
    				playlist: [{
					0:{src:'".$name_cmd."',type:'".$type."'},
					config:{streamType:'rtmp', streamServer:'rtmp://".$crtmpserver."/live'}
				}]
			});
		});
	</script>";
}

/*	writing our template... */
$body = doPlayer($style_main,$style,$headscript,$title,$tag,$contentscript,$js_dir,$jq_dir);
echo $body;
?>
