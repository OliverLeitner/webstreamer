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

$this_script = basename(__FILE__);
//$this_folder = str_replace('/'.$this_script, '', $_SERVER['SCRIPT_NAME']);
$this_folder = $_GET['dir'];

if(!isset($_GET['dir'])){
	$_GET['dir'] = $this_script."?dir=/";
}

// Declare vars used beyond this point.
$file_list = array();
//$folder_list = array();
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
			$stat				=	stat($_GET['dir'].$file); // ... slow, but faster than using filemtime() & filesize() instead.
			$info				=	pathinfo($_GET['dir'].$file);
			// Organize file info.
			$item['dir']		=	$_GET['dir'];
			$item['name']		=	$info['filename'];
			$item['lname']		=	strtolower($info['filename']);
			$item['ext']		=	$info['extension'];
				if($info['extension'] == '') $item['ext'] = '.';
			$item['bytes']		=	$stat['size'];
			$item['size']		=	bytes_to_string($stat['size'], 2);
			$item['mtime']		=	$stat['mtime'];

			//setting video types...
			$item['type']		=	$typesArray[$item['ext']];

			// Add files to the file list...
			if($info['extension'] != '')
			{
				//filter out all files we do not want to show...
				if(in_array($info['extension'],$include_files))
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

//******************************** output definitions *************************************************************
if($folder_list) {
	foreach($folder_list as $item) {
		//if we have items, we show up
		$has_files = dirEmpty($item["dir"].$item["name"],$filetype);
		if($has_files == TRUE){
			$listfolders .= '<tr class="folder">
				<td colspan="3" class="name"><img src="'.$this_script.'?image='.$item['ext'].'" alt="'.$item['ext'].'" /><a href="?dir='.$item['dir'].$item['name'].'/">'.$item['name'].'</a></td>
			</tr>';
		}
	}
}


if($file_list){
	foreach($file_list as $item) {
		$listfiles .= '<tr class="file">
			<td class="name" id="'.$item['name'].'"><img src="'.$this_script.'?image='.$item['ext'].'" alt="'.$item['ext'].'" /><a href="#'.urlencode($item['name']).'" onclick="popitup(\'player.php?name='.urlencode($item['dir']).urlencode($item['name']).'.'.$item['ext'].'&amp;file='.urlencode($item['name']).'.'.$item['ext'].'&amp;type='.$item['type'].'\')">'.$item['name'].'.'.$item['ext'].'</a></td>
			<td class="start"><a href="#'.$item['name'].'" onclick="javascript:ajax_startstream(\''.$item['dir'].$item['name'].'.'.$item['ext'].'\',\''.$item['name'].'.'.$item['ext'].'\');">start</a></td>
			<td class="stop"><a href="#'.$item['name'].'" onclick="javascript:ajax_stopstream(\''.$item['dir'].$item['name'].'.'.$item['ext'].'\',\''.$item['name'].'.'.$item['ext'].'\');">stop</a></td>
			<td class="size">'.$item['size']['num'].'<span>'.$item['size']['str'].'</span></td>
		</tr>';
	}
}

$body = doIndex($style_main,$js_dir,$jquery,($height+80),$width,$this_folder,$listfolders,$listfiles);
echo $body;
?>
