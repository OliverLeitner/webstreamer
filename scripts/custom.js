//start the video stream to rtmp
function ajax_get(url){
	var request = new XMLHttpRequest();
	request.open('GET', url, true);
	request.onreadystatechange = function() {
		if (this.readyState === 4) {
			if (this.status >= 200 && this.status < 400) {
				console.log("command worked: "+url);
			} else {
				console.log("get request "+url+" couldnt be sent!");
			}
		}
	};
	request.send();
	request = null;
}

function ajax_cmd(cmd,file,name){
	ajax_get("cmd/"+cmd+".php?file="+file+"&name="+name);
	return true;
}
