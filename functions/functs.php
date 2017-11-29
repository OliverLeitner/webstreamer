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

/**
 * filling out the listings
 *
 * @param string $tpl_file filename and location of the template file
 * @param mixed $settings the template marker data
 *
 * @return string filled out template
 */
function templating($tpl_file,$settings){
    $template = file_get_contents($tpl_file);
    foreach($settings AS $skey => $sdata)
    {
        $template = preg_replace('/###'.$skey.'###/i',$sdata,$template);
    }
    return $template;
}

/**
 * http://us.php.net/manual/en/function.array-multisort.php#83117
 */
function php_multisort($data,$keys)
{
    $cols = array();
    foreach ($data as $key => $row)
    {
        foreach ($keys as $k)
        {
            $cols[$k['key']][$key] = $row[$k['key']];
        }
    }
    $idkeys = array_keys($data);
    array_multisort($cols['lname'],SORT_ASC,$cols['size'],SORT_ASC,$idkeys);
    foreach($idkeys as $idkey)
    {
        $result[$idkey]=$data[$idkey];
    }
    return $result;
}

/**
 * http://us3.php.net/manual/en/function.filesize.php#84652
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
 * http://us.php.net/manual/en/function.time.php#71342
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

/**
 * directory scanner function
 */
function dirEmpty($dirname,$allowed,$commands){
    if($_GET['dir'] != '/')
    {
        $params['directory_name'] = $dirname;
        $params['subdirs'] = '';
        foreach($allowed["video"] AS $key => $ending){
            //first level
            $params['file_ending'] = $ending;
            $cmd_dir = buildCmd($params,$commands['find_file']);
            $cmd = intval(exec($cmd_dir));
            if($cmd > 0)
            {
                return true;
            }
            //more than one level
            $i = 1;
            while($i <= 8)
            {
                $builder = $i;
                $param = "";
                while($builder > 0)
                {
                    $param .= "/*";
                    $builder--;
                }
                $params['subdirs'] = $param;
                $cmd_dir_level2 = buildCmd($params,$commands['find_file']);
                $cmd_sub = intval(exec($cmd_dir_level2));
                if($cmd_sub > 0)
                {
                    return true;
                }
                $i++;
            }
        }
    }
    if($_GET["dir"] == "/")
    {
        return true;
    }
    return false;
}

/**
 * compress down thumbnails on the fly
 */
function compress_image($source_url, $destination_url, $quality) {
    if(file_exists($source_url))
    {
        $info = getimagesize($source_url);

        if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source_url);
        elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source_url);
        elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url);

        //save file
        imagejpeg($image, $destination_url, $quality);

        //return destination file
        return $destination_url;
    }
    else
    {
        return false;
    }
}

/**
 * create title from filename
 */
function substrwords($text,$maxchar,$end='...'){
    if(strlen($text)>$maxchar){
        //check for more than just spaces...
        $split_chars = array('.','_','-',' ');
        $words = array();
        foreach($split_chars AS $splitby)
        {
            $outstr = explode($splitby,$text);
            if($outstr != " ")
            {
                $words[] = $outstr;
            }
        }
        //much more elegant to implode than
        //the original while loop
        $output = trim(implode(' ',$words[3]));
    }else{
        $output = $text;
    }
    return $output.$end;
}

/**
 * build a shell command
 *
 * @param mixed $params replacement string array
 * @param string $cmd_tpl command template string
 *
 * @return returns the built commandstring
 */
function buildCmd($params,$cmd_tpl)
{
    $command = $cmd_tpl;
    foreach($params AS $dkey => $data)
    {
        preg_match('/\*\//',$data,$matched);
        if($matched)
        {
            $data = escapeshellcmd($data);
        }
        $command = preg_replace('/###'.$dkey.'###/i',$data,$command);
    }
    return $command;
}
