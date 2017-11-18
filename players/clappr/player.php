<?php
//i like to have some error output
//where needed
ini_set("display_errors","On");
error_reporting(E_ALL);

/* clappr definitions */
$quotes = array("'", '"');
$style = "";
$headscript = "<script type=\"text/javascript\" src=\"".$js_dir."clappr/dist/clappr.min.js\"></script>\n";
$headscript .= "<script type=\"text/javascript\" src=\"".$js_dir."clappr-rtmp-plugin/dist/rtmp.min.js\"></script>";
$contentscript = "<script type=\"text/javascript\">
var player = new Clappr.Player({
    source: '".htmlentities($long_src)."',
    parentId: '#container1',
    poster: '".htmlentities(str_replace($quotes, "", $thumbs_dir.$filename.'_thumb.png'))."',
    duration: ".$seconds.",
    plugins: {'playback': [RTMP]},
    rtmpConfig: {
        swfPath: '".$js_dir."clappr-rtmp-plugin/dist/assets/RTMP.swf',
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
</script>";
