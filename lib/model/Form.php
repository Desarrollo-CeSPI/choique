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

/**
 * Subclass for representing a row from the 'form' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Form extends BaseForm implements SlotletInterface
{
  public function setTitle($v)
  {
    parent::setTitle($v);
    $this->setName(strtr($v, array('á' => 'a',
                                   'é' => 'e',
                                   'í' => 'i',
                                   'ó' => 'o',
                                   'ú' => 'u',
                                   'à' => 'a',
                                   'è' => 'e',
                                   'ì' => 'i',
                                   'ò' => 'o',
                                   'ù' => 'u',
                                   'ä' => 'a',
                                   'ë' => 'e',
                                   'ï' => 'i',
                                   'ö' => 'o',
                                   'ü' => 'u',
                                   ' ' => '_')));
  }

  /**
   *    Return a string holding an HTML representation of this
   *    Form 
   *
   *    @return string
   */
  public static function getNullHTMLRepresentation($description)
  {
    sfLoader::loadHelpers(array('I18N'));

    return sprintf('<span class="not-found">%s</span>', empty($description) ? __('Referencia a formulario inválida') : $description);
  }

  public function getHTMLRepresentation()
  {
    sfLoader::loadHelpers(array('Form', 'I18N', 'Javascript'));

    $str = tag('div', array('class' => 'form', 'id' => 'form_'.$this->getId()), true);

    $str .= content_tag('div', content_tag('h1', $this->getDescription(), array('class' => 'form-description-title')), array('class' => 'form-description'));

    $str .= form_remote_tag(array('url' => 'form/submit',
                                  'update' => 'form_'.$this->getId(),
                                  'script' => true));


    $str .= input_hidden_tag('form_id', $this->getId());

    foreach ($this->getFieldsBySort() as $field)
    {
      $str .= content_tag('div', $field->getHTMLRepresentation(), array('class' => 'form-row'));
    }

    $str .= tag('div', array('class' => 'form-actions'), true);

    if ( !sfConfig::get('choique_is_backend', false))
    {
      if ($this->getIsPoll())
      {
        $str .= submit_tag(__('votar'), array('class' => 'submit'));
      }
      else
      {
        $str .= reset_tag(__('limpiar'), array('class' => 'reset'));
        $str .= submit_tag(__('guardar'), array('class' => 'submit'));
      }
    }

    $str .= tag('/div', array(), true);
    $str .= tag('/form', array(), true);
    $str .= tag('/div', array(), true);

    return $str;
  }

  public static function getLastActivePoll()
  {
    $c = new Criteria();
    $c->add(FormPeer::IS_POLL, true);
    $c->add(FormPeer::IS_ACTIVE, true);
    $c->addDescendingOrderByColumn(FormPeer::UPDATED_AT);

    return FormPeer::doSelectOne($c);
  }

  /*Interface implementation*/
  public static function getSlotletMethods()
	{
	  return array('getSlotlet');
	}

  public static function getSlotletName() 
  {
    sfLoader::loadHelpers(array('I18N'));
    
    return __("Formulario");
  }
    
  /**
   *  Returns a piece of HTML code holding the representation
   *  of the Slotlet for Form class. 
   *  The resulting HTML code should look like this:
   *  If the there's a cookie:
   *    <code>
   *        <div id="poll" class="sl_shortcut">
   *            <div class="title">__("Encuesta")</div>
   *            <div class="rectangle"> 
   *                <div class="form-description">
   *                    <h1>FORM DESCRIPTION</h1>
   *                </div>
   *            </div>
   *            <div id="results">
   *                FORM RESULTS
   *            </div>
   *        </div>
   *    </code>
   * 
   *  If there isn't a cookie:
   *    <code>
   *        <div id="poll" class="sl_shortcut">
   *            <div class="title">__("Encuesta")</div>
   *            FORM REPRESENTATION 
   *        </div>
   *    </code> 
   * 
   *  @param $options Array The options passed to the Slotlet.
   *  
   *  @return string The HTML code of the Slotlet.
   */
  public static function getSlotlet($options)
  {
    sfContext::getInstance()->getResponse()->addStylesheet('frontend/slotlet/sl_form');
    sfLoader::loadHelpers(array('I18N'));

    $str = '';

    $form = self::getLastActivePoll();
    if ($form)
    {
      $str .= "<div id=\"poll\">";
      $str .= "<div class=\"title\">".__('Encuesta')."</div>";  
      if (!sfContext::getInstance()->getRequest()->getCookie($form->getName()))
      {
        $str .= $form->getHTMLRepresentation();
      }
      else
      {
      	$str .= "<div class=\"rectangle\">";
        $str .= "<div class=\"form-description\">";
        $str .= "<h1 class=\"form-description-title\">".$form->getDescription()."</h1>";
        $str .= "</div>";
        $str .= "<div id=\"results\">";
        if ($rows = $form->getRows())
        {
          foreach ($form->getFieldsBySort() as $field)
          {
            $td_content = ($field->getLabel())?$field->getLabel():__('Etiqueta');
            $str .= "<div class='results-row'>";
            $str .= "<div class='results-first-td'>$td_content</div>";
            $str .= "<div class='results-second-td'>".sprintf("%.2f%s", $field->getPercentage(), '%')."</div>";
            $str .= "</div>";
          }
        }
        $str .= "</div></div>";
      }
      $str .= "<div class=\"footer\"></div></div>";
    }

    return $str;
  }
  /*End Interface implementation*/

  public function getFieldsBySort()
  {
    $c = new Criteria();
    $c->addAscendingOrderByColumn(FieldPeer::SORT);
    
    return $this->getFields($c);
  }

  public function canEdit()
  {
    return ($context= sfContext::getInstance()) &&
          ((
          $context->getUser()->isSuperAdmin()
          ||
          $context->getUser()->hasCredential(array('designer', 'reporter_admin','reporter'),false) &&
          ($this->getCreatedByAsGuardUser()) && ($this->getCreatedByAsGuardUser()->getUsername() == $context->getUser()->getUsername())
          ) ||
          $context->getUser()->hasCredential('designer_admin')
          );
  }

  public function getCreatedByAsGuardUser()
  {
    return sfGuardUserPeer::retrieveByPK($this->getCreatedBy());
  }
  
  public function getCreatedByUser()
  {
    $author = $this->getCreatedByAsGuardUser();

    return ($author) ? $author->getName() : '';
  }


  public function getUpdatedByUser()
  {
    $author = $this->getsfGuardUserRelatedByUpdatedBy();

    return ($author) ? $author->getName() : '';
  }

  public function canDelete()
  {
    if (!$this->canEdit()) return false;
    $criteria = new Criteria();
    $criteria->add(ArticleFormPeer::FORM_ID,$this->getId());
    return ArticleFormPeer::doCount($criteria) == 0;
  }

  public function delete ($con = null)
  {
    if ($this->canDelete())
    {
      return parent::delete($con);
    }
    else
    {
      return false;
    }
  }

  public function getAllArticles()
  {
    $articles = array();
    $article_forms = $this->getArticleFormsJoinArticle();
    foreach ($article_forms as $af)
    {
      $articles[] = $af->getArticle();
    }

    return $articles;
  }

  public function getNameForHTML()
  {
    return sha1($this->getName());
  }
}