<?php
ini_set("display_errors","Off");

/* projekktor definitions */
$quotes = array("'", '"');
$style = "<link rel=\"stylesheet\" href=\"".$js_dir."projekktor/themes/maccaco/projekktor.style.css\" type=\"text/css\" media=\"screen\" />";
$headscript = "<script type=\"text/javascript\" src=\"".$js_dir."projekktor/projekktor-1.3.09.min.js\"></script>";
$contentscript .= "<script type=\"text/javascript\">
	$(document).ready(function() {
		projekktor('#container1', {
            poster: '".htmlentities(str_replace($quotes, "", $thumbs_dir.$filename))."_thumb.png',
			playerFlashMP4: '".$js_dir."projekktor/swf/StrobeMediaPlayback/StrobeMediaPlayback.swf',
			playerFlashMP3: '".$js_dir."projekktor/swf/StrobeMediaPlayback/StrobeMediaPlayback.swf',
			useYTIframeAPI: 'true',
			platforms: ['flash', 'native', 'vlc', 'browser', 'android', 'ios'],
			title: '".htmlentities(str_replace($quotes, "", $name_cmd))."',
            controls: 'true',
            duration: ".$seconds.",
			playlist: [{
                0:{src:'".htmlentities(str_replace($quotes, "", $default_src))."',type:'".htmlentities(str_replace($quotes, "", $type))."'},
				config:{streamType:'rtmp', streamServer:'rtmp://".$crtmpserver."/vod', duration: ".$seconds."}
			}]
		});
	});
</script>";
?>
