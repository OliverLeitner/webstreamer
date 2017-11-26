<?php
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
//template markers
$markers = array();

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
        $has_files = dirEmpty($item["dir"].$item["name"],$filetype,$commands);
        if($has_files == TRUE){
            $listfolders .= trim('<tr class="folder"><td class="name" title="'.
                $item['name'].'"><img src="images/Folder_open_trans.gif" alt="'.
                $item['name'].'" /><a href="?dir='.
                urlencode($item['dir']).
                urlencode($item['name']).'/" title="'.
                $item['name'].'">'.
                $item['name'].'</a></td></tr>');
        }
    }
}

if($file_list){
    if($m != ""){
        $file_list = $m->get('file_list');
    }
    foreach($file_list as $item) {
        //creating thumbnail for the player on player load
        $params['file_path'] = $item['dir'].$item['name'].'.'.$item['ext'];
        $params['thumb_dir'] = $thumbs_dir;
        $filename = preg_replace("/[^A-Za-z0-9\_\-\.]/","",$item['name']);
        $params['thumb_name'] = strtolower($filename);
        $cmd_thumb = buildCmd($params,$commands['create_thumbnail']);

        $params['meta_dir'] = $meta_dir;
        $out_duration_cmd = buildCmd($params,$commands['write_metadata']);

        if(!file_exists($webroot."/".$params['thumb_dir'].$params['thumb_name']."_thumb.png")){
            if($m != ""){
                $m->set('cmd_thumb',exec($cmd_thumb));
                $compressed_image = compress_image($params['thumb_dir'].
                    $params['thumb_name']."_thumb.png", $params['thumb_dir'].
                    $params['thumb_name']."_thumb.png", 60);

                if($compressed_image != false)
                {
                    $m->set('compress_image',$compressed_image);
                    $m->set('gzcompress',gzcompress($params['thumb_dir'].$params['thumb_name']."_thumb.png"));
                }
            } else {
                exec($cmd_thumb);
                $compressed_image = compress_image($params['thumb_dir'].
                    $params['thumb_name']."_thumb.png", $params['thumb_dir'].
                    $params['thumb_name']."_thumb.png", 60);

                if($compressed_image != false)
                {
                    gzcompress($params['thumb_dir'].$params['thumb_name']."_thumb.png");
                }
            }
        }
        if(!file_exists($webroot."/".$params['meta_dir'].$params['thumb_name'].".txt")){
            exec($out_duration_cmd);
        }
        $out_duration = file_get_contents($params['meta_dir'].$params['thumb_name'].".txt");
        $out_duration = str_replace(",","<br />",$out_duration);
        $popup_link = "/player.php?name=".$params['file_path']."&amp;file=".$item['name'].".".
            $item['ext']."&amp;type=".$item['type']."&t=".rand();

        $listfiles .= trim('<tr class="file"><td class="thumb" title="'.
            substrwords($params['thumb_name'],20).'">'.
            '<a href="http://'.$storageserver.':'.$storageport.'/'.$params['file_path'].
            '" target="_blank">Direct Link</a><br/><span class="item_title">'.
            substrwords($params['thumb_name'],20).'</span><a title="'.
            substrwords($params['thumb_name'],20).'" href="'.$popup_link.'" target="_blank"><img alt="'.
            substrwords($params['thumb_name'],20).'" src="'.
            $params['thumb_dir'].$params['thumb_name'].'_thumb.png" /></a></td><td class="name" id="'.
            substrwords($params['thumb_name'],20).'" title="'.
            substrwords($params['thumb_name'],20).'"><img src="'.$this_script.'?image='.
            $item['ext'].'" alt="'.$item['ext'].'" /><a href="'.$popup_link.'" target="_blank">'.
            $item['name'].'.'.$item['ext'].'</a><br />'.$out_duration.'</td><td class="start"><a href="#'.
            $item['name'].'" onclick="javascript:ajax_cmd(\'start\',\''.$params['file_path'].'\',\''.
            $item['name'].'.'.$item['ext'].'\');">start</a></td><td class="stop"><a href="#'.
            $item['name'].'" onclick="javascript:ajax_cmd(\'stop\',\''.$params['file_path'].'\',\''.
            $item['name'].'.'.$item['ext'].'\');">stop</a></td><td class="size">'.
            $item['size']['num'].'<span>'.$item['size']['str'].'</span></td></tr>');
    }
}

//filling out the template markers
$markers['main_style'] = $style_main;
$markers['js_dir'] = $js_dir;
$markers['height'] = intval($height)+80;
$markers['width'] = intval($width)+20;
$markers['folder'] = $this_folder;
$markers['list_folders'] = $listfolders;
$markers['list_files'] = $listfiles;
//define the template file
$tpl_file = 'templates/index.html';

//output
echo templating($tpl_file,$markers);
$out = ob_get_contents();
ob_end_flush();
