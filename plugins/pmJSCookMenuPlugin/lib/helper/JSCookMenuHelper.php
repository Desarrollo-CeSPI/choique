<?php

use_helper('Javascript');

function _load_jscookmenu_resources($theme)
{
  sfContext::getInstance()->getResponse()->addJavascript('/pmJSCookMenuPlugin/js/JSCookMenu');
  switch ($theme) {
    case 'cmThemeGray':
      sfContext::getInstance()->getResponse()->addJavascript('/pmJSCookMenuPlugin/js/ThemeGray/theme');
      sfContext::getInstance()->getResponse()->addStylesheet('/pmJSCookMenuPlugin/css/ThemeGray/theme');
    break;
    case 'cmThemeIE':
      sfContext::getInstance()->getResponse()->addJavascript('/pmJSCookMenuPlugin/js/ThemeIE/theme');
      sfContext::getInstance()->getResponse()->addStylesheet('/pmJSCookMenuPlugin/css/ThemeIE/theme');
    break;
    case 'cmThemeMiniBlack':
      sfContext::getInstance()->getResponse()->addJavascript('/pmJSCookMenuPlugin/js/ThemeMiniBlack/theme');
      sfContext::getInstance()->getResponse()->addStylesheet('/pmJSCookMenuPlugin/css/ThemeMiniBlack/theme');
    break;
    case 'cmThemeOffice':
      sfContext::getInstance()->getResponse()->addJavascript('/pmJSCookMenuPlugin/js/ThemeOffice/theme');
      sfContext::getInstance()->getResponse()->addStylesheet('/pmJSCookMenuPlugin/css/ThemeOffice/theme');
    break;
    case 'cmThemeOffice2003':
      sfContext::getInstance()->getResponse()->addJavascript('/pmJSCookMenuPlugin/js/ThemeOffice2003/theme');
      sfContext::getInstance()->getResponse()->addStylesheet('/pmJSCookMenuPlugin/css/ThemeOffice2003/theme');
    break;
    case 'cmThemePanel':
      sfContext::getInstance()->getResponse()->addJavascript('/pmJSCookMenuPlugin/js/ThemePanel/theme');
      sfContext::getInstance()->getResponse()->addStylesheet('/pmJSCookMenuPlugin/css/ThemePanel/theme');
    break;
  }
}

function _get_item($array)
{
  use_helper('I18N');

  $item = (isset($array['icon'])?"'".image_tag($array['icon'])."'":'null').", ".
          (isset($array['title'])?"'".__($array['title'])."'":'null').", ".
          (isset($array['url'])?"'".url_for($array['url'])."'":'null').", ".
          (isset($array['target'])?"'".$array['target']."'":"'_self'").", ".
          (isset($array['description'])?"'".__($array['description'])."'":'null');
  return $item;
}

function _jscookmenu($arr, $name, $orientation, $theme)
{
  _load_jscookmenu_resources($theme);

  $sf_user = sfContext::getInstance()->getUser();

  $js = "var $name = [";
  foreach ($arr as $item) {
    if (is_array($item)) {
      // check credentials
      $display = true;
      if (isset($item['credentials']))
        $display = $sf_user->hasCredential($item['credentials']);

      // if sf_user has credentials, display the menu item
      if ($display) {
        $js .= "["._get_item($item);
        if (isset($item['submenu'])) {
          foreach ($item['submenu'] as $subitem) {
            if (is_array($subitem)) {
              // check credentials
              $display = true;
              if (isset($subitem['credentials']))
                $display = $sf_user->hasCredential($subitem['credentials']);

              // if sf_user has credentials, display the menu item
              if ($display) {
                $js .= ", ["._get_item($subitem);
                if (isset($subitem['submenu'])) {
                  foreach ($subitem['submenu'] as $subsubitem) {
                    if (is_array($subsubitem)) {
                      // check credentials
                      $display = true;
                      if (isset($subsubitem['credentials']))
                        $display = $sf_user->hasCredential($subsubitem['credentials']);

                      // if sf_user has credentials, display the menu item
                       if ($display) {
                         $js .= ", ["._get_item($subsubitem)."]";
                       }
                    } else if ($subsubitem == '_cmSplit') {
                      $js .= ", ".$subsubitem;
                    }
                  }
                }
                $js .= "]";
              }
            } else if ($subitem == '_cmSplit') {
              $js .= ', '.$subitem;
            }
          }
        }
        $js .= "], ";
      }
    } else if ($item == '_cmSplit') {
      $js .= $item.', ';
    }
  }
  $js = substr($js, 0, strlen($js) - 2);
  $js .= "];";

  $html = tag('div', array('id' => "$name"), true);
  $html .= tag('/div', array(), true);

  $js2 = "cmDraw('$name', $name, '$orientation', $theme);";

  return javascript_tag($js).$html.javascript_tag($js2);
}

function jscookmenu_from_array($arr, $name, $orientation, $name)
{
  return _jscookmenu($arr, $name, $orientation, $theme);
}

function jscookmenu_from_yml($yml_file, $name, $orientation, $theme)
{
  return _jscookmenu(sfYaml::load($yml_file), $name, $orientation, $theme);
}
