<?php
include_once("functions/functs.php");
include_once("templates/player.php");
include_once("config.php");

//grab the full filepath...
$name = ltrim($_GET['name'],"/");
$type = $_GET['type'];
$title = $_GET['name'];
$file = $_GET['file'];
//for multiuser support we use client ip and chosen file
$uid = md5($_SERVER['REMOTE_ADDR'].$_GET['file']);

//commandline testing if we got our rtmp stream running or not
$cmd = "ps auxf |grep {$uid} |awk '{ print $13 }' |grep avconv";
$name_cmd = exec($cmd);

//check if we are streaming or not streaming...
if($name_cmd == ""){
	$name_cmd = $name;
} else {
	$name_cmd = $uid;
}

/* global vars possible to set... */
$tag = "<div id=\"css-poster\" data-ratio=\"1.0\" class=\"flowplayer minimalist is-splash\" data-rtmp=\"rtmp://".$crtmpserver."/live/\">
<video id=\"container1\" class=\"projekktor\" poster=\"flash_thumb.jpg\" title=\"".$title."\" controls>";

/*	flowplayer_flash definitions 	*/
if($player == "flowplayer_flash"){
	$tag = "<div id=\"container1\" style=\"display:block;width:".$width."px;height:".$height."px;\">";

	$style = "";

	$headscript = "<script src=\"".$js_dir."flowplayer/flowplayer-3.2.11.min.js\"></script>";

	if($name_cmd == $uid){
		$contentscript = "
		<script language=\"JavaScript\">
		flowplayer('container1', '".$js_dir."flowplayer/flowplayer-3.2.15.swf', {
		plugins: {
        	rtmp: {
            	url: '".$js_dir."flowplayer/flowplayer.rtmp-3.2.11.swf',
            	netConnectionUrl: 'rtmp://".$crtmpserver."/live',
            	failOverDelay: 1000
        	}
		},
		clip: {
       		url: '".$name_cmd."',
       		provider: 'rtmp',
       		live: true,
			autoPlay: false,
			autoBuffering: true
		}
		});
		</script>";
	} else {
		$contentscript = "
		<script language=\"JavaScript\">
		flowplayer('container1', '".$js_dir."flowplayer/flowplayer-3.2.15.swf', {
		plugins: {
        	pseudo: {
            	url: '".$js_dir."flowplayer/flowplayer.pseudostreaming-3.2.11.swf'
        	}
		},
		clip: {
        	provider: 'pseudo',
			url: '".$name_cmd."',
			baseUrl: 'http://".$storageserver.":".$storageport."/',
			autoPlay: false,
			autoBuffering: true
		}

		});
		</script>";
	}
}

/*	global definitions for all other players but flowplayer_flash	*/
if($player != "flowplayer_flash"){
	if($name_cmd == $uid){
       	$tag .= "<source src=\"rtmp://".$crtmpserver."/live/".$name_cmd."\" type=\"".$type."\" />";
	} else {
       	$tag .= "<source src=\"http://".$storageserver.":".$storageport."/".$name_cmd."\" type=\"".$type."\" />";
	}
	$tag .= "</video></div>";
} else {
	$tag .= "</div>";
}

/*	flowplayer_html5 definitions	*/
if($player == "flowplayer_html5"){
	$style = "<link rel=\"stylesheet\" href=\"".$js_dir."flowplayer/skin/minimalist.css\" type=\"text/css\" media=\"screen\" />
	<style>
	.flowplayer {
		width: ".$width."px;
		height: ".$height."px;
	}
	</style>";

	$headscript = "<script src=\"".$js_dir."flowplayer/flowplayer.min.js\"></script>
	<script type=\"text/javascript\">
		flowplayer.conf.rtmp = 'rtmp://".$crtmpserver."/live';
		flowplayer.conf.splash = 'true';
		if (/flash/.test(location.search)) {
			flowplayer.conf.engine = 'flash';
			flowplayer.conf.swf = '".$js_dir."flowplayer/flowplayer.swf';
		}
	</script>";
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
	<script type=\"text/javascript\">
	$(document).ready(function() {
	var flashvars = {
        streamer:'rtmp://".$crtmpserver."/live',
        provider:'rtmp',
        allowscriptaccess:'always',
        file:'".$name_cmd."',
        autostart:'true',
        'rtmp.subscribe':'false',
        'modes': [
                {type: 'html5'},
                {type: 'flash', src: '".$js_dir."jwplayer/player.swf'},
                {type: 'download'}
        ]
	};
	var params = { allowfullscreen:'true', allowscriptaccess:'always', wmode:'opaque', stretching:'fill', controlbar:'over' };
	var attributes = { id:'player1', name:'player1' };
	swfobject.embedSWF('".$js_dir."jwplayer/player.swf','container1','".$width."','".$height."','9.0.115','false', flashvars, params, attributes);
	});
	</script>";
}

/*	projekktor definitions	*/
if($player == "projekktor"){
	$style = "<link rel=\"stylesheet\" href=\"".$js_dir."projekktor/theme/style.css\" type=\"text/css\" media=\"screen\" />";

	$headscript = "<script type=\"text/javascript\" src=\"".$js_dir."projekktor/projekktor-1.2.01r130.min.js\"></script>";

	$contentscript = "
	<script type=\"text/javascript\">
	$(document).ready(function() {
	projekktor('#container1', {
    	volume: 1.0,
    	playerFlashMP4: '".$js_dir."projekktor/jarisplayer.swf',
    	playerFlashMP3: '".$js_dir."projekktor/jarisplayer.swf',
    	title: '".$title."',
    	ID: 'playerID',
    	debug: 'false',
    	loop: 'false',
    	allowFullScreen: 'true',
    	wmode: 'opaque',
    	seamlesstabbing: 'false',
    	autoplay: 'false',
    	controls: 'true',
	height: '".$height."',
	width: '".$width."',
    	//poster: 'images/flash_thumb.jpg',
    	//cover: 'images/flash_thumb.jpg',
    	file: '".$name_cmd."',
    	streamType: 'rtmp',
    	streamServer: 'rtmp://".$crtmpserver."/live',
    	flashStreamType: 'rtmp',
    	flashRTMPServer: 'rtmp://".$crtmpserver."/live',
    	playbackQuality: 'large',
    	playlist: [
		{
			0:{src:'".$name_cmd."', type: '".$type."', quality: 'large', streamType: 'rtmp', streamServer: 'rtmp://".$crtmpserver."/live'}
		}
    ]
	});
	});
	</script>";
}

/*	writing our template... */
$body = doPlayer($style_main,$style,$headscript,$title,$tag,$contentscript,$js_dir,$js_dir.$jquery,$swfobject);
echo $body;
?>
