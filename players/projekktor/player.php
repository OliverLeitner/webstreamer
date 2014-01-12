<?php
ini_set("display_errors","Off");

/* projekktor definitions */
$style = "<link rel=\"stylesheet\" href=\"".$js_dir."projekktor/themes/maccaco/projekktor.style.css\" type=\"text/css\" media=\"screen\" />";
$headscript = "<script type=\"text/javascript\" src=\"".$js_dir."projekktor/projekktor-1.3.08.min.js\"></script>";
$contentscript .= "<script type=\"text/javascript\">
	$(document).ready(function() {
		projekktor('#container1', {
			poster: '".$thumbs_dir.$filename."_thumb.png',
			playerFlashMP4: '".$js_dir."projekktor/swf/StrobeMediaPlayback/StrobeMediaPlayback.swf',
			playerFlashMP3: '".$js_dir."projekktor/swf/StrobeMediaPlayback/StrobeMediaPlayback.swf',
			useYTIframeAPI: 'true',
			platforms: ['flash', 'native', 'vlc', 'browser', 'android', 'ios'],
			title: '".$name_cmd."',
			controls: 'true',
			playlist: [{
				0:{src:'".$default_src."',type:'".$type."'},
				config:{streamType:'rtmp', streamServer:'rtmp://".$crtmpserver."/live'}
			}]
		});
	});
</script>";
?>
