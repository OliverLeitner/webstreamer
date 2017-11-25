<?php
/**
 * keeping
 * all the central
 * things central
 */
//first, enable debugging for everything
ini_set("display_errors","On");
error_reporting(E_ALL);

//lets do buffering
ob_start();

//define our webroot...
$webroot = "/var/www/webstreamer";

//start loading things...
require_once $webroot."/functions/functs.php";
require_once $webroot."/functions/definitions.php";
require_once $webroot."/config.php";

//initialize memcached support if possible
$m = "";
if(isset($memcservers) && $memcservers != ""){
    $m = init_memcache($memcservers,":"); 
}
