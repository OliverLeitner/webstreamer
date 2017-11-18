<?php
/* some config settings for our system */
//where are our javascripts placed...
$js_dir = "scripts/";

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
	clappr (WORKING!) DEFAULT

beware: pseudo playback almost always only support flv,m4v,mp4
it also doesnt work with slow bandwith connections (LAN only, if no fast server avail.)
if youre using an iPad, iPhone, Android, only the pseudo streaming options might work...
*/
$player = "jwplayer";

//jwplayer registration key for stats...
//leave blank if you dont have one...
$jw_key = "your jw license key";

//vid settings
$height = "480";
$width = "680";
$buffer = "250";

//just show rootdirs in this array...
$include_dirs = array("/path1","/path2","/path3","/path4","/path5");

//just show files with extensions in this array...
$include_files = array("avi","flv","m4v","mp4","ogm","mpg","mpeg","mkv","wmv","mov");

//security setting jails media indexing to media storage dir...
$mediaroot = "/mediarootdir";

//the rtmp media server domain to look up
//!IMPORTANT! currently we only support crtmpserver
$crtmpserver = '192.168.1.1'; //the ip address of the machine running the rtmp server
$crtmp_out_port = '1935'; //port that connects us to the crtmpserver, useful for rtmps and rtmpt
$crtmp_in_port = '6666'; //port used for streaming media content to the crtmp server
//where is the main.log of the crtmpserver located
$crtmpserverlog = "/var/log/crtmpserver/main.log";

//the storage server, needed for local playback
//simply just create a webserver listening on a different port...
//if storageserver and crtmpserver are on the same machine
//leave $storageservervar as is.
//this server holds your media files.
$storageserver = $_SERVER['SERVER_NAME'];
$storageport = "8000";

//enable memcache support
$memcservers = array("127.0.0.1:11211");
