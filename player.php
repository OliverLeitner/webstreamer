<?php
//page init
require_once "loader.php";

//array to store the markers...
$markers = array();

//grab the full filepath...
$name = addslashes(ltrim(urldecode(str_replace("..", "", $_GET['name'])),"/"));
$type = $_GET['type'];
$title = urldecode($_GET['name']);
preg_match('/\.[0-9A-Za-z]{3}$/',basename($name),$matched);
$video_title = explode($matched[0],basename($name))[0];

//for multiuser support we use client ip and chosen file
$params['uid'] = md5($_SERVER['REMOTE_ADDR'].$_GET['file']);

//commandline testing if we got our rtmp stream running or not
$cmd = buildCmd($params,$commands['ps_get']);
$name_cmd = exec($cmd);

$filename = preg_replace("/[^A-Za-z0-9\_\-\.]/","",strtolower($video_title));
//reading out the duration of a clip to have a scrollbar...
$data = file_get_contents($meta_dir.$filename.".txt");

if($m != ""){
    $m->set('data',$data);
    $data = $m->get('data');
}

preg_match("#Duration: (.+), start#",$data,$duration);
$parsed = date_parse(trim($duration[1]));
$seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
//check if we are streaming or not streaming...
if($name_cmd == ""){
    $name_cmd = $name;
} else {
    $name_cmd = $params['uid'];
}

/* global vars possible to set... */
$style = "";
$headscript = "";
$tag = "<div id=\"css-poster\" class=\"player minimalist is-splash\" data-rtmp=\"rtmp://".
    $crtmpserver.":".
    $crtmp_out_port."/flvplayback\" data-engine=\"flash\"><video id=\"container1\" class=\"player projekktor\" poster=\"".
    $thumbs_dir.$filename."_thumb.png\" data-engine=\"html5\" width=\"".$width."\" height=\"".
    $height."\" title=\"".$title."\" controls>";

/* global definitions for all other players but flowplayer */
if($name_cmd == $params['uid']){
    $long_src = "rtmp://".$crtmpserver.":".$crtmp_out_port."/flvplayback/".$name_cmd;
    $short_src = $name_cmd;
    $default_src = "rtmp://".$crtmpserver.":".$crtmp_out_port."/flvplayback/".$name_cmd;

    $tag .= "<source type=\"".$type."\" src=\"".$long_src."\"/>";
    /* load the desired player */
    if($player == "clappr" || !isset($player)){
        include_once $webroot."/players/clappr/player.php";
        $clappr_tag = '<div id="container1"></div>';
    } else {
        include_once $webroot."/players/".$player."/player.php";
    }
} else {
    $contentscript = "";
    $long_src = "http://".$storageserver.":".$storageport."/".$name_cmd;
    $short_src = $long_src;
    $default_src = $long_src;

    $tag .= "<source type='".$type.";codecs=\"".$codecs[$type]."\"' src=\"".$long_src."\" autobuffer controls preload=\"auto\"/>";
}
$tag .= "</video></div>";

//special case for clappr player, cause he hates the video tag
//in this version...
if(($player == "clappr" || !isset($player)) && $name_cmd == $params['uid'])
{
    $tag = $clappr_tag;
}

//combining all the above...
$markers['players_content_script'] = $contentscript;
$markers['players_scripts'] = $headscript;
$markers['players_content_tag'] = $tag;
$markers['title'] = $title;
$markers['video_title'] = $video_title;
$markers['js_dir'] = $js_dir;
$markers['main_style'] = $style_main;
$markers['players_styles'] = $style;
//choose a template file...
$tpl_file = "templates/player.html";

/* writing our template */
header('X-Content-Duration: '.$seconds);
echo templating($tpl_file,$markers);
$out = ob_get_contents();
ob_end_flush();
