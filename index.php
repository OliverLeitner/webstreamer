<?php
$page = "index";
require_once "loader.php";
####################################################################################################################################
####################################[[[ SCROLL TO BOTTOM OF THIS FILE TO CHANGE THE TEMPLATE ]]]####################################
####################################################################################################################################
/*
 *		ALL IN ONE FOLDER/FILE LISTER
 *
 *       Author:    Greg Johnson
 *         Info:    http://greg-j.com/some-url
 *      License:    Creative Commons 3.0
 *                  @ http://creativecommons.org/licenses/by-sa/3.0/us/
 *
 */


/*	Report errors. Ignore pesky notices.
 *	There are a few places this script assumes information is
 *	available for folders (size, for example) that does not
 *	exist. Let's just make sure you're not reporting notices.
 */

/*	changes by: nevermind
 */

/**********************************************************************************************************************************/
/************************************************************************************************************[ DIRECTORY LOGIC ]***/
//predefine here, for funct at the bottom...
$listfiles = '';
$listfolders = '';

//base location definitions
$this_script = basename(__FILE__);
$this_folder = $_GET['dir'];
$this_folder = str_replace("..", "", $this_folder);

if(!preg_match("@^{$mediaroot}@",$this_folder)){
    $this_folder = $mediaroot.$this_folder;
}

//always root to / if no param given
if(!isset($_GET['dir'])){
    $this_folder = $this_script."?dir=/";
    header("Location: ".$this_script."?dir=/");
}

// Declare vars used beyond this point.
$file_list = array();
$folder_list = array();
$total_size = 0;

if ($handle = opendir($this_folder))
{
    while (false !== ($file = readdir($handle)))
    {
        if ($file != "." && $file != ".." && $file != './' && $file != $this_script && 
            !preg_match('/[^a-zA-Z0-9\_\-\.\,]+/', $file, $matches))
        {
            $stat = stat($this_folder.$file);
            $info = pathinfo($this_folder.$file);
            $item['dir'] = $this_folder;
            if(is_file($this_folder.$file)){
                $item['name'] = $info['filename'];
            } else {
                $item['name'] = $info['basename'];
            }
            $item['lname'] = strtolower($info['filename']);
            if(!isset($info['extension']) || $info['extension'] == '')
            {
                $item['ext'] = '.';
            }
            else
            {
                $item['ext'] = $info['extension'];
            }
            $item['bytes'] = $stat['size'];
            $item['size'] = bytes_to_string($stat['size'], 2);
            $item['mtime'] = $stat['mtime'];
            if(isset($typesArray[$item['ext']]))
            {
                if(isset($typesArray[$item['ext']]) && $typesArray[$item['ext']] != '')
                {
                    $item['type'] = $typesArray[$item['ext']];
                }
                else
                {
                    $item['type'] = 'undefined';
                }
            }
            if(is_file($item['dir'].$item['name'].".".$item['ext']) && 
                !preg_match('/[^a-zA-Z0-9\_\-\.\,]+/', $item['name'], $matches))
            {
                if(in_array($item['ext'],$filetype['video']))
                {
                    if(isset($item['type']) && $item['type'] != '')
                    {
                        array_push($file_list, $item);
                    }
                }
            }
            else
            {
                if ($item['dir'] == '/' && in_array($item['name'],$include_dirs))
                {
                    array_push($folder_list, $item);
                }
                if ($item['dir'] != '/')
                {
                    if($item['name'] != '' && !preg_match('/[^a-zA-Z0-9\_\-\.]+/', $item['name'], $matches))
                    {
                        array_push($folder_list, $item);
                    }
                }
            }
            clearstatcache();
            $total_size += $item['bytes'];
        }
    }
    closedir($handle);
}

//Sort folder list.
if($folder_list)
    $folder_list = php_multisort($folder_list, $sort);
//Sort file list.
if($file_list)
    $file_list = php_multisort($file_list, $sort);
//Calculate the total folder size
if($file_list && $folder_list)
    $total_size = bytes_to_string($total_size, 2);

if($m != ""){
    $m->set('folder_list',$folder_list);
    $m->set('file_list',$file_list);
}

//******************************** output definitions *************************************************************
if($folder_list) {
    if($m != ""){
        $folder_list = $m->get('folder_list');
    }
    foreach($folder_list as $item) {
        $has_files = dirEmpty($item["dir"].$item["name"],$filetype);
        if($has_files == TRUE){
            $listfolders .= trim('<tr class="folder"><td class="name" title="'.
                urlencode($item['name']).'"><img src="images/Folder_open_trans.gif" alt="'.
                urlencode($item['name']).'" /><a href="?dir='.
                urlencode($item['dir']).
                urlencode($item['name']).'/" title="'.
                urlencode($item['name']).'">'.
                urlencode($item['name']).'</a></td></tr>');
        }
    }
}

if($file_list){
    if($m != ""){
        $file_list = $m->get('file_list');
    }
    foreach($file_list as $item) {
        //creating thumbnail for the player on player load
        $filename = preg_replace("/[^A-Za-z0-9\_\-\.]/","",$item['name']);
        $dirname = dirname($item['dir'].$item['name'].'.'.$item['ext']);
        $cmd_thumb = "avconv -ss 00:02:00 -t 1 -i '".
            escapeshellcmd($item['dir'].$item['name']).".".
            escapeshellcmd($item['ext'])."' -r 16 -qscale 1 -s 320x240 -f image2 '".
            escapeshellcmd($thumbs_dir.strtolower($filename))."_thumb.png'";

        $out_duration_cmd = "avconv -i '".
            escapeshellcmd($item['dir'].$item['name']).".".
            escapeshellcmd($item['ext'])."' 2>&1 | grep Duration > '".
            escapeshellcmd($meta_dir.strtolower($filename)).".txt'";

        if(!file_exists($webroot."/".$thumbs_dir.strtolower($filename)."_thumb.png")){
            if($m != ""){
                $m->set('cmd_thumb',exec($cmd_thumb));
                $compressed_image = compress_image($thumbs_dir.
                    strtolower($filename)."_thumb.png", $thumbs_dir.
                    strtolower($filename)."_thumb.png", 60);

                if($compressed_image != false)
                {
                    $m->set('compress_image',$compressed_image);
                    $m->set('gzcompress',gzcompress($thumbs_dir.strtolower($filename)."_thumb.png"));
                }
            } else {
                exec($cmd_thumb);
                $compressed_image = compress_image($thumbs_dir.
                    strtolower($filename)."_thumb.png", $thumbs_dir.
                    strtolower($filename)."_thumb.png", 60);

                if($compressed_image != false)
                {
                    gzcompress($thumbs_dir.strtolower($filename)."_thumb.png");
                }
            }
        }
        if(!file_exists($webroot."/".$meta_dir.strtolower($filename).".txt")){
            exec($out_duration_cmd);
        }
        $out_duration = file_get_contents(escapeshellcmd($meta_dir.strtolower($filename)).".txt");
        $out_duration = str_replace(",","<br />",$out_duration);
        $popup_link = "/player.php?name=".$item['dir'].$item['name'].".".
            $item['ext']."&amp;file=".$item['name'].".".
            $item['ext']."&amp;type=".$item['type']."&t=".rand();

        $listfiles .= trim('<tr class="file"><td class="thumb" title="'.
            urlencode(substrwords(strtolower($filename),20)).'"><span class="item_title">'.
            substrwords(strtolower($filename),20).'</span><a title="'.
            urlencode(substrwords(strtolower($filename),20)).'" href="'.$popup_link.'" target="_blank"><img alt="'.
            urlencode(substrwords(strtolower($filename),20)).'" src="'.
            $thumbs_dir.strtolower($filename).'_thumb.png" /></a></td><td class="name" id="'.
            urlencode(substrwords(strtolower($filename),20)).'" title="'.
            urlencode(substrwords(strtolower($filename),20)).'"><img src="'.$this_script.'?image='.
            $item['ext'].'" alt="'.$item['ext'].'" /><a href="'.$popup_link.'" target="_blank">'.
            $item['name'].'.'.$item['ext'].'</a><br />'.$out_duration.'</td><td class="start"><a href="#'.
            $item['name'].'" onclick="javascript:ajax_cmd(\'start\',\''.$item['dir'].$item['name'].'.'.
            $item['ext'].'\',\''.$item['name'].'.'.$item['ext'].'\');">start</a></td><td class="stop"><a href="#'.
            $item['name'].'" onclick="javascript:ajax_cmd(\'stop\',\''.$item['dir'].$item['name'].'.'.
            $item['ext'].'\',\''.$item['name'].'.'.$item['ext'].'\');">stop</a></td><td class="size">'.
            $item['size']['num'].'<span>'.$item['size']['str'].'</span></td></tr>');
    }
}

$body = doIndex($style_main,$js_dir,($height+80),$width,$this_folder,$listfolders,$listfiles);
echo $body;
$out = ob_get_contents();
ob_end_flush();
