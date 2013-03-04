<?php

  /**
   *
   *  JWFLVMediaPlayerHelper.php
   *
   *  JWFLVMediaPlayer options:
   *    The basics
   *      1. height (320): Sets the overall height of the player/rotator.
   *      2. width (260): Sets the overall width of the player/rotator.
   *      3. file (undefined): Sets the location of the file to play. The mediaplayer can play a single MP3, FLV, SWF, JPG, GIF, PNG, H264 file or a playlist. The imagerotator only plays playlists. The wmvplayer only plays WMV and WMA files and MMS streams.
   *      4. image (undefined): If you play a sound or movie, set this to the url of a preview image. When using a playlist, you can set an image for every entry.
   *      5. id (undefined): Use this to set the RTMP stream identifier (example) with the mediaplayer. The ID will also be sent to statistics callbacks. If you play a playlist, you can set an id for every entry.
   *      6. searchbar (true): Set this to false to hide the searchbar below the display. You can set the search destination with the searchlink flashvar.
   *    The colors
   *      1. backcolor (0xFFFFFF): Backgroundcolor of the controls, in HEX format.
   *      2. frontcolor (0x000000): Texts & buttons color of the controls, in HEX format.
   *      3. lightcolor (0x000000): Rollover color of the controls, in HEX format.
   *      4. screencolor (0x000000): Color of the display area, in HEX format. With the rotator, change this to your HTML page's color make images of different sizes blend nicely.
   *    Display appearance
   *      1. logo (undefined): Set this to an image that can be put as a watermark logo in the top right corner of the display. Transparent PNG files give the best results (example).
   *      2. overstretch (false): Sets how to stretch images/movies to make them fit the display. The default stretches to fit the display. Set this to true to stretch them proportionally to fill the display, fit to stretch them disproportionally and none to keep original dimensions.
   *      3. showeq (false): Set this to true to show a (fake) equalizer at the bottom of the display. Nice for MP3 files.
   *      4. showicons (true): Set this to false to hide the activity icon and play button in the middle of the display.
   *      5. transition (random): Only for the rotator. Sets the transition to use between images. The default, random, randomly pick a transition. To restrict to a certain transition, use these values: fade, bgfade, blocks, bubbles, circles, flash, fluids, lines or slowfade
   *    Controlbar appearance
   *      1. shownavigation (true): Set this to false to completely hide the controlbar.
   *      2. showstop (false): Not for the rotator. Set this to true to show a stop button in the controlbar.
   *      3. showdigits (true): Not for the rotator. Set this to false to hide the elapsed/remaining digits in the controlbar.
   *      4. showdownload (false): Not for the rotator. Set this to true to show a button in the player controlbar which links to the link flashvar.
   *      5. usefullscreen (true): Not for the rotator. Set this to false to hide the fullscreen button and disable fullscreen.
   *    Playlist appearance (only for the mediaplayer)
   *      1. autoscroll (false): Set this to true to automatically scroll through the playlist on rollover, instead of using a scrollbar.
   *      2. displayheight (height): Set this smaller as the height to show a playlist below the display (example). If you set it the same as the height, the controlbar will auto-hide on top of the video (example).
   *      3. displaywidth (width): Set this smaller as the width to show a playlist to the right of the display (example).
   *      4. thumbsinplaylist (true): Set this to false to hide preview images in the display (and restrict the buttons to a single line).
   *    Playback behaviour
   *      1. audio (undefined): Not for the wmvplayer. Assigns an additional, synchronized MP3. Use this for a closed audio description or director's comments with the mediaplayer or background music with the rotator. When using the mediaplayer and a playlist, you can assign audio to every entry.
   *      2. autostart (false for players, true for rotator): Set this to true in the player to automatically start playing when the page loads, or set this to false with the rotator to prevent the automatic rotation.
   *      3. bufferlength (3): Not for the rotator. Sets the number of seconds a video should be buffered before the players starts playback. Set this small for fast connections or short videos and big for slow connections.
   *      4. captions (undefined): Only for the mediaplayer. Assigns closed captions. Captions should be in TimedText format (example). When using a playlist, you can assign captions for every entry.
   *      5. fallback (undefined): Only for the mediaplayer. If you play an MP4 file, set here the location of an FLV fallback. It'll automatically be picked by older flash players (example).
   *      6. repeat (false): Not for the wmvplayer. Set this to true to automatically repeat playback of all files. Set this to list to playback an entire playlist once.
   *      7. rotatetime (5):Not for the wmvplayer. Sets the number of seconds an image is played back.
   *      8. shuffle (false): Not for the wmvplayer. Set this to true to playback a playlist in random order.
   *      9. smoothing (true): Only for the mediaplayer. Set this to false to turn of the smoothing of video. Quality will decrease, but performance will increase. Good for HD files and slower computers. Example.
   *      10. volume (80): sets the startup volume for playback of sounds, movies and audiotracks.
   *    External communication
   *      1. callback (undefined): Only for the mediaplayer. Set this to a serverside script that can process statistics. The player will send it a POST every time an item starts/stops. To send callbacks automatically to Google Analytics, set this to urchin (if you use the old urchinTracker code) or analytics (if you use the new pageTracker code).
   *      2. enablejs (false): Not for the wmvplayer (which is already entirely JS). Set this to true to enable javascript interaction. This'll only work online! Javascript interaction includes playback control, asynchroneous loading of media files and return of track information. More info at this demo page.
   *      3. javascriptid (undefined): Not for the wmvplayer. If you interact with multiple mediaplayers/rotators in javascript, use this flashvar to give each of them a unique ID. More info at this demo page.
   *      4. link (url): Set this to an external URL or downloadeable version of the file. This link is assigned to the display, logo and link button. With playlists, set links for every entry in the XML.
   *      5. linkfromdisplay (false): Set this to true to make a click on the display result in a jump to the webpage assigned to the link flashvar.
   *      6. linktarget (_self): Set this to the frame you want hyperlinks to open in. Set it to _blank to open links in a new window or _top to open in the top frame.
   *      7. recommendations (undefined): Only for the mediaplayer. Set this to an XML with items you want to recommend. The thumbs will show up when the current movie stops playing, just like YouTube. Here's an example setup and example XML.
   *      8. searchlink (http://search.longtail.tv/?q=): Only for the mediaplayer. Sets the destination of the searchbar. The default is the LongTail search page (which you can brand with your logo, colors and XML), but you can set other destinations (e.g. http://yoube.com/results?search_query=). Use the searchbar flashvar to hide the bar altogether.
   *      9. streamscript (undefined): Only for the mediaplayer. Set this to the URL of a script to use for http streaming movies. The parameters file and pos are sent to the script. Here's more info and an example script. If you use LigHTTPD streaming, set this to lighttpd.
   *      10. type (mp3,flv,rtmp,jpg,png,gif,swf): Only for the mediaplayer, which determines the type of file to play based upon the last three characters of the file flashvar. This doesn't work with database id's or mod_rewrite, so you can set this flashvar to the correct filetype. If not sure, the player assumes a playlist is loaded.
   *
   */

  function _jwflvmp_options($options)
  {
    $keys = array_keys($options);
    $str = '&';

    foreach ($keys as $key) {
      $str .= $key.'='.$options[$key].'&';
    }

    return substr($str, 0, strlen($str) - 1);
  }

  /**
   *
   *  Returns a common JW FLV Media Player instance.
   *  @param string $url an url for the media player.
   *  @param integer $width media player's width.
   *  @param integer $height media player's height.
   *  @param array options options for JW FLV Media Player.
   *  @return string a JW FLV Media Player instance.
   *
   */
  function _mediaplayer_common($url, $width, $height, $options = array())
  {
    sfLoader::loadHelpers(array('Url', 'I18N'));
    $str = tag('embed',
               array('src' => _compute_public_path('mediaplayer', 'pmJWFLVMediaPlayerPlugin/flash', 'swf'),
                     'width' => "$width",
                     'height' => "$height",
                     'allowscriptaccess' => 'always',
                     'allowfullscreen' => 'true',
                     'wmode' => 'transparent',
                     'flashvars' => "file=$url"._jwflvmp_options($options)),
               false);
    return $str.content_tag('noembed', link_to(image_tag('pmJWFLVMediaPlayer/images/flashplayer.jpg').__(' Su navegador no tiene el plugin para ver contenidos flash. Clickee aquí para conseguir el plugin'), "http://www.adobe.com/go/BPCLG"));
  }

  /**
   *
   *  Returns a JW FLV Media Player instance for video files.
   *  @param string $url an url for the media player.
   *  @param integer $width media player's width.
   *  @param integer $height media player's height.
   *  @param array options options for JW FLV Media Player.
   *  @return string a JW FLV Media Player instance.
   *
   */
  function mediaplayer_video($url, $width = 300, $height = 300, $options = array())
  {
    return _mediaplayer_common($url, $width, $height, $options);
  }

  /**
   *
   *  Returns a JW FLV Media Player instance for audio files.
   *  @param string $url an url for the media player.
   *  @param array options options for JW FLV Media Player.
   *  @return string a JW FLV Media Player instance.
   *
   */
  function mediaplayer_audio($url, $options = array())
  {
    return _mediaplayer_common($url, 300, 20, $options);
  }

?>
