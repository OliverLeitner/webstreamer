<?php
/* some config settings for our system */
//where are our javascripts placed...
$js_dir = "scripts/";

//the query install
$jquery = "zepto.min.js";

//main style
$style_main = "style/style_dir.css";

//thumbnail directory
$thumbs_dir = "thumbs/";

//metadata storage
$meta_dir = "meta/";

/*
set the player for streaming we want to use
options:
	jwplayer (WORKING!)
	projekktor (WORKING!)
	flowplayer (WORKING!)

beware: pseudo playback almost always only support flv,m4v,mp4
it also doesnt work with slow bandwith connections (LAN only, if no fast server avail.)
if youre using an iPad, iPhone, Android, only the pseudo streaming options might work...
*/
$player = "projekktor";

//jwplayer registration key for stats...
//leave blank if you dont have one...
$jw_key = "";

//vid settings
$height = "480";
$width = "680";
$buffer = "250";

//just show rootdirs in this array...
$include_dirs = array("directory1","directory2","directory3");

//just show files with extensions in this array...
$include_files = array("avi","flv","m4v","mp4","ogm","mpg","mpeg","mkv","wmv","mov");

//security setting jails media indexing to media storage dir...
$mediaroot = "/var/www";

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
