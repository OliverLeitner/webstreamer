<?php
/* main website templates */
/*
 * ###js_root###
 * ###height###
 * ###width###
 * ###folder###
 * ###list_folders###
 * ###list_files###
 * ###style###
 */
function doIndex($style,$jsroot,$height,$width,$folder,$list_folders,$list_files){
    $template = file_get_contents("templates/index.html");
    $template = str_replace("###width###", $width+20, $template); //we are adding 20px for the controls...
    $template = str_replace("###height###", $height, $template);
    $template = str_replace("###folder###", htmlentities($folder), $template);
    $template = str_replace("###list_folders###", $list_folders, $template);
    $template = str_replace("###list_files###", $list_files, $template);
    $template = str_replace("###js_dir###", $jsroot, $template);
    $template = str_replace("###main_style###", $style, $template);
    return $template;
}
