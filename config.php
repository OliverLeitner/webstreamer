<?php
/* some config settings for our system */

//where are our javascripts placed...
$js_dir = "scripts/";

//swfobject 2.x local or google based?
$swfobject = "swfobject/swfobject.js";

//the query install
$jquery = "jquery-1.8.3.min.js";

//main style
$style_main = "style/style_dir.css";

/*
set the player for streaming we want to use
options:
	jwplayer (WORKING!)
	projekktor (BROKEN!, only pseudo rtmp from files)
	flowplayer_flash (WORKING!, default cause GPL)
	flowplayer_html5 (BROKEN!, only pseudo rtmp from files)

beware: pseudo playback almost always only support flv,m4v,mp4
it also doesnt work with slow bandwith connections (LAN only, if no fast server avail.)
*/
$player = "flowplayer_flash";

//jwplayer registration key for stats...
//leave blank if you dont have one...
$jw_key = "";

//vid settings
$height = "540";
$width = "660";
$buffer = "500";

//just show rootdirs in this array...
$include_dirs = array("directory1","directory2","directory3");

//just show files with extensions in this array...
$include_files = array("avi","flv","m4v","mp4","ogm","mpg","mpeg","mkv","wmv");

//the rtmp media server domain to look up
//!IMPORTANT! currently we only support crtmpserver
$crtmpserver = $_SERVER['SERVER_NAME'];

//where is the main.log of the crtmpserver located
$crtmpserverlog = "/var/log/crtmpserver/main.log";

//the storage server, needed for local playback
//simply just create a webserver listening on a different port...
//if storageserver and crtmpserver are on the same machine
//leave $storageservervar as is.
//this server holds your media files.
$storageserver = $_SERVER['SERVER_NAME'];
$storageport = "8000";
?>
