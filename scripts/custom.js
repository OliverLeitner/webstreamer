//start the video stream to rtmp
function ajax_startstream(file,name){
	$.get("cmd/start.php", { file: file, name: name } );
	return true;
}

//stop the video stream to rtmp
function ajax_stopstream(file,name){
        $.get("cmd/stop.php", { file: file, name: name } );
        return true;
}
