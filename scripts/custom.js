//start the video stream to rtmp
function ajax_get(url){
    var request = new XMLHttpRequest();
    var response = "";
    request.open('GET', url, true);
    request.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (this.status >= 200 && this.status < 400) {
                response = this.responseText;
            } else {
                console.log("get request "+url+" couldnt be sent!");
            }
        }
    };
    request.send();
    request = null;
    return response;
}

function ajax_cmd(cmd,file,name){
    var out = ajax_get("cmd/"+cmd+".php?file="+file+"&name="+name);
    console.log("command response: " + out);
    return true;
}
