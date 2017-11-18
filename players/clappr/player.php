<?php
ini_set("display_errors","Off");

/* projekktor definitions */
$quotes = array("'", '"');
//$style = "<link rel=\"stylesheet\" href=\"".$js_dir."projekktor/themes/maccaco/projekktor.style.css\" type=\"text/css\" media=\"screen\" />";
$style = "";
$headscript = "<script type=\"text/javascript\" src=\"/scripts/clappr/dist/clappr.min.js\"></script>";
$headscript .= "<script type=\"text/javascript\" src=\"/scripts/clappr-rtmp-plugin/dist/rtmp.min.js\"></script>";
$contentscript = "<script>
//$(document).ready(function(){
var player = new Clappr.Player({
    source: '".htmlentities($long_src)."',
    parentId: '#container1',
    poster: '".htmlentities(str_replace($quotes, "", $thumbs_dir.$filename.'_thumb.png'))."',
    duration: ".$seconds.",
    plugins: {'playback': [RTMP]},
    rtmpConfig: {
        swfPath: '/scripts/clappr-rtmp-plugin/dist/assets/RTMP.swf',
        scaling:'stretch',
        playbackType: 'live',
        bufferTime: 1,
        startLevel: 0,
        switchRules: {
            'SufficientBandwidthRule': {
                'bandwidthSafetyMultiple': 1.15,
                'minDroppedFps': 2
            },
            'InsufficientBufferRule': {
                'minBufferLength': 2
            },
            'DroppedFramesRule': {
                'downSwitchByOne': 10,
                'downSwitchByTwo': 20,
                'downSwitchToZero': 24
            },
            'InsufficientBandwidthRule': {
                'bitrateMultiplier': 1.15
            }
        }
    },
});
//});
</script>";
