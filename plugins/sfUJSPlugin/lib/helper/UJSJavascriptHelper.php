<?php
//sfLoader::loadHelpers(array('UJS'));
use_helper('UJS', 'Javascript', 'Form');


/**
 * Inserts a button triggering a remote action and updating content accordingly,
 * unobtrusively
 *
 * <b>Example:</b>
 * <code>
 *  <?php echo button_to_remote('click me', array(
 *    'update' => 'emails',
 *    'url'    => '@list_emails'
 *  ), array(
 *    'class' => 'bar'
 *  )) ?>
 * </code>
 *
 * @param string The text displayed in the button
 * @param array Ajax parameters (see _UJS_remote_function for details)
 * @param array the <input> element attributes
 *
 * @return string An invisible HTML placeholder 
 */
function UJS_button_to_remote($name, $options = array(), $html_options = array())
{
  if(isset($options['update']))
  {
    $options['update'] = '#'.$options['update']; 
  }
  return UJS_button_to_function($name, _UJS_remote_function($options), $html_options);
}

  /**
   * wrapper for script.aculo.us/prototype Ajax.Autocompleter.
   * @param string name value of input field
   * @param string default value for input field
   * @param array input tag options. (size, autocomplete, etc...)
   * @param array completion options. (use_style, etc...)
   *
   * @return string input field tag, div for completion results, and
   *                 auto complete javascript tags
   */
  function UJS_input_auto_complete_tag($name, $value, $url, $tag_options = array(), $completion_options = array())
  {
    $context = sfContext::getInstance();

    $tag_options = _convert_options($tag_options);

    $response = $context->getResponse();
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/prototype');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/effects');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/controls');

    $comp_options = _convert_options($completion_options);
    if (isset($comp_options['use_style']) && $comp_options['use_style'] == true)
    {
      $response->addStylesheet(sfConfig::get('sf_prototype_web_dir').'/css/input_auto_complete_tag');
    }

    $tag_options['id'] = get_id_from_name(isset($tag_options['id']) ? $tag_options['id'] : $name);

    $javascript  = UJS_write(input_tag($name, $value, $tag_options));
    $javascript .= UJS_write(content_tag('div', '' , array('id' => $tag_options['id'].'_auto_complete', 'class' => 'auto_complete')));
    $javascript .= _UJS_auto_complete_field($tag_options['id'], $url, $comp_options);

    return $javascript;
  }
  
  /**
   * wrapper for script.aculo.us/prototype Ajax.Autocompleter.
   * @param string id value of input field
   * @param string url of module/action to execute for autocompletion
   * @param array completion options
   * @return string javascript tag for Ajax.Autocompleter
   */
  function _UJS_auto_complete_field($field_id, $url, $options = array())
  {
    $javascript = "new Ajax.Autocompleter(";

    $javascript .= "'".get_id_from_name($field_id)."', ";
    if (isset($options['update']))
    {
      $javascript .= "'".$options['update']."', ";
    }
    else
    {
      $javascript .= "'".get_id_from_name($field_id)."_auto_complete', ";
    }

    $javascript .= "'".url_for($url)."'";

    $js_options = array();
    if (isset($options['tokens']))
    {
      $js_options['tokens'] = _array_or_string_for_javascript($options['tokens']);
    }
    if (isset ($options['with']))
    {
      $js_options['callback'] = "function(element, value) { return ".$options['with']."}";
    }
    if (isset($options['indicator']))
    {
      $js_options['indicator']  = "'".$options['indicator']."'";
    }
    if (isset($options['on_show']))
    {
      $js_options['onShow'] = $options['on_show'];
    }
    if (isset($options['on_hide']))
    {
      $js_options['onHide'] = $options['on_hide'];
    }
    if (isset($options['min_chars']))
    {
      $js_options['minChars'] = $options['min_chars'];
    }
    if (isset($options['frequency']))
    {
      $js_options['frequency'] = $options['frequency'];
    }
    if (isset($options['update_element']))
    {
      $js_options['updateElement'] = $options['update_element'];
    }
    if (isset($options['after_update_element']))
    {
      $js_options['afterUpdateElement'] = $options['after_update_element'];
    }
    if (isset($options['param_name'])) 
    {
      $js_options['paramName'] = "'".$options['param_name']."'";
    }

    $javascript .= ', '._options_for_javascript($js_options).');';

    return UJS($javascript);
  }
  
  /**
   * Returns two XHTML compliant <input> tags to be used as a free-text date fields for a date range.
   * 
   * Built on the input_date_tag, the input_date_range_tag combines two input tags that allow the user
   * to specify a from and to date.  
   * You can easily implement a JavaScript calendar by enabling the 'rich' option in the 
   * <i>$options</i> parameter.  This includes a button next to the field that when clicked, 
   * will open an inline JavaScript calendar.  When a date is selected, it will automatically
   * populate the <input> tag with the proper date, formatted to the user's culture setting.
   *
   * <b>Note:</b> The <i>$name</i> parameter will automatically converted to array names. 
   * For example, a <i>$name</i> of "date" becomes date[from] and date[to]
   * 
   * <b>Options:</b>
   * - rich - If set to true, includes an inline JavaScript calendar can auto-populate the date field with the chosen date
   * - before - string to be displayed before the input_date_range_tag
   * - middle - string to be displayed between the from and to tags
   * - after - string to be displayed after the input_date_range_tag
   *
   * <b>Examples:</b>
   * <code>
   *  $date = array('from' => '2006-05-15', 'to' => '2006-06-15');
   *  echo input_date_range_tag('date', $date, array('rich' => true));
   * </code>
   *
   * <code>
   *  echo input_date_range_tag('date', null, array('middle' => ' through ', 'rich' => true));
   * </code>
   *
   * @param  string field name 
   * @param  array  dates: $value['from'] and $value['to']
   * @param  array  additional HTML compliant <input> tag parameters
   * @return string XHTML compliant <input> tag with optional JS calendar integration
   * @see input_date_tag
   */
  function UJS_input_date_range_tag($name, $value, $options = array())
  {
    $options = _parse_attributes($options);

    $before = _get_option($options, 'before', '');
    $middle = _get_option($options, 'middle', '');
    $after  = _get_option($options, 'after', '');
    $from_help  = _get_option($options, 'from_help', '');
    $to_help  = _get_option($options, 'to_help', '');

    return $before.
           UJS_input_date_tag($name.'[from]', isset($value['from']) ? $value['from'] : null, $options).
           content_tag('noscript', 
             input_tag($name.'[from]', isset($value['from']) ? $value['from'] : null, $options).
             content_tag('div', $from_help, array('class' => "input_date_range_help")), 
             array()).
           $middle.
           UJS_input_date_tag($name.'[to]',   isset($value['to'])   ? $value['to']   : null, $options).
           content_tag('noscript', 
             input_tag($name.'[to]', isset($value['to']) ? $value['to'] : null, $options).
             content_tag('div', $to_help, array('class' => "input_date_range_help")), 
             array()).
           $after;
  }

  /**
   * Returns an XHTML compliant <input> tag to be used as a free-text date field.
   * 
   * You can easily implement a JavaScript calendar by enabling the 'rich' option in the 
   * <i>$options</i> parameter.  This includes a button next to the field that when clicked, 
   * will open an inline JavaScript calendar.  When a date is selected, it will automatically
   * populate the <input> tag with the proper date, formatted to the user's culture setting. 
   * Symfony also conveniently offers the input_date_range_tag, that allows you to specify a to
   * and from date.
   *
   * <b>Options:</b>
   * - rich - If set to true, includes an inline JavaScript calendar can auto-populate the date field with the chosen date
   *
   * <b>Examples:</b>
   * <code>
   *  echo input_date_tag('date', null, array('rich' => true));
   * </code>
   *
   * @param  string field name 
   * @param  string date
   * @param  array  additional HTML compliant <input> tag parameters
   * @return string XHTML compliant <input> tag with optional JS calendar integration
   * @see input_date_range_tag
   */
  function UJS_input_date_tag($name, $value = null, $options = array())
  {
    $options = _parse_attributes($options);

    $context = sfContext::getInstance();

    $culture = _get_option($options, 'culture', $context->getUser()->getCulture());

    $withTime = _get_option($options, 'withtime', false);

    // rich control?
    if (!_get_option($options, 'rich', false))
    {
      use_helper('DateForm');

      // set culture for month tag
      $options['culture'] = $culture;

      if ($withTime)
      {
        return UJS_write(select_datetime_tag($name, $value, $options, isset($options['html']) ? $options['html'] : array()));
      }
      else
      {
        return UJS_write(select_date_tag($name, $value, $options, isset($options['html']) ? $options['html'] : array()));
      }
    }

    $pattern = _get_option($options, 'format', $withTime ? 'g' : 'd');

    $dateFormat = new sfDateFormat($culture);

    $pattern = $dateFormat->getInputPattern($pattern);

    // parse date
    if ($value === null || $value === '')
    {
      $value = '';
    }
    else
    {
      $value = $dateFormat->format($value, $pattern);
    }

    // register our javascripts and stylesheets
    $langFile = sfConfig::get('sf_calendar_web_dir').'/lang/calendar-'.strtolower(substr($culture, 0, 2));
    $jss = array(
      sfConfig::get('sf_calendar_web_dir').'/calendar',
      is_readable(sfConfig::get('sf_symfony_data_dir').'/web/'.$langFile.'.js') || is_readable(sfConfig::get('sf_web_dir').'/'.$langFile.'.js') ? $langFile : sfConfig::get('sf_calendar_web_dir').'/lang/calendar-en',
      sfConfig::get('sf_calendar_web_dir').'/calendar-setup',
    );
    foreach ($jss as $js)
    {
      $context->getResponse()->addJavascript($js);
    }

    // css
    if ($calendar_style = _get_option($options, 'css', 'skins/aqua/theme'))
    {
      $context->getResponse()->addStylesheet(sfConfig::get('sf_calendar_web_dir').'/'.$calendar_style);
    }

    // date format
    $date_format = $dateFormat->getPattern($pattern);

    // calendar date format
    $calendar_date_format = $date_format;
    $calendar_date_format = strtr($date_format, array('yyyy' => 'Y', 'yy'=>'y', 'MM' => 'm', 'M'=>'m', 'dd'=>'d', 'd'=>'e', 'HH'=>'H', 'H'=>'k', 'hh'=>'I', 'h'=>'l', 'mm'=>'M', 'ss'=>'S', 'a'=>'p'));

    $calendar_date_format = preg_replace('/([mdyhklspe])+/i', '%\\1', $calendar_date_format);

    $id_inputField = isset($options['id']) ? $options['id'] : get_id_from_name($name);
    $id_calendarButton = 'trigger_'.$id_inputField;
    $js = '
      document.getElementById("'.$id_calendarButton.'").disabled = false;
      Calendar.setup({
        inputField : "'.$id_inputField.'",
        ifFormat : "'.$calendar_date_format.'",
        daFormat : "'.$calendar_date_format.'",
        button : "'.$id_calendarButton.'"';
    
    if ($withTime)
    {
      $js .= ",\n showsTime : true";
    }

    // calendar options
    if ($calendar_options = _get_option($options, 'calendar_options'))
    {
      $js .= ",\n".$calendar_options;
    }

    $js .= '
      });
    ';

    // calendar button
    $calendar_button = '...';
    $calendar_button_type = 'txt';
    if ($calendar_button_img = _get_option($options, 'calendar_button_img'))
    {
      $calendar_button = $calendar_button_img;
      $calendar_button_type = 'img';
    }
    else if ($calendar_button_txt = _get_option($options, 'calendar_button_txt'))
    {
      $calendar_button = $calendar_button_txt;
      $calendar_button_type = 'txt';
    }

    // construct html
    if (!isset($options['size']))
    {
      // educated guess about the size
      $options['size'] = strlen($date_format)+2;
    }
    $html = UJS_write(input_tag($name, $value, $options));

    if ($calendar_button_type == 'img')
    {
      $html .= UJS_write(image_tag($calendar_button, array('id' => $id_calendarButton, 'style' => 'cursor: pointer; vertical-align: middle')));
    }
    else
    {
      $html .= UJS_write(content_tag('button', $calendar_button, array('type' => 'button', 'disabled' => 'disabled', 'onclick' => 'return false', 'id' => $id_calendarButton)));
    }

    if (_get_option($options, 'with_format'))
    {
      $html .= '('.$date_format.')';
    }

    // add javascript
    $html .= UJS($js);

    return $html;
  }

