<?php
/* popup player templates */
/*
 * ###players_styles###
 * ###players_scripts###
 * ###title###
 * ###players_content_tag###
 * ###players_content_script###
 * ###jsquery_dir###
 * ###swfobject###
 * 
 */
function doPlayer($main_style,$style,$headscript,$title,$tag,$bodyscript,$js_dir,$jquery){
	 $template = file_get_contents("templates/player.html");
	 $template = str_replace("###players_styles###", $style, $template);
	 $template = str_replace("###players_scripts###", $headscript, $template);
	 $template = str_replace("###title###", htmlentities($title), $template);
	 $template = str_replace("###players_content_tag###", $tag, $template);
	 $template = str_replace("###players_content_script###", $bodyscript, $template);
	 $template = str_replace("###js_dir###", $js_dir, $template);
	 $template = str_replace("###jquery_dir###", $jquery, $template);
	 $template = str_replace("###main_style###", $main_style, $template);
	 return $template;
}
?>
