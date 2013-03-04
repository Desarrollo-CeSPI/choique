<?php

  function _osflv_clean_options(&$options)
  {
    if (isset($options['autoplay']) && '' != trim($options['autoplay']))
    {
      $options['autoplay'] = $options['autoplay'] ? 'on' : 'off';
    }

    if (isset($options['autoload']) && '' != trim($options['autoload']))
    {
      $options['autoload'] = $options['autoload'] ? 'on' : 'off';
    }

    if (isset($options['autorewind']) && '' != trim($options['autorewind']))
    {
      $options['autorewind'] = $options['autorewind'] ? 'on' : 'off';
    }

    if (isset($options['loop']) && '' != trim($options['loop']))
    {
      $options['loop'] = $options['loop'] ? 'on' : 'off';
    }

    if (isset($options['mute']) && '' != trim($options['mute']))
    {
      $options['mute'] = $options['mute'] ? 'on' : 'off';
    }

    if (isset($options['muteonly']) && '' != trim($options['muteonly']))
    {
      $options['muteonly'] = $options['muteonly'] ? 'on' : 'off';
    }
  }

  function _osflv_parse_options($options)
  {
    $parsed = array();
    foreach ($options as $key => $value)
    {
      $parsed[] = "$key=$value";
    }

    return implode('&', $parsed);
  }

  function _osflv_generate_tag($source, $width, $height, $options_string)
  {
    return sprintf('<object width="%s" height="%s" id="flvPlayer">
  <param name="allowFullScreen" value="true" />
  <param name="movie" value="%s?movie=%s&%s" />
  <embed src="%s?movie=%s&%s" width="%s" height="%s" allowFullScreen="true" type="application/x-shockwave-flash" />
</object>',
        $width,
        $height,
        image_path('/ncOSFLVPlayerPlugin/flash/player.swf', true),
        $source,
        $options_string,
        image_path('/ncOSFLVPlayerPlugin/flash/player.swf', true),
        $source,
        $options_string,
        $width,
        $height
      );
  }

/*
fgcolor
bgcolor
autoplay
autoload
autorewind
volume
loop
mute
muteonly
clicktarget
clickurl
*/
  function osflv_player($source, $width = 400, $height = 400, $options = array())
  {
    $height += 40;
    _osflv_clean_options($options);

    return _osflv_generate_tag($source, $width, $height, _osflv_parse_options($options));
  }
