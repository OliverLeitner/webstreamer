<?php
/**
 * send an encoding request
 * to avconv
 */
require_once "../loader.php";
$checkfile = __FILE__;
$checkdir = glob($checkfile)[0];
if(is_writable($checkdir))
{
    $params = array();
    $params['file_path'] = escapeshellarg($_GET['file']);
    $params['uid'] = md5($_SERVER['REMOTE_ADDR'].$_GET['name']); //multi user stuff...
    $params['crtmpserver'] = $crtmpserver;
    $params['crtmp_in_port'] = $crtmp_in_port;
    $check_stcmd = buildCmd($params,$commands['check_subtitle']);
    $check_subtitle = shell_exec($check_stcmd);
    $subtitles = "";
    if($check_subtitle != ""){
        $subtitles = buildCmd($params,$commands['subtitles']);
    }
    $check_cmd = buildCmd($params,$commands['ps_get']);
    $checked = shell_exec($check_cmd);
    //only start encoder if its not already running...
    if($checked == ""){
        $cmd = buildCmd($params,$commands['start_avconv']);
        $debug = pclose(popen($cmd,"r"));
        var_dump($debug);
    }
}
else
{
    die("cannot write to directory, please make sure, that the webserver user has write access to cmd/ directory");
}
