== Web Video Streamer ==

System Requirements (highly depend on the number of paralell users you got visiting...):

Minimum RAM: 64 Megabytes
Minimum CPU Cores: 1
Harddisk Space: ~700 Megabytes (this holds the base operating system, all required tools, the web interface and all caching folders)

Change (17.01.2014)
IF YOU ARE RUNNING THIS IN PRODUCTION MAKE SURE THAT YOU UPDATE
TO CURRENT VERSION, THERE HAVE BEEN MULTIPLE SECURITY ISSUES.

what it does:

this program allows you to watch your home media from about everywhere, through even slow connections.

needs:

1. a webserver with php support
2. a current crtmpserver installation
3. a current libav-tools installation
5. a current libfaac installation
6. an outgoing connection thats strong enough to let you stream media (i guess at least 30kbyte/s out) if
you plan to use it from the web.

install procedure:

1. unpack the zip into your webroot or a subdir of your webroot
2. change the config.php settings to fit your needs, if needed (buffer and width/height depending on your bandwidth)
3. if youre behind a firewall, be sure you open port 8090 tcp (rtmpt) and port 1935 tcp (rtmp) 
to the outside world and forward them to the box running the rtmp server.
4. if you plan to use pseudo rtmp (no realtime encoding, dont click the start link before clicking the file...)
you will need to setup a second virtualhost directive on your server that links to your media directory on a port
of your choice (no need for a second domain, i.e. first directive on port 80, second on 8000), change the config.php according to your needs afterwards, if you want to use pseudo rtmp streaming
outside of your lan, be aware that it will need alot more bandwith than with real rtmp.
5. Make sure, that you have the right to run the php exec(); command (usually you find out which commands you are
forbidden to use by looking in your php.ini for the line "disable_functions")

tips (stuff ive come across while playing with the tool):

Instead of having a second virtualhost on the apache, could be an nginx, varnish, lighttpd serving the files.
Mediafiles and webserver dont have to be on the same machine, same goes for the thumbnails an/or the metadata
(i.e. for really large deployments: nfs mount to the mediafiles mass-create the thumbs and metadata files 
with a avconv bash script, and serve them via an nginx instance back into the webstreamer).

usage:

1. go to a directory with media files in it.
2. if you want to have the slow bandwidth version, click on start besides a media file (starts the encoding procedure).
3. click on the media file name (opens the popup with the playback).
4. to stop an encoding procedure of a media file, click on the stop besides the media file.
5. if you just click on the thumbnail or the link, you will get the high quality version of the video.

optimized to run under linux, playback tested with windows, linux. (want to assist me, please contact me),
works with html5 in most cases, so you should be fine on your iPad.

known bugs:

2. no audio file support done by now, this is something im gonna add in the future...
   (audio is working fine from video files, just no mp3/ogg support as of now)
3. some mpg files dont have good thumbnails generated.

always happy for any kind of input=)
