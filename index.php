<?php
include_once("functions/functs.php"); //general functions
include_once("templates/index.php"); //output templating
include_once("config.php"); //basic configurations
include_once("functions/definitions.php"); //base definitions for images and files
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
// Get this folder and files name.
ini_set("display_errors","Off");

//check for and enable memcache if possible...
$m = "";
if(isset($memcservers) && $memcservers != ""){
   $m = init_memcache($memcservers,":"); 
}

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
// Open the current directory...
{
	// ...start scanning through it.
    while (false !== ($file = readdir($handle)))
	{
		// Make sure we don't list this folder, file or their links.
        if ($file != "." && $file != ".." && $file != './' && $file != $this_script)
		{
			// Get file info.
			$stat				=	stat($this_folder.$file); // ... slow, but faster than using filemtime() & filesize() instead.
			$info				=	pathinfo($this_folder.$file);
			// Organize file info.
			$item['dir']		=	$this_folder;

			if(is_file($this_folder.$file)){
			    $item['name']		=	$info['filename'];
			} else {
                $item['name']           =       $info['basename'];
			}

			$item['lname']		=	strtolower($info['filename']);
			$item['ext']		=	$info['extension'];
				if($info['extension'] == '') $item['ext'] = '.';
			$item['bytes']		=	$stat['size'];
			$item['size']		=	bytes_to_string($stat['size'], 2);
			$item['mtime']		=	$stat['mtime'];

			//setting video types...
            $item['type']		=	$typesArray[$item['ext']];

			// Add files to the file list...
			if(is_file($item['dir'].$item['name'].".".$item['ext']))
			{
				//filter out all files we do not want to show...
				if(in_array($item['ext'],$filetype['video']))
                {
                    array_push($file_list, $item);
				}
			}
			// ...and folders to the folder list.
			else
			{
				if ($item['dir'] == '/' && in_array($item['name'],$include_dirs))
				{
					array_push($folder_list, $item);
				}

				if ($item['dir'] != '/')
				{
					array_push($folder_list, $item);
				}
			}
			// Clear stat() cache to free up memory (not really needed).
			clearstatcache();
			// Add this items file size to this folders total size
			$total_size += $item['bytes'];
        }
    }
	// Close the directory when finished.
    closedir($handle);
}

// Sort folder list.
if($folder_list)
    $folder_list = php_multisort($folder_list, $sort);
// Sort file list.
if($file_list)
    $file_list = php_multisort($file_list, $sort);
// Calculate the total folder size
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
			$listfolders .= '<tr class="folder">
				<td colspan="3" class="name" title="'.urlencode(substrwords($item["name"],50)).'"><img src="images/folder.png" alt="'.urlencode($item['name']).'" /><a href="?dir='.urlencode($item['dir']).urlencode($item['name']).'/" title="'.urlencode($item["name"]).'">'.urlencode($item['name']).'</a></td>
			</tr>';
		}
	}
}


if($file_list){
    if($m != ""){
        $file_list = $m->get('file_list');
    }
	foreach($file_list as $item) {
		//creating thumbnail for the player on player load
		$filename = preg_replace("/ /","_",$item['name'].".".$item['ext']);
		$dirname = dirname($item['dir'].$item['name'].'.'.$item['ext']);
        $cmd_thumb = "avconv -ss 00:02:00 -t 1 -i '".escapeshellcmd($item['dir'].$item['name']).".".escapeshellcmd($item['ext'])."' -r 16 -qscale 1 -s 320x240 -f image2 '".escapeshellcmd($thumbs_dir.$filename)."_thumb.png'";
        $out_duration_cmd = "avconv -i '".escapeshellcmd($item['dir'].$item['name']).".".escapeshellcmd($item['ext'])."' 2>&1 | grep Duration > '".escapeshellcmd($meta_dir.$filename).".txt'";
        if(!file_exists($thumbs_dir.$filename."_thumb.png")){
            if($m != ""){
                $m->set('cmd_thumb',exec($cmd_thumb));
                $m->set('compress_image',compress_image($thumbs_dir.$filename."_thumb.png", $thumbs_dir.$filename."_thumb.png", 60));
                $m->set('gzcompress',gzcompress($thumbs_dir.$filename."_thumb.png"));
            } else {
                exec($cmd_thumb);
                compress_image($thumbs_dir.$filename."_thumb.png", $thumbs_dir.$filename."_thumb.png", 60);
                gzcompress($thumbs_dir.$filename."_thumb.png");
            }
        }
		if(!file_exists($meta_dir.$filename.".txt")){
			exec($out_duration_cmd);
		}
		$out_duration = exec("cat ".escapeshellcmd($meta_dir.$filename).".txt");
		$out_duration = str_replace(",","<br />",$out_duration);
		$popup_link = 'popitup(\'player.php?name='.$item['dir'].$item['name'].'.'.$item['ext'].'&amp;file='.$item['name'].'.'.$item['ext'].'&amp;type='.$item['type'].'&t='.mktime().'\')';
		$listfiles .= '<tr class="file">
			<td class="thumb" title="'.urlencode(substrwords($item["name"],20)).'"><span class="item_title">'.substrwords($item["name"],20).'</span><a title="'.urlencode(substrwords($item["name"],20)).'" href="#'.urlencode($item['name']).'" onclick="'.$popup_link.'"><img alt="'.urlencode($item["name"]).'" src="'.$thumbs_dir.$filename.'_thumb.png" border="0" /></a></td>
			<td class="name" id="'.$item['name'].'" title="'.urlencode(substrwords($item["name"],20)).'"><img src="'.$this_script.'?image='.$item['ext'].'" alt="'.$item['ext'].'" /><a href="#'.urlencode($item['name']).'" onclick="'.$popup_link.'">'.$item['name'].'.'.$item['ext'].'</a><br />'.$out_duration.'</td>
			<td class="start"><a href="#'.$item['name'].'" onclick="javascript:ajax_startstream(\''.$item['dir'].$item['name'].'.'.$item['ext'].'\',\''.$item['name'].'.'.$item['ext'].'\');">start</a></td>
			<td class="stop"><a href="#'.$item['name'].'" onclick="javascript:ajax_stopstream(\''.$item['dir'].$item['name'].'.'.$item['ext'].'\',\''.$item['name'].'.'.$item['ext'].'\');">stop</a></td>
			<td class="size">'.$item['size']['num'].'<span>'.$item['size']['str'].'</span></td>
		</tr>';
	}
}

$body = doIndex($style_main,$js_dir,$jquery,($height+80),$width,$this_folder,$listfolders,$listfiles);
echo $body;
?>
