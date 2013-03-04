<?php 
/*
 * Choique CMS - A Content Management System.
 * Copyright (C) 2012 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 * 
 * This file is part of Choique CMS.
 * 
 * Choique CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 * 
 * Choique CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Choique CMS.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php
	use_helper('Javascript');

  /**
    *	CmsMenuHelper.php
    *
    *	Generate menues from YAML
    */
    
  /**
   *  Insert a menu which represents the structure defined in the YAML
   *  '$yaml' (could either be a YAML file or a string with YAML syntax).
   *  The structure of $yaml should look like this example:
   *
   *  <code>
   *  file:
   *    name:           File
   *    credentials:    [admin]
   *    children:
   *      new:
   *        name:       New...
   *        module:     file
   *        action:     new
   *      open:
   *        name:       Open
   *        module:     file
   *        children:
   *          recent:
   *            name:   Recent file
   *            action: openRecent
   *          more:
   *            name:   More...
   *            action: open
   *  actions:
   *    name:           Actions
   *    children:
   *      exit:
   *        name:       Exit
   *        module:     actions
   *        action:     exit
   *  </code>
   *
   *  Which outputs a menu looking like this:
   *
   *  <pre>
   *  File
   *    New...
   *    Open
   *      Recent file
   *      More...
   *  Actions
   *    Exit
   *  </pre>
   *
   *  @return string
   *  @param string $yaml Path to 
   */
  function insert_menu($yaml)
  {
    $paramsArray = sfYaml::load($yaml);

    if (empty($paramsArray))
    {
      return '';
    }

    sfLoader::loadHelpers(array('I18N', 'Url', 'Tag', 'Javascript'));

    $levels = array('', '');
    $js = '';
    
    $i = 0;
    foreach ($paramsArray as $firstLevel)
    {
      $credentials = (array_key_exists('credentials', $firstLevel)) ? $firstLevel['credentials'] : array();

      //only show menu item if the user has the required credentials
      if (sfContext::getInstance()->getUser()->hasCredential($credentials))
      {
        //if the first level item has children, make it clickeable to show/hide them
        if (array_key_exists('children', $firstLevel))
        {
          $levels[0] .= sprintf("<div class=\"first-level\">%s</div>",
                                link_to_function(__($firstLevel['name']),
                                                 "toggleSelected('submenu_$i', this);" . visual_effect('toggle_blind', 'submenu_' . $i, array('duration' => 0.25))));

          $js .= visual_effect('toggle_blind', 'submenu_' . $i, array('duration' => 0));

          //done with the first level item. Let's move on to its children
          $levels[1] .= sprintf("<div id=\"submenu_%s\" class=\"second-level\">", $i);
          foreach ($firstLevel['children'] as $secondLevel)
          {
            //if the second level item has children, it's a separator. we'll ignore his module/action
            if (array_key_exists('children', $secondLevel)) {
              $levels[1] .= sprintf("<span class=\"second-level separator\">%s</span>", __($secondLevel['name']));

              //done with the second level item. We'll continue with its children
              foreach ($secondLevel['children'] as $thirdLevel)
              {
                //Let's extract the module/action data from the third level item and/or its ancestors
                $module = (array_key_exists('module', $firstLevel))  ? $firstLevel['module']  : '';
                $module = (array_key_exists('module', $secondLevel)) ? $secondLevel['module'] : $module;
                $module = (array_key_exists('module', $thirdLevel))  ? $thirdLevel['module']  : $module;
                $action = (array_key_exists('action', $firstLevel))  ? $firstLevel['action']  : 'index';
                $action = (array_key_exists('action', $secondLevel)) ? $secondLevel['action'] : $action;
                $action = (array_key_exists('action', $thirdLevel))  ? $thirdLevel['action']  : $action;

                //if it has a module/action set, it's a link
                if (!empty($module))
                {
                  $levels[1] .= sprintf(" <span class=\"third-level\">%s</span>  ", link_to(__($thirdLevel['name']), $module . '/' . $action));
                }
                else
                {
                  //otherwise, it is a plain and simple text
                  $levels[1] .= sprintf(" <span class=\"third-level\">%s</span>  ", __($thirdLevel['name']));
                }
              }
            }
            else
            {
              //the second level item doesn't have any children. Let's try to make him a link to a module/action
              $module = (array_key_exists('module', $firstLevel))  ? $firstLevel['module']  : '';
              $module = (array_key_exists('module', $secondLevel)) ? $secondLevel['module'] : $module;
              $action = (array_key_exists('action', $firstLevel))  ? $firstLevel['action']  : 'index';
              $action = (array_key_exists('action', $secondLevel)) ? $secondLevel['action'] : $action;

              if (!empty($module))
              {
                $levels[1] .= sprintf("<span class=\"second-level\">%s</span>", link_to(__($secondLevel['name']), $module . '/' . $action));
              }
              else
              {
                //the second level item doesn't have a module either. We'll output its name.
                $levels[1] .= sprintf("<span class=\"second-level\">%s</span>", __($firstLevel['name']));
              }
            }
          }
          $levels[1] .= "</div>";
        }
        else
        {
          //if the first level item doesn't have any children, try to make it clickeable in order to go to a module/action
          if (array_key_exists('module', $firstLevel))
          {
            $action = (array_key_exists('action', $firstLevel)) ? $firstLevel['action'] : 'index';
            $levels[0] .= sprintf("<div class=\"first-level\">%s</div>", link_to(__($firstLevel['name']), $firstLevel['module'] . '/' . $action));
          }
          else
          {
            //the first level item doesn't even have a module set. Just spit it out.
            $levels[0] .= sprintf("<div class=\"first-level\">%s</div>", __($firstLevel['name']));
          }
        }
      }
      $i++;
    }

    $html = '<div id="cms_menu">';
    foreach ($levels as $level)
    {
      $html .= $level . "\n\n";
    }
    $html .= '</div>';

    $html .= javascript_tag("
      var currentSubMenuId = '';

      function toggleSelected(subMenuId, elmnt)
      {
        if ($(currentSubMenuId))
        {
          $(currentSubMenuId).removeClassName('selected');
          new Effect.toggle(currentSubMenuId, 'blind', { duration: 0.25 });
        }

        if (currentSubMenuId != subMenuId)
        {
          $(elmnt).up().addClassName('selected');
          currentSubMenuId = subMenuId;
        }
        else
        {
          currentSubMenuId = '';
        }
      }
      $js;");

    return $html;
  }