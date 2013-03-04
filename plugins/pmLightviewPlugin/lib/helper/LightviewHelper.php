<?php
  /**
   *
   *  LightviewHelper.php
   *
   *  Lightview options:
   *    ajax:       Additional options you would normal put as options on an Ajax.Request.
   *    autosize:   For inline and ajax views, resizes the view to the element recieves.
   *                Make sure it has dimensions otherwise this will give unexpected results.
   *    width:      integer, sets the width of the view in pixels
   *    height:     integer, sets the height of the view in pixels
   *    flashvars:  flashvars to put on the swf object
   *    fullscreen: 'true' or 'false', shows the view in fullscreen, usable on iframes.
   *    loop:       'true' or 'false', for quicktime movies
   *    menubar:    'true' or 'false', show or hide the menubar (title/caption/close).
   *    scrolling:  'true' or 'false'. For inline, iframe and ajax views, sets scrolling to auto when true or disables scrolling when false.
   *    topclose:   'true' or 'false', show the sliding close button instead of the static one.
   *
   *  @author Patricio Mac Adden <pmacadden@desarrollo.cespi.unlp.edu.ar>
   *  @link http://www.nickstakenburg.com/projects/lightview
   *  @version 0.0.1
   *
   */

  /**
   *
   *  Adds needed javascript and css resources for runnning Lightview.
   *
   */
  function _add_resources()
  {
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript('/sfPrototypePlugin/js/prototype');
    $response->addJavascript('/sfPrototypePlugin/js/scriptaculous?load=effects');
    $response->addJavascript('/pmLightviewPlugin/js/lightview');

    $response->addStylesheet('/pmLightviewPlugin/css/lightview');
  }

  /**
   *
   *  Returns a Lightview link.
   *  @param string $href an image, iframe, ajax or other media href.
   *  @param string $thumb a descriptive string of $href or a html anchor tag (<a href=...></a>).
   *  @param string $title a title for $href.
   *  @param string $caption a caption (description) for $href.
   *  @param string $options options for Lightview.
   *  @param string $rel Lightview link type.
   *  @return string a Lightview link.
   *
   */
  function _lightview_common($href, $thumb, $title, $caption, $options, $rel = '')
  {
    _add_resources();

    return content_tag('a',
                       $thumb,
                       array('href' => $href,
                             'class' => 'lightview',
                             'title' => "$title::$caption::"._parse_options($options),
                             'rel' => $rel));
  }
 
  function _parse_options($options)
  {
    $keys = array_keys($options);
    $str = '';
    foreach ($keys as $key):
      $str .= $key.': '.$options[$key].', ';
    endforeach;
    return substr($str, 0, strlen($str) - 2);
  }

  /**
   *
   *  Returns a Lightview image link.
   *  @param string $href an image, iframe, ajax or other media href.
   *  @param string $thumb a descriptive string of $href or a html anchor tag (<a href=...></a>).
   *  @param string $title a title for $href.
   *  @param string $caption a caption (description) for $href.
   *  @param string $gallery_name
   *  @param string $options options for Lightview.
   *  @return string a Lightview link.
   *
   */
  function lightview_image($href, $thumb, $title = '', $caption = '', $gallery_name = '', $options = array())
  {
    return _lightview_common($href, $thumb, $title, $caption, $options, ($gallery_name)?"gallery[$gallery_name]":"");
  }

  /**
   *
   *  Returns a Lightview iframe link.
   *  @param string $href an image, iframe, ajax or other media href.
   *  @param string $thumb a descriptive string of $href or a html anchor tag (<a href=...></a>).
   *  @param string $title a title for $href.
   *  @param string $caption a caption (description) for $href.
   *  @param string $options options for Lightview.
   *  @return string a Lightview link.
   *
   */
  function lightview_iframe($href, $thumb, $title = '', $caption = '', $options = array())
  {
    return _lightview_common($href, $thumb, $title, $caption, $options, 'iframe');
  }

  /**
   *
   *  Returns a Lightview media link.
   *  @param string $href an image, iframe, ajax or other media href.
   *  @param string $thumb a descriptive string of $href or a html anchor tag (<a href=...></a>).
   *  @param string $title a title for $href.
   *  @param string $caption a caption (description) for $href.
   *  @param string $options options for Lightview.
   *  @return string a Lightview link.
   *
   */
  function lightview_media($href, $thumb, $title = '', $caption = '', $options = array())
  {
    return _lightview_common($href, $thumb, $title, $caption, $options);
  }

  /**
   *
   *  Returns a Lightview ajax link.
   *  @param string $href an image, iframe, ajax or other media href.
   *  @param string $thumb a descriptive string of $href or a html anchor tag (<a href=...></a>).
   *  @param string $title a title for $href.
   *  @param string $caption a caption (description) for $href.
   *  @param string $options options for Lightview.
   *  @return string a Lightview link.
   *
   */
  function lightview_ajax($href, $thumb, $title = '', $caption = '', $options = array())
  {
    return _lightview_common($href, $thumb, $title, $caption, $options, 'ajax');
  }

?>
