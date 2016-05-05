<?php
/* collection of required functions */

/* initializing memcache support */
function init_memcache($servers=array(),$delimiter){
    $m = new Memcached();
    foreach($servers AS $server){
        $serverdata = explode($delimiter,$server);
        $m->addServer($serverdata[0], $serverdata[1]);
    }
    return $m;
}

/* creating a clean title from the filelink */
function cleanTitle($ending,$string,$replacer="/",$replacing="."){
	$title = str_replace($ending,"",$string);
	$title = str_replace($replacer,$replacing,$string);
	$titleArr = explode($replacing,$title);
	$title = array_pop($titleArr);
	return $title;
}

/**
 *	http://us.php.net/manual/en/function.array-multisort.php#83117
 */
function php_multisort($data,$keys)
{
	foreach ($data as $key => $row)
	{
		foreach ($keys as $k)
		{
			$cols[$k['key']][$key] = $row[$k['key']];
		}
	}
	$idkeys = array_keys($data);
	$i=0;
	foreach ($keys as $k)
	{
		if($i>0){$sort.=',';}
		$sort.='$cols['.$k['key'].']';
		if($k['sort']){$sort.=',SORT_'.strtoupper($k['sort']);}
		if($k['type']){$sort.=',SORT_'.strtoupper($k['type']);}
		$i++;
	}
	$sort .= ',$idkeys';
	$sort = 'array_multisort('.$sort.');';
	eval($sort);
	foreach($idkeys as $idkey)
	{
		$result[$idkey]=$data[$idkey];
	}
	return $result;
}

/**
 *	@ http://us3.php.net/manual/en/function.filesize.php#84652
 */
function bytes_to_string($size, $precision = 0) {
	$sizes = array('YB', 'ZB', 'EB', 'PB', 'TB', 'GB', 'MB', 'KB', 'Bytes');
	$total = count($sizes);
	while($total-- && $size > 1024) $size /= 1024;
	$return['num'] = round($size, $precision);
	$return['str'] = $sizes[$total];
	return $return;
}

/**
 *	@ http://us.php.net/manual/en/function.time.php#71342
 */
function time_ago($timestamp, $recursive = 0)
{
	$current_time = time();
	$difference = $current_time - $timestamp;
	$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);
	for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);
	if ($val < 0) $val = 0;
	$new_time = $current_time - ($difference % $lengths[$val]);
	$number = floor($number);
	if($number != 1)
	{
		$periods[$val] .= "s";
	}
	$text = sprintf("%d %s ", $number, $periods[$val]);

	if (($recursive == 1) && ($val >= 1) && (($current_time - $new_time) > 0))
	{
		$text .= time_ago($new_time);
	}
	return $text;
}

function dirEmpty($dirname,$allowed){
	$has_allowed = FALSE;
	$findtype = "";
	$excludedirs = '-not -path "*svn*|*etc*|*root*|*lost+found*|*boot*|*app*"';
	foreach($allowed["video"] AS $key => $ending){
		$findtype .= "-name *.".$ending." -o ";
	}
	
	$findtype = rtrim($findtype," -o ");

    //we dont run this if were on top of em all...
	if($_GET["dir"] != "/")
        $result = exec("find '".escapeshellcmd($dirname)."' -not -path ".escapeshellcmd($excludedirs)." -type f ".escapeshellcmd($findtype));
	if($result != "" || $_GET["dir"] == "/"){
		$has_allowed = TRUE;
	}
	return $has_allowed;
}

function compress_image($source_url, $destination_url, $quality) {
	$info = getimagesize($source_url);
 
	if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source_url);
	elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source_url);
	elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url);
 
	//save file
	imagejpeg($image, $destination_url, $quality);
 
	//return destination file
	return $destination_url;
}

function myTruncate($string, $limit, $break=".", $pad="...") {
    if(strlen($string) <= $limit) return $string;
    if(false !== ($breakpoint = strpos($string, $break, $limit))) {
        if($breakpoint < strlen($string) - 1) {
            $string = substr($string, 0, $breakpoint) . $pad;
        }   
    }   
    return $string; 
}   

function ellipsis($text, $max=100, $append='&hellip;') {
    if (strlen($text) <= $max) return $text;
    $out = substr($text,0,$max);
    if (strpos($text,' ') === FALSE) return $out.$append;
    return preg_replace('/\w+$/','',$out).$append;
}   

function catch_regex($string,$regex){
    $out = preg_split($regex, $string);
    return $out;
}   

function substrwords($text,$maxchar,$end='...'){
    if(strlen($text)>$maxchar){
        $words=explode(" ",$text);
        $output = ''; 
        $i=0;
        while(1){
            $length = (strlen($output)+strlen($words[$i]));
            if($length>$maxchar){
                break;
            } else {
                $output = $output." ".$words[$i];
                ++$i;
            };  
        };  
    }else{
        $output = $text;
    }   
    return $output.$end;
}
?>
