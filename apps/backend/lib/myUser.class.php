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

class myUser extends sfGuardSecurityUser
{
  public function __toString()
  {
    return $this->getUsername();
  }
  

  public function checkSectionCredentialsFor($section=null)
  {
    //Si el usuario no tiene una seccion asociada...
    if ($this->getGuardUser()->getSection() === null) return true;

    if ( is_null($section) ) return true;

    if ( $this->hasCredential('designer_admin') || $this->hasCredential('designer') ) return true;

    return in_array($this->getGuardUser()->getSection()->getName(), array_map(create_function('$section', 'return $section->getName();'), $section->getAncestors(true)));
  }

  /* Este modulo agrega credenciales por seccion en caso de ser necesario.. */
  public function requiredCredentialsFor(sfAction $action, $credentials)
  {

    if ( $this->hasCredential('designer_admin') || $this->hasCredential('designer') ) return $credentials;

    if ( $this->getGuardUser() && $this->getGuardUser()->getSection() === null ) return $credentials;

    /* En este caso tenemos que analizar si al recurso al que accedemos pertenece a una seccion de las que podemos acceder.
       Si así no fuera, agregamos una credencial que no tenemos y que no nos permite acceder 
       
       Si el modulo al que se quiere acceder es list entonces se procede con las credenciales normales
       Solo nos importa asegurar el acceso a UN elemento no a muchos
    */
    if ( in_array($action->getActionName(), array('index','list', 'create','autocomplete','autocompleteArticle', 'addArticleSection', 'autocompleteMultimedia'))) return $credentials;


    $section = null;

    if ( $action instanceOf sectionActions)
    { // Caso del modulo SECTION
      // Tratamos de obtener una seccion desde el request
      $section_id = $action->getRequestParameter('id');
      if ( isset($section_id) ) $section=SectionPeer::retrieveByPk($section_id);
    }
    elseif ( $action instanceOf articleActions)
    { //Caso del modulo ACTION
      if ( $action->getActionName() == 'show')  return $credentials; //El show lo permitimos sin importar 
      // Tratamos de obtener una seccion desde el request
      $article_id = $action->getRequestParameter('id');
      if (isset($article_id) && ($article = ArticlePeer::retrieveByPk($article_id)) && !is_null($article))
        $section = $article->getSection();
    }

    if (!is_null($section))
    { // Si tenemos una seccion asociada al objeto que se esta trabajando... tratamos de ver si tenemos acceso
      if ( $this->getGuardUser() && $this->getGuardUser()->getSection() && in_array($this->getGuardUser()->getSection()->getName(), array_map(create_function('$section', 'return $section->getName();'), $section->getAncestors(true))) )
        return $credentials;
    }
    return '___restricted_section___';
  }
  

  /* Modulo anterior en section:
  public function getCredential()
  {
    $ret = parent::getCredential();
    switch ($this->getActionName())
    {
      case 'list':
        return $ret;
    }

    $section_id = $this->getRequestParameter('id');
    if (isset($section_id)&&($section=SectionPeer::retrieveByPk($section_id))&&!is_null($section))
    {
      $credential=$section->getCredentials();
      if (!empty($credential))
      {
        if (is_null($ret))
        {
          return $credential;
        }
        elseif (is_array($credential))
        {
          $ret=array_merge($ret,$credential);
        }
        else
        {
          array_unshift($ret,$credential);
        }
      }
    }

    return $ret;
  }

  Modulo anterior en acrticle:
  public function getCredential()
  {
    $ret = parent::getCredential();
    switch ($this->getActionName())
    {
      case 'show':
      case 'list':
        return $ret;
    }

    $article_id = $this->getRequestParameter('id');
    if (isset($article_id) && ($article = ArticlePeer::retrieveByPk($article_id)) && !is_null($article))
    {
      $section = $article->getSection();
      if (is_null($section))
      {
        return $ret;
      }

      $credential = $section->getCredentials();
      if (!empty($credential))
      {
        if (is_null($ret))
        {
          return $credential;
        }
        elseif (is_array($credential))
        {
          $ret = array_merge($ret, $credential);
        }
        else
        {
          array_unshift($ret, $credential);
        }
      }
    }

    return $ret;
  }

  */

}