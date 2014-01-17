<?php
$style = "";

$headscript = "<script src=\"".$js_dir."jwplayer/jwplayer.js\"></script>";

//if we are registered for statistics on jwplayer homepage...
if($jw_key != ""){
	$headscript .= "<script type=\"text/javascript\">jwplayer.key=\"".$jw_key."\"</script>";
}

$contentscript = "<script type=\"text/javascript\">
	jwplayer('container1').setup({
        file: '".htmlentities(str_replace($quotes, "", $long_src))."',
		image: '".htmlentities(str_replace($quotes, "",$thumbs_dir.$filename))."_thumb.png',
		'rtmp.subscribe': 'false',
		primary: 'flash'
	});
</script>";
?>
