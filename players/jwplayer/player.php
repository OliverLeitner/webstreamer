<?php
$style = "";
$headscript = "<script src=\"".$js_dir."jwplayer/jwplayer.js\"></script>";
//if we are registered for statistics on jwplayer homepage...
if($jw_key != ""){
    $headscript .= "<script type=\"text/javascript\">jwplayer.key=\"".$jw_key."\"</script>";
}
$contentscript = "<script type=\"text/javascript\">
    jwplayer('container1').setup({
    file: '".$long_src."',
        image: '".$thumbs_dir.$filename."_thumb.png',
        'rtmp.subscribe': 'true',
        });
</script>";
