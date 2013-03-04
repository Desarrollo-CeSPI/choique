<?php
sfLoader::loadHelpers(array('Asset'));
//use_javascript('/sfUJSPlugin/js/jquery');

/**
 * Inserts JavaScript code unobtrusively
 *
 * <b>Example:</b>
 * <code>
 *  <?php UJS("alert('foobar');") ?>
 * </code>
 *
 * @param  string JavaScript code
 */
function UJS($script)
{
  $response = sfContext::getInstance()->getResponse();
  $response->setParameter('script', $response->getParameter('script', '', 'symfony/view/UJS').$script.";\n", 'symfony/view/UJS');
}

/**
 * Starts a JavaScript code block for unobtrusive insertion
 *
 * <b>Example:</b>
 * <code>
 *  <?php UJS_block() ?>
 *    alert('foobar');
 *  <?php UJS_end_block() ?> 
 * </code>
 *
 * @see UJS_end_block
 */
function UJS_block()
{
  ob_start();
  ob_implicit_flush(0);
}

/**
 * Ends a JavaScript code block and inserts the JavaScript code unobtrusively
 *
 * @see UJS_block
 */
function UJS_end_block()
{
  $content = ob_get_clean();
  UJS($content);
}

/**
 * Gets a template-unique ID
 *
 * @return integer the incremental ID
 */
 function UJS_incremental_id()
{
  static $ujs_incremental_id = 0;
  return 'UJS_'.$ujs_incremental_id++;
}

/**
 * Inserts an invisible placeholder for adding something to the DOM with UJS afterwards
 *
 * @param string the id attribute of the placeholder
 *
 * @return string an HTML <span> tag 
 */
function UJS_placeholder($id)
{
  return content_tag('span', '', array('style' => 'display: none', 'class' => 'UJS_placeholder', 'id' => $id));
}

/**
 * Replaces an existing DOM element with some content unobtrusively
 *
 * <b>Example:</b>
 * <code>
 *  <?php UJS_replace('#foo', '<div>Hello, world!</div>') ?>
 * </code>
 *
 * @param string the CSS3 selector to the DOM element(s) to replace
 * @param string the HTML content to use for replacement
 *
 */
function UJS_replace($selector, $html_code)
{
  $html_code = preg_replace('/\r\n|\n|\r/', "\\n", $html_code);
  $html_code = preg_replace("/'/", "\'", $html_code);
  UJS(sprintf("jQuery('%s').after('%s');jQuery('%s').remove()", $selector, $html_code, $selector));
}

/**
 * Adds some some content unobtrusively
 *
 * <b>Example:</b>
 * <code>
 *  <?php UJS_write('<div>Hello, world!</div>') ?>
 * </code>
 *
 * @param string the HTML content to use for insertion
 *
 */
function UJS_write($html)
{
  $id = UJS_incremental_id();
  UJS_replace("#".$id, $html);
  return UJS_placeholder($id);
}

/**
 * Starts a HTML code block for unobtrusive content insertion
 *
 * <b>Example:</b>
 * <code>
 *  <?php UJS_write_block() ?>
 *    <div>Hello, world!</div>
 *  <?php UJS_end_write_block() ?> 
 * </code>
 *
 * @see UJS_end_write_block
 */
function UJS_write_block()
{
  ob_start();
  ob_implicit_flush(0);  
}

/**
 * Ends a HTML code block for unobtrusive content insertion
 *
 * @see UJS_write_block
 */
function UJS_end_write_block()
{
  $content = ob_get_clean();
  UJS_write($content);
}

/**
 * Changes some attributes of an existing DOM element unobtrusively
 * Alias for UJS_attr()
 *
 * @see UJS_attr
 */
function UJS_change_attributes($selector, $html_options = array())
{
	return UJS_attr($selector, $html_options);
}

/**
 * Changes some attributes of an existing DOM element unobtrusively
 *
 * <b>Example:</b>
 * <code>
 *  <?php UJS_attr('#foo', array('class' => 'bar', 'alt' => 'foobar')) ?>
 *  // You can also use the string syntax
 *  <?php UJS_attr('#foo', 'class=bar alt=foobar') ?>
 * </code>
 *
 * @param string the CSS3 selector to the DOM element(s) to replace
 * @param array the element attributes to update
 */
function UJS_attr($selector, $html_options = array())
{
  $html_options = _parse_attributes($html_options);
  $response = sfContext::getInstance()->getResponse();
  $script = $response->getParameter('script', '', 'symfony/view/UJS');
  $attributes = '';
  foreach($html_options as $key => $value)
  {
    $attributes .= sprintf(".attr('%s', '%s')", $key, $value);
  }
  $script .= sprintf("jQuery('%s')%s;\n", $selector, $attributes);
  $response->setParameter('script', $script, 'symfony/view/UJS');
}

/**
 * Changes some style attributes of an existing DOM element unobtrusively
 * Alias for UJS_css()
 *
 * @see UJS_css
 */
function UJS_change_style($selector, $css_options = array())
{
	return UJS_css($selector, $css_options);
}

/**
 * Changes some style attributes of an existing DOM element unobtrusively
 *
 * <b>Example:</b>
 * <code>
 *  <?php UJS_attr('#foo', array('display' => 'none', 'text-decoration' => 'underline')) ?>
 *  // You can also use the string syntax
 *  <?php UJS_attr('#foo', 'display:none text-decoration:underline') ?>
 * </code>
 *
 * @param string the CSS3 selector to the DOM element(s) to replace
 * @param array the style attributes to update
 */
function UJS_css($selector, $css_options = array())
{
  if(is_string($css_options))
  {
    preg_match_all('/
      \s*([\w-]+)             # key (may contain dash)            \\1
      \s*:\s*                 # :
      (\'|")?                 # values may be included in \' or " \\2
      (.*?)                   # value                             \\3
      (?(2) \\2)              # matching \' or " if needed        \\4
      [\s;]*(?=
        (?=[\w-]+\s*:) | \s*$  # followed by another key: or the end of the string
      )
    /x', $css_options, $matches, PREG_SET_ORDER);
    $css_options = array();
    foreach ($matches as $val)
    {
      $css_options[$val[1]] = sfToolkit::literalize($val[3]);
    }
  }

  $response = sfContext::getInstance()->getResponse();
  $script = $response->getParameter('script', '', 'symfony/view/UJS');
  $attributes = '';
  foreach($css_options as $key => $value)
  {
    $attributes .= sprintf(".css('%s', '%s')", $key, $value);
  }
  $script .= sprintf("jQuery('%s')%s;\n", $selector, $attributes);
  $response->setParameter('script', $script, 'symfony/view/UJS');
}

/**
 * Adds an event listener to an existing DOM element unobtrusively
 *
 * <b>Example:</b>
 * <code>
 *  <?php UJS_add_behaviour('#foo', 'click', "alert('foobar')") ?>
 * </code>
 *
 * @param string the CSS3 selector to the DOM element(s) concerned by the behaviour
 * @param string the event name (without leading 'on')
 * @param string JavaScript code
 */
function UJS_add_behaviour($selector, $event, $script)
{
  UJS(sprintf("jQuery('%s').%s(function() { %s })", $selector, $event, $script));  
}

/**
 * Inserts a link triggering a script unobtrusively
 *
 * <b>Example:</b>
 * <code>
 *  <?php echo UJS_link_to_function('click me', "alert('foo')", array('class' => 'bar')) ?>
 *  // You can also use the string syntax
 *  <?php echo UJS_link_to_function('click me', "alert('foo')", 'class=bar') ?>
 * </code>
 *
 * @param string The text displayed in the link
 * @param string JavaScript code
 * @param array the <a> element attributes
 *
 * @return string An invisible HTML placeholder 
 */
function UJS_link_to_function($name, $script, $html_options = array())
{
  $html_options = _parse_attributes($html_options);
  $html_options['href'] = isset($html_options['href']) ? $html_options['href'] : '#';
  $html_options['onclick'] = $script.'; return false;';
  return UJS_write(content_tag('a', $name, $html_options, true));
}

function UJS_lightview_ajax($href, $thumb, $title = '', $caption = '', $options = array())
{
  return _UJS_lightview_common($href, $thumb, $title, $caption, $options, 'ajax');
  
}

function UJS_lightview_media($href, $thumb, $title = '', $caption = '', $options = array())
{
  return _UJS_lightview_common($href, $thumb, $title, $caption, $options);
}

function UJS_lightview_image($href, $thumb, $title = '', $caption = '', $gallery_name = '', $options = array())
{
  return _UJS_lightview_common($href, $thumb, $title, $caption, $options, ($gallery_name)?"gallery[$gallery_name]":"");
}

function _UJS_lightview_common($href, $thumb, $title, $caption, $options, $rel = '')
{
  sfLoader::loadHelpers(array('Lightview'));
  _add_resources();

  return UJS_write(content_tag('a',
                     $thumb,
                     array('href' => $href,
                           'class' => 'lightview',
                           'title' => "$title::$caption::"._parse_options($options),
                           'rel' => $rel)));
}
/**
 * Inserts a button (input type=button) triggering a script unobtrusively
 *
 * <b>Example:</b>
 * <code>
 *  <?php echo UJS_button_to_function('click me', "alert('foo')", array('class' => 'bar')) ?>
 *  // You can also use the string syntax
 *  <?php echo UJS_button_to_function('click me', "alert('foo')", 'class=bar') ?>
 * </code>
 *
 * @param string The text displayed in the button
 * @param string JavaScript code
 * @param array the <input> element attributes
 *
 * @return string An invisible HTML placeholder 
 */
function UJS_button_to_function($name, $script, $html_options = array())
{
  $html_options = _parse_attributes($html_options);
  $html_options['type'] = 'button';
  $html_options['value'] = $name;
  $html_options['onclick'] = $script;
  return UJS_write(tag('input', $html_options, false, true));
}

/**
 * Execute a remote action in the background using XMLHttpRequest
 * defined by 'url' (using the 'url_for()' format) 
 * The result of that request can then be inserted into a
 * DOM object whose selector can be specified with 'update'.
 * Caution: This function is internal to the helper and does not produce UJS code
 *
 * Examples:
 *  <?php echo _UJS_remote_function(array(
 *    'update' => '#posts',
 *    'url'    => 'destroy?id='.$post.id,
 *  )) ?>
 *  <?php echo _UJS_remote_function(array(
 *    'update' => '#emails',
 *    'url'    => '@list_emails',
 *  )) ?>
 *
 * You can also specify a hash for 'update' to allow for
 * easy redirection of output to an other DOM element if a server-side error occurs:
 *
 * Example:
 *  <?php echo _UJS_remote_function(array(
 *      'update' => array('success' => '#posts', 'failure' => '#error'),
 *      'url'    => 'destroy?id='.$post.id,
 *  )) ?>
 *
 * Optionally, you can use the 'position' parameter to influence
 * how the target DOM element is updated. It must be one of
 * 'before', 'top', 'bottom', or 'after'.
 *
 * Example:
 *  <?php echo _UJS_remote_function(array(
 *    'update'   => '#posts',
 *    'position' => 'after',
 *    'url'      => 'destroy?id='.$post.id,
 *  )) ?>
 *
 * By default, these remote requests are processed asynchronous during
 * which various JavaScript callbacks can be triggered (for progress indicators and
 * the likes). All callbacks get access to the 'request' object,
 * which holds the underlying XMLHttpRequest.
 *
 * To access the server response, use 'request.responseText', to
 * find out the HTTP status, use 'request.status'.
 *
 * Example:
 *  <?php echo link_to_remote($word, array(
 *    'url'      => '@undo?n='.$word_counter,
 *    'complete' => 'undoRequestCompleted(request)'
 *  )) ?>
 *
 * The callbacks that may be specified are (in order):
 *
 * 'beforeSend'              Called before the XMLHttpRequest is executed.
 *                           (synonyms: 'loading', 'loaded', 'interactive')
 * 'success'                 Called when the XMLHttpRequest is completed,
 *                           and the HTTP status code is in the 2XX range.
 * 'error'                   Called when the XMLHttpRequest is completed,
 *                           and the HTTP status code is not in the 2XX
 *                           range. (synonym: 'failure')
 * 'complete'                Called when the XMLHttpRequest is complete
 *                           (fires after success/failure if they are present).,
 *
 * If you for some reason or another need synchronous processing (that'll
 * block the browser while the request is happening), you can specify
 * 'type' => 'synchronous'.
 *
 * You can customize further browser side call logic by passing
 * in JavaScript code snippets via some optional parameters. In
 * their order of use these are:
 *
 * 'confirm'             Adds confirmation dialog.
 * 'condition'           Perform remote request conditionally
 *                       by this expression. Use this to
 *                       describe browser-side conditions when
 *                       request should not be initiated.
 *
 * @param array Ajax parameters
 */
function _UJS_remote_function($options)
{
  $condition    = '';
  $ajax_options = array();
  
  $ajax_options[] = "url: '".url_for($options['url'])."'";
  
  if (isset($options['position']))
  {
    switch($options['position'])
    {
      case "before":
        $update_position = 'before';
        break;
      case "top":
      case "prepend":
        $update_position = 'prepend';
        break;
      case "bottom":
      case "append":
        $update_position = 'append';
        break;
      case "after":
        $update_position = 'after';
        break;
      default:
        $update_position = 'html';
    }
  }
  else
  {
    $update_position = 'html';
  }

  if (isset($options['type']) && $options['type'] == "synchronous")
  {
    $ajax_options[] = "async: false";
  }

  if (isset($options['type']) && $options['type'] != "synchronous" && $options['type'] != "asynchronous")
  {
    $ajax_options[] = "type: ".strtoupper($options['type']);
  }
  else if (isset($options['method']))
  {
    $ajax_options[] = "type: ".strtoupper($options['method']);
  }

  if (isset($options['with']))
  {
    $ajax_options[] = "data: ".$options['with'];
  }
  else if (isset($options['data']))
  {
    $ajax_options[] = "data: ".$options['data'];
  }

  if (isset($options['update']) && is_array($options['update']))
  {
    if (isset($options['update']['success']))
    {
      $ajax_options[] = "success: function(response) { jQuery('".$options['update']['success']."').".$update_position."(response) }";
    }
    if (isset($options['update']['failure']))
    {
      $ajax_options[] = "error: function(response) { jQuery('".$options['update']['failure']."').".$update_position."(response.responseText) }";
    }
  }
  else if (isset($options['update']))
  {
    $ajax_options[] = "success: function(response) { jQuery('".$options['update']."').".$update_position."(response) }";
  }
  else if (isset($options['success']))
  {
    $ajax_options[] = "success: function(response) { jQuery('".$options['success']."').".$update_position."(response) }";
  }
  
  if (isset($options['confirm']))
  {
    $condition .= "if(confirm('".$options['confirm']."')) ";
  }
  if (isset($options['condition']))
  {
    $condition .= "if(".$options['condition'].") ";
  }
  
  return $condition."jQuery.ajax({".join(', ', $ajax_options)."})";
}

/**
 * Transforms a regular link (or button) into an Ajax one unobtrusively
 *
 * @param string the CSS3 selector to the DOM element(s) concerned by the behaviour
 * @param array Hash of Ajax options (see _UJS_remote_function for details)
 *
 * @see _UJS_remote_function 
 */
function UJS_ajaxify_link($selector, $ajax)
{
  UJS(sprintf("jQuery('%s').click(function() { %s; return false })", $selector, _UJS_remote_function($ajax)));  
}

/**
 * Transforms a form into an Ajax one unobtrusively
 *
 * @param string the CSS3 selector to the DOM element(s) concerned by the behaviour
 * @param array Hash of Ajax options (see _UJS_remote_function for details)
 *
 * @see _UJS_remote_function 
 */
function UJS_ajaxify_form($selector, $ajax)
{
  UJS(sprintf("jQuery('%s').submit(function() { %s; return false })", $selector, _UJS_remote_function($ajax)));  
}

/**
 * Transforms a link, a button or form into an Ajax one unobtrusively
 *
 * @param string the CSS3 selector to the DOM element(s) concerned by the behaviour
 * @param array Hash of Ajax options (see _UJS_remote_function for details)
 *
 * @see _UJS_remote_function 
 */
function UJS_ajaxify($selector, $ajax)
{
  $UJS = "elements = jQuery('%s');";
  $UJS .= "if(elements.is('form')) handler = 'submit'; else handler = 'click'; ";
  $UJS .= "elements.bind(handler, function() { %s; return false }); ";
  UJS(sprintf($UJS, $selector, _UJS_remote_function($ajax))); 
}

/**
 * Defines how the UJS code is added for the current request
 *
 * @param boolean True for static code (attached in another file), false for code embedded in the response contents
 */
function UJS_set_inclusion($static = true)
{
  sfContext::getInstance()->getResponse()->setParameter('static', $static, 'symfony/view/UJS');
}

/**
 * Returns previously added UJS code
 * The static setting can be set in three places:
 *  - as a parameter to the get_UJS() helper
 *  - as a parameter of the response object (name 'static', namespace 'symfony/view/UJS')
 *  - as a settings of the app.yml (name 'app_UJSPlugin_static')
 * The default value is true
 *
 * @param boolean True for static code (attached in another file), false otherwise (default)
 *
 * @return string if $static=false, a JavaScript code block
 */
function get_UJS($static = null)
{
  $response = sfContext::getInstance()->getResponse();
  $response->setParameter('included', true, 'symfony/view/UJS');
  
  if(is_null($static))
  {
    if(is_null($response_static = $response->getParameter('static', null, 'symfony/view/UJS')))
    {
      $static = sfConfig::get('app_UJSPlugin_static', true);
    }
    else
    {
      $static = $response_static;
    }
  }
  if ($UJS = $response->getParameter('script', false, 'symfony/view/UJS'))
  {
	  $code = sprintf("jQuery(document).ready(function(){\n%s })", $UJS);
	  if($static)
	  {
      // JavaScript code is in another file
      use_helper('PJS');
      $key = md5(sfRouting::getInstance()->getCurrentInternalUri());
      sfContext::getInstance()->getUser()->setAttribute('UJS_'.$key, $code, 'symfony/UJS');
      use_pjs('sfUJS/script?key='.$key);
    }
    else
    {
      // JavaScript code appears in the document
      return sprintf("<script>\n//  <![CDATA[\n%s\n//  ]]>\n</script>", $code);
    }
  }
  return '';
}

/**
 * Prints previously added UJS code
 *
 * @param boolean True for static code (attached in another file), false otherwise (default)
 *
 */
function include_UJS($static = false)
{
  echo get_UJS($static);
}

function UJS_link_to_remote($name, $options = array(), $html_options = array())
{
  if(isset($options['update']))
  {
    $options['update'] = '#'.$options['update']; 
  }
  return UJS_link_to_function($name, _UJS_remote_function($options), $html_options);
}
