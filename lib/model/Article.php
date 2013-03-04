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
 * Subclass for representing a row from the 'article' table.
 *
 *
 *
 * @package lib.model
 */
class Article extends BaseArticle implements SlotletInterface
{
  const ARTICLE                   = 0;  // articulo
  const NEWS                      = 1;  // novedad
  const INSTITUTIONAL             = 2;  // noticia

  const REFERENCE_TYPE_NONE       = 0;
  const NONE_STRING               = "Ninguno";
  const REFERENCE_TYPE_EXTERNAL   = 1;
  const EXTERNAL_STRING           = "Externo";
  const REFERENCE_TYPE_SECTION    = 2;
  const SECTION_STRING            = "Sección";
  const REFERENCE_TYPE_ARTICLE    = 3;
  const ARTICLE_STRING            = "Artículo";

  /**
   *    Return a string holding an HTML representation of this
   *    Article
   *
   *    @return string
   */
  public static function getNullHTMLRepresentation($description)
  {
    sfLoader::loadHelpers(array('I18N'));

    return sprintf('<span class="not-found">%s</span>', empty($description) ? __('Referencia a artículo inválida') : $description);
  }

  public function getArticleSections($criteria = null, $con = null)
  {
    $this->getSection()->addDefaultArticleSections();
    return parent::getArticleSections($criteria, $con);
  }

  public function setName($name)
  {
    sfLoader::loadHelpers(array('CmsEscaping'));

    $aux  = ($this->getTitle()) ? $this->getTitle() : "Art" . ' ' . date('Y_m_d');
    $name = ($name) ? escape_string($name) : escape_string($aux);

    return parent::setName($name);
  }

  public function hasMultimedia()
  {
    return (!is_null($this->getMultimediaId()));
  }

  /**
   *  Return a string with a reference to this article
   *   in the following manner:
   *    [[articulo:YYYY/MM/DD/name_of_the_article|innerElement]]
   *  which is more suitable for human reference.
   *
   *  @return string
   */
  public function getNamedReferenceTag($innerElement = null)
  {
    $str  = "[[articulo:";
    $str .= $this->getCreatedAt('Y/m/d');
    $str .= "/" . $this->getName();
    if ($innerElement)
      $str .= "|" . $innerElement;
    $str .= "]]";

    return $str;
  }

  /**
   *  Return a string with the url to which this article
   *  should link on any anchor.
   *
   *  @return string
   */
  public function getURLReference()
  {
    switch ($this->getReferenceType()) {
      case self::REFERENCE_TYPE_NONE:
        return sprintf('@article_shortcut?year=%d&month=%d&day=%d&name=%s',
                       $this->getCreatedAt('Y'),
                       $this->getCreatedAt('m'),
                       $this->getCreatedAt('d'),
                       $this->getName());
        break;
      case self::REFERENCE_TYPE_EXTERNAL:
        return $this->getReference();
        break;
      case self::REFERENCE_TYPE_SECTION:
        $section = SectionPeer::retrieveByPK($this->getReference());
        if (!$section) return "@homepage";
        return sprintf('@template_by_name?name=%s', $section->getName());
        break;
      case self::REFERENCE_TYPE_ARTICLE:
        $article = ArticlePeer::retrieveByPK($this->getReference());
        if (!$article) $article = $this;
        return sprintf('@article_shortcut?year=%d&month=%d&day=%d&name=%s',
                       $article->getCreatedAt('Y'),
                       $article->getCreatedAt('m'),
                       $article->getCreatedAt('d'),
                       $article->getName());
        break;
      default:
        return "@homepage";
        break;
    }
  }

  public function getLinkedTitle()
  {
    sfLoader::loadHelpers(array('Url','Tag'));

    $options = ($this->getReferenceType() == self::REFERENCE_TYPE_EXTERNAL) ? 'target=_blank' : null;

    return link_to($this->getTitle(), $this->getURLReference(),$options);
  }

  public function getHTMLReference($innerElement = null, $html_options = array())
  {
    sfLoader::loadHelpers(array('Url','Tag'));

    $innerElement=trim($innerElement);
    if (!$innerElement || empty($innerElement))
    {
      $innerElement = $this->getTitle();
    }

    return link_to($innerElement, $this->getURLReference(), array_merge(array('title' => $this->getTitle()), $html_options));
  }

  /**
   *  Return a snippet which is the action toolbar for an Article
   *
   *  @return string
   */
  public function getActionToolbar($with_navigation = false)
  {
    sfLoader::loadHelpers(array('UJS', 'Asset', 'Url', 'Tag', 'I18N', 'Lightview', 'Javascript', 'CmsCSRFToken'));

    $separator = '<span class="separator">|</span>';

    if (CmsConfiguration::get('check_use_social_share_toolbar', false))
    {
      try
      {
        $social_tool = SocialTools::get(CmsConfiguration::get('custom_social_sharing_tool'));

        $social_tool->register(sfContext::getInstance()->getResponse());
        
        $social = $social_tool->render().$separator;
      }
      catch (RuntimeException $e)
      {
        $social = '';
      }
    }
    else
    {
      $social = '';
    }

    $toolbar = <<<TOOLBAR
<div id="break-line">
  <div id="top-article-actions" class="article-actions">
    %social%
    <div class="cq-article-actions">
      %nav_back%
      %nav_forward%
      %print%
      %send_by_email%
      %enlarge_text%
      %shrink_text%
      <noscript>%noscript%</noscript>
    </div>
  </div>
</div>
TOOLBAR;
    
    return strtr($toolbar, array(
      '%nav_back%'      => $with_navigation ? UJS_link_to_function(image_tag(choiqueFlavors::getImagePath('navigation_back', 'gif'), array('alt' => __('Volver'), 'title' => __('Volver'))), 'history.back()').UJS_write(" ".$separator) : '',
      '%nav_forward%'   => $with_navigation ? UJS_link_to_function(image_tag(choiqueFlavors::getImagePath('navigation_forward', 'gif'), array('alt' => __('Adelante'), 'title' => __('Adelante'))), 'history.go(1)').UJS_write(" ".$separator) : '',
      '%print%'         => UJS_link_to_function(image_tag(choiqueFlavors::getImagePath('print', 'gif'), array('alt' => __('Imprimir'), 'title' => __('Imprimir'))), 'popup_window("'.url_for('article/printPreview?id='.$this->getId()).'","print_window")').UJS_write(" ".$separator),
      '%send_by_email%' => UJS_lightview_ajax(url_for('article/sendByEmail?id='.$this->getId().'&_csrf_token='.csrf_token()), image_tag(choiqueFlavors::getImagePath('article_contact', 'gif'), array('alt' => __('Enviar por mail'), 'title' => __('Enviar por mail'))), __('Envio de email'), __('Enviar por email'), array('fullscreen' => 'false', 'height' => 150, 'width'=>200)).UJS_write(" ".$separator),
      '%enlarge_text%'  => UJS_link_to_function(image_tag(choiqueFlavors::getImagePath('zoom_plus', 'gif'), array('alt' => __('aumentar'), 'title' => __('aumentar'))),'enlargeText("full-html", 5)').UJS_write(" ".$separator),
      '%shrink_text%'   => UJS_link_to_function(image_tag(choiqueFlavors::getImagePath('zoom_minus', 'gif'), array('alt' => __('disminuir'), 'title' => __('disminuir'))),'enlargeText("full-html", -5)'),
      '%social%'        => $social,
      '%noscript%'      => link_to(image_tag(choiqueFlavors::getImagePath('print', 'gif'), array('alt' => __('Imprimir'), 'title' => __('Imprimir'))), 'article/printPreview?id='.$this->getId())
    ));
  }

  /**
   *  Return a snippet which is the representation
   *  of the article's related Multimedia (MainImage) in either
   *  a large (zoomable) representation or a small one, according
   *  to what has been specified by the author of the article.
   *
   *  @return string The representation of the MainImage.
   */
  public function getRepresentedMultimedia()
  {
    $size = ($this->getZoomableMultimedia() ? 'l' : 's');

    return (($this->hasMultimedia()) ? $this->getMultimedia()->getHTMLRepresentation($size) : "&nbsp;");
  }

  /**
   *  Return a snippet which is the public
   *  representation of the article, ready to be
   *  displayed in a page for a reader to read the article.
   *  That is, with all of the tags of inner use replaced
   *  with browser-inteligible HTML tags.
   *
   *  @return string
   */
  public function getFullHTMLRepresentation()
  {
    sfLoader::loadHelpers(array('Asset', 'Url', 'Tag', 'I18N', 'Lightview', 'Javascript'));

    $view = sfYaml::load(DIRNAME(__FILE__) . '/../../apps/frontend/config/view.yml');
    if (isset($view['default']['article_columns']))
    {
      $nbcolumns = $view['default']['article_columns'];
    }
    else
    {
      $nbcolumns = 1;
    }

    switch ($nbcolumns) {
      case 1:
        $str  = "<div id=\"full-html\">";
        $str .= (!is_null($this->getUpperDescription())) ? "<div class=\"upper-description\">" . $this->getUpperDescription() . "</div>" : '';
        $str .= "<h1 class=\"title\">" . $this->getTitle() . "</h1>";
        $str .= "<div class=\"heading\">" . $this->getHeading() . "</div>";
        $str .= "<div class=\"body\">";
        $str .= (($this->hasMultimedia()) ? "<div id=\"article-image\">" . $this->getRepresentedMultimedia() . "</div>" : '');
        $str .= $this->getHTMLText() . "</div>";
        $str .= "</div>";
        if ($this->shouldIncludeLastUpdateDate())
        {
          $str .= '<div class="updated-at">' . $this->getPrintUpdatedAt() . '</div>';
        }
        break;
      case 2:
        $str  = '<div id="full-html">';
        $str .= '<div id="images_left">' . $this->getRepresentedMultimedia() . '</div>';
        $str .= '<div id="text_right">';
        $str .= '<h1 class="title">' . $this->getTitle() . '</h1>';
        $str .= '<div class="heading">' . $this->getHeading() . '</div>';
        $str .= '<div class="body">' . $this->getHTMLText() . '</div>';
        $str .= '</div></div>';
        if ($this->shouldIncludeLastUpdateDate())
        {
          $str .= '<div class="updated-at">' . $this->getPrintUpdatedAt() . '</div>';
        }
        break;
    }

    return $str;
  }

  public function getPrintUpdatedAt()
  {
    $format = 'd/m/Y';

    if ($this->shouldIncludeLastUpdateTime())
    {
      $format .= ' H:i';
    }

    return __(CmsConfiguration::get('article_updated_at_caption', 'Actualizado el %f%'), array('%f%' => $this->getUpdatedAt($format)));
  }

  public function shouldIncludeLastUpdateDate()
  {
    return $this->shouldIncludeLastUpdatePart('date');
  }

  public function shouldIncludeLastUpdateTime()
  {
    return $this->shouldIncludeLastUpdatePart('time');
  }

  public function shouldIncludeLastUpdatePart($part)
  {
    $types = array(
      self::ARTICLE       => 'article',
      self::NEWS          => 'news',
      self::INSTITUTIONAL => 'institutional'
    );

    if (!array_key_exists($this->getType(), $types))
    {
      return true;
    }

    return CmsConfiguration::get('check_show_last_'.$types[$this->getType()].'_update_'.$part, true);
  }

  public function getSendByEmailForm()
  {
    sfLoader::loadHelpers(array('Asset', 'Validation', 'Form', 'Tag', 'I18N', 'Javascript'));

    $html = '<br/><br/><div id="send_by_email_form">
              <div id="mail-body-acc">';
    $html .= '<h1 class="title">';
    $html .= __("Enviar el artículo por Email");
    $html .= '</h1>';
    $html .= form_tag('article/sendByEmailUJS?id='.$this->getId(), array('method'    => 'get'));
    $html .= '<fieldset id="fieldset-mail" class="">
              <div class="form-row-email">';
    $html .= label_for('mailto', __('Enviar a:'), 'class="required" ');
    $html .= '<div class="content';
    if (sfContext::getInstance()->getRequest()->hasError('mailto'))
    {
      $html .= ' form-error';
    }
    $html .= '">';

    if (sfContext::getInstance()->getRequest()->hasError('mailto'))
    {
      $html .= form_error('mailto', array('class' => 'form-error-msg'));
    }
    $html .= input_tag('mailto',__("Email de destino"));
    $html .= '</div>
              </div>
              <div class="form-row-email">';
    $html .= label_for('from', __('Remitente:'), 'class="required" ');
    $html .= '<div class="content';
    if (sfContext::getInstance()->getRequest()->hasError('from')){
      $html .= ' form-error';
    }
    $html .= '">';
    if (sfContext::getInstance()->getRequest()->hasError('from')){
      $html .= form_error('from', array('class' => 'form-error-msg'));
    }
    $html .= input_tag('from',__("Remitente del email"));
    $html .= '</div>
    </div>
  </fieldset>
  <div id="form-actions-email">';
    $html .= submit_tag('Enviar');
    $html .= '</div>
  </form>
  </div>
  </div>';

    return $html;
  }

  public function getFooter()
  {
    sfLoader::loadHelpers(array('CmsCSRFToken'));
    
    $html = "&nbsp;";
    $remote_options = array('update' => 'contact_form',
                            'url'    => '/contact/contactForm?article_id=' . $this->getId().'&_csrf_token='.csrf_token());
    if (CmsConfiguration::get('check_show_article_mail', true))
    {
      sfLoader::loadHelpers(array('I18N', 'Javascript', 'Asset', 'Tag', 'UJS'));
      $mail = $this->getObfuscatedContact();
      if (!empty($mail))
      {
        $mail_text = CmsConfiguration::get('text_contact_mail');

        if (!empty($mail_text))
        {
          $content = $mail_text . ' ' . image_tag(choiqueFlavors::getImagePath('send_by_mail', 'gif'), array('alt' => __('E-mail'), 'title' => __('E-mail')));
          $html = sprintf("<div id=\"contact_form\"><span class=\"link_contact_form\">%s </span><noscript>%s</noscript></div><noscript>%s</noscript>",
                          UJS_link_to_remote($content, $remote_options), link_to(self::unobfuscate($mail), $remote_options['url']), $this->getSendByEmailForm());
        }
        else
        {
          $html = sprintf("<div id=\"contact_form\"><span class=\"link_contact_form\">%s<span class=\"%s\">%s</span></span><noscript>%s</noscript></div><noscript>%s</noscript>",
                          __("Email de contacto: "),
                      CmsConfiguration::get('check_obfuscate_mail_addresses', true) ? 'obfuscated' : '',
                        UJS_link_to_remote($mail, $remote_options), link_to(self::unobfuscate($mail), $remote_options['url'], array('title' => __("Email de contacto"))), $this->getSendByEmailForm());
        }
      }
    }

    return $html;
  }

  /**
   *  Return a snippet which is a short
   *  representation of this article, suitable for
   *  displaying it as a list item.
   *
   *  @return string
   */
  public function getListItemRepresentation($width_percentage = 100)
  {
    return '<h1 class="front-title-only" style="width:'.$width_percentage.'%;">' . $this->getLinkedTitle() . '</h1>';
  }

  /**
   *  Return a snippet which is the photo
   *  inscription ("foto epigrafe") representation of
   *  this article.
   *
   *  @return string
   */
  public function getPhotoInscriptionRepresentation($orientation = 'left', $width_percentage = 100)
  {
    $html  = "<div class=\"photo-inscription\" style=\"width:$width_percentage%;\">";
    $html .= "<div class=\"photo-inscription-content\">";
    $html .= "<div class=\"photo ".(($orientation == 'left')?"img-left":"img-right")."\">" . (($this->hasMultimedia()) ? $this->getMultimedia()->getHTMLRepresentation("m") : "&nbsp;") . "</div>";
    $html .= "<div class=\"content\">";
    $html .= "<h1 class=\"front-title\">" . $this->getLinkedTitle() . "</h1>";
    $html .= "<div class=\"body\">" . $this->getHeading() . "</div>";
    if ($this->shouldIncludeLastUpdateDate())
    {
      $html .= '<div class="result-updated-at-text-only">' . $this->getPrintUpdatedAt() . '</div>';
    }

    $html .= $this->getMoreInformationText();

    $html .= "</div>";
    $html .= "</div>";
    $html .= "</div>";

    return $html;
  }

  /**
   *  Return a snippet with a short summary
   *  and a photograph of this article.
   *
   *  @return string
   */
  public function getPhotoSummaryRepresentation($orientation = 'left', $width_percentage = 100)
  {
    $html  = "<div class=\"photo-summary\" style=\"width:$width_percentage%;\">";
    $html .= "<div class=\"photo-summary-content\">";
	  $html .= "<div class=\"photo ".(($orientation == 'left')?"img-left":"img-right")."\">" . (($this->hasMultimedia()) ? $this->getMultimedia()->getHTMLRepresentation("m") : "&nbsp;") . "</div>";
	  $html .= "<div class=\"content\">";
    $html .= "<h1 class=\"front-title\">" . $this->getLinkedTitle() . "</h1>";
    $html .= "<div class=\"body\">" . $this->getHeading() . "</div>";
    if ($this->shouldIncludeLastUpdateDate())
    {
      $html .= '<div class="result-updated-at-text-only">' . $this->getPrintUpdatedAt() . '</div>';
    }
    //$html .= $this->getHTMLReference('[' . __('+ info') . ']', array('class' => 'more-information'));
    $html .= $this->getMoreInformationText();
    $html .= "</div>";
    $html .= "</div>";
    $html .= "</div>";
    return $html;
  }

  /**
   *  Return a snippet with a short summary
   *  and a photograph of this article.
   *
   *  @return string
   */
  public function getTextOverPhotoRepresentation($width_percentage = 100)
  {
    sfLoader::loadHelpers(array('I18N'));

    $str  = '<div class="text-over-photo" style="width:'.$width_percentage.'%;">';
    $str .= '<div class="text-over-photo-container">';
    $str .= '<h1 class="front-title">' . $this->getLinkedTitle() . '</h1> ';
    $str .= '<div class="body">' . $this->getHeading() . '</div>';
    if ($this->shouldIncludeLastUpdateDate())
    {
      $str .= '<div class="result-updated-at-text-only">' . $this->getPrintUpdatedAt() . '</div>';
    }
    //$str .= $this->getHTMLReference(__('[+ info]'), array("class" => "more-information"));
    $str .= $this->getMoreInformationText();
    $str .= '<div class="photo">';
    if ($this->hasMultimedia())
    {
      $str .= $this->getMultimedia()->getHTMLRepresentation('m');
    }
    $str .= '</div>';
    $str .= '</div>';
    $str .= '</div>';
    return $str;
  }

  /**
   *  Return a snippet with a short summary
   *  and a photograph of this article.
   *
   *  @return string
   */
  public function getPhotoOverTextRepresentation($width_percentage = 100)
  {
    sfLoader::loadHelpers(array('I18N'));

    $str  = '<div class="photo-over-text" style="width:'.$width_percentage.'%;">';
    $str .= '<div class="photo-over-text-container">';
    $str .= '<div class="photo">';
    if ($this->hasMultimedia())
    {
      $str .= $this->getMultimedia()->getHTMLRepresentation('m');
    }
    $str .= '</div>';
    $str .= '<h1 class="front-title">' . $this->getLinkedTitle() . '</h1>';
    $str .= '<div class="body">' . $this->getHeading() . '</div>';
    if ($this->shouldIncludeLastUpdateDate())
    {
      $str .= '<div class="result-updated-at-text-only">' . $this->getPrintUpdatedAt() . '</div>';
    }
    //$str .= $this->getHTMLReference(__('[+ info]'), array("class" => "more-information"));
    $str .= $this->getMoreInformationText();
    $str .= '</div>';
    $str .= '</div>';

    return $str;
  }

  /**
   *  Return a snippet with a short text-only
   *  summary of this article.
   *
   *  @return string
   */
  public function getTextOnlySummaryRepresentation($width_percentage = 100, $link_title = true)
  {
    sfLoader::loadHelpers(array('I18N'));

    $title = $link_title ? $this->getLinkedTitle() : $this->getTitle();

    $str  = '<div class="text-only" style="width:'.$width_percentage.'%;">';
    $str .= '<div class="text-only-container">';
    $str .= '<h1 class="front-title">' . $this->getLinkedTitle() . '</h1> ';
    $str .= '<div class="body">' . $this->getHeading() . '</div>';
    if ($this->shouldIncludeLastUpdateDate())
    {
      $str .= '<div class="result-updated-at-text-only">' . $this->getPrintUpdatedAt() . '</div>';
    }
    $str .= $this->getMoreInformationText();
    $str .= '</div>';
    $str .= '</div>';

    return $str;
  }


  /**
   *  Return a snippet with a short text-only
   *  summary of this article.
   *
   *  @return string
   */
  public function getTextOnlySummaryRepresentationWithoutLink($width_percentage = 100, $link_title = false)
  {
    sfLoader::loadHelpers(array('I18N'));

    $title = $link_title ? $this->getLinkedTitle() : $this->getTitle();

    $str  = '<div class="text-without-link" style="width:'.$width_percentage.'%;">';
    $str .= '<div class="text-only-container">';
    $str .= '<h1 class="front-title">' . $title . '</h1> ';
    $str .= '<div class="body">' . $this->getHeading() . '</div>';
    if ($this->shouldIncludeLastUpdateDate())
    {
      $str .= '<div class="result-updated-at-text-only">' . $this->getPrintUpdatedAt() . '</div>';
    }
    $str .= $this->getMoreInformationText();
    $str .= '</div>';
    $str .= '</div>';

    return $str;
  }



  /**
   *  Return a snippet with the image of this article.
   *  If $link_image is true, a link will be included
   *  otherwise, only the image will be output.
   *
   *  @param $link_image Boolean True if a link should be included.
   *  @return string
   */
  public function getOnlyPhotoRepresentation($link_image = true, $width_percentage = 100)
  {
    sfLoader::loadHelpers(array("Url", "Tag"));
    $str = '<div class="only-photo">';
    $str .= '<div class="photo" style="width:'.$width_percentage.'%;">';
    if ($this->hasMultimedia())
    {
      if ($link_image)
      {
        $params = ($this->getReferenceType() == self::REFERENCE_TYPE_EXTERNAL) ? array('target' => '_blank') : array();
        $str .= link_to($this->getMultimedia()->getHTMLRepresentation('m'), $this->getURLReference(), $params);
      }
      else
      {
        $str .= $this->getMultimedia()->getHTMLRepresentation('m');
      }
    }
    $str .= '</div>';
    $str .= '</div>';

    return $str;
  }

  /**
   *  Return a string with the raw text, that is
   *  as it is stored in the database (with all the
   *  inner use tags and all the references to other
   *  articles by id).
   *
   *  @return string
   */
  public function getRawText()
  {
    return $this->getBody();
  }

  /**
   *  Return a string with the text suitable for human
   *  editing: with all the article references by id
   *  replaced by their named version.
   *
   *  @return string
   */
  public function getTextForEditor()
  {
    return $this->getRawText();
  }

  /**
   *  Return a string with the HTML representation of the
   *  text in the article (with all the tags of inner use
   *  replaced with HTML tags).
   *
   *  @return string
   */
  public function getHTMLText()
  {
    sfLoader::loadHelpers(array('I18N'));

    $text = $this->getRawText();

    // Multimedia : {{multimedia:id}}
    foreach ($this->getReferencedMultimedia() as $ref)
    {
      $clickeable = 1 == preg_match(sprintf("/\{\{multimedia:%d:c\s*(\|(.*?))?\}\}/e",$ref->getId()), $text);
      $text = preg_replace(sprintf("/\{\{multimedia:%d(:c)?\s*(\|(.*?))?\}\}/e",$ref->getId()),'$ref->getHTMLRepresentation("'. ($clickeable?'c':'m') . '")',$text,1);
    }

    // Gallery : {{galeria:id}}
    $i = 1;
    foreach ($this->getReferencedGalleries() as $ref)
    {
      $text = preg_replace(sprintf("/\{\{galeria:%d\s*(\|(.*?))?\}\}/e",$ref->getId()),'$ref->getHTMLRepresentation("m")',$text,1);
      $description = $ref->getDescription();
      $text .= sprintf("<noscript><div class='gallery-links'>%s</div></noscript>", link_to(
                                                          __("Galeria Asociada: %%description%%",
                                                          array("%%description%%" => (!empty($description))?$description:__("galería %%num%%", array("%%num%%" => ($i++))))),
                                                          $ref->getURLReference($this->getId()),
                                                          array('title' => $ref->getDescription()))
                                                          );
    }

    // Documents : {{documento:id}}
    foreach ($this->getReferencedDocuments() as $ref)
    {
      $text = preg_replace(sprintf("/\{\{documento:%d\s*\}\}/e",$ref->getId()),'$ref->getHTMLRepresentation()',$text,1,$count);
      if (!$count)
        $text = preg_replace(sprintf("/\{\{documento:%d\s*\|(.*?)\}\}/e",$ref->getId()),'$ref->getHTMLRepresentation("$1")',$text,1);
    }

    // Forms : {{formulario:id}}
    foreach ($this->getReferencedForms() as $ref)
    {
      $text = preg_replace(sprintf("/\{\{formulario:%d\s*(\|(.*?))?\}\}/e",$ref->getId()),'$ref->getIsActive()?$ref->getHTMLRepresentation():__("Ya no es posible completar el formulario")',$text,1);
    }

    // Articles : [[articulo:id]]
    // Articles : [[articulo:id|descripcion o multimedia]]
    foreach ($this->getReferencedArticles() as $ref)
    {
      $text = preg_replace(sprintf("/\[\[articulo:%d\s*\]\]/e",$ref->getId()),'$ref->getHTMLReference()',$text,1,$count);
      if (!$count)
        $text = preg_replace(sprintf("/\[\[articulo:%d\s*\|(.*?)\]\]/e",$ref->getId()),'$ref->getHTMLReference("$1")',$text,1);
    }

    // RSS : {{rss:id:count}}
    foreach ($this->getReferencedRssChannels() as $ref)
    {
      $text = preg_replace(sprintf("/\{\{rss:%d\s*:(\d+)\s*\}\}/e",$ref->getId()),'$ref->getHTMLRepresentation("$1",false)',$text,1,$count);
    }

    //At this point every tag not replaced are obsolete references. We show default cases for each case
    $text = preg_replace("/\{\{multimedia:\d+\s*(\|(.*?))?\}\}/e",'Multimedia::getNullHTMLRepresentation("m","$2")',$text);
    $text = preg_replace("/\{\{documento:\d+\s*(\|(.*?))?\}\}/e",'Document::getNullHTMLRepresentation("$2")',$text);
    $text = preg_replace("/\{\{formulario:\d+\s*(\|(.*?))?\}\}/e",'Form::getNullHTMLRepresentation("$2")',$text);
    $text = preg_replace("/\[\[articulo:\d+\s*(\|(.*?))?\]\]/e",'Article::getNullHTMLRepresentation("$2")',$text);
    $text = preg_replace("/\{\{galeria:\d+\s*(\|(.*?))?\}\}/e",'Gallery::getNullHTMLRepresentation("$2")',$text);

    $text = preg_replace("/\{\{rss:\d+\s*:\d+\s*\}\}/e",'RssChannel::getNullHTMLRepresentation("Elemento RSS")',$text);

    return $text;
  }

  /**
   *  Sets the text of the article from $editorText,
   *  which is expressed with named references.
   *  This method translates all those named references
   *  to references by id, and stores the result
   *  in the instance variable $_text.
   *  The return value will be true if $editorText
   *  was successfully translated and stored,
   *  otherwise false will be returned.
   *
   *  @return boolean
   */
  public function setTextFromEditor($editorText)
  {
    $this->deleteReferences();
    sfLoader::loadHelpers(array('I18N'));
    $rawText = $editorText;
    $refs = preg_split("/\}\}/", $editorText);
    foreach ($refs as $ref)
    {
      //Multimedia
      if (preg_match("/\{\{multimedia:(\d+)(:c)?\s*(\|.*)?/",$ref,$matches))
      {
        // $matches[1] holds the id
        try
        {
          $aa = new ArticleMultimedia();
          $aa->setMultimediaId(floatval($matches[1]));
          $this->addArticleMultimedia($aa);
        }
        catch(Exception $e)
        {
          throw new Exception(__("Referenced multimedia not found. Referenced ID ".$matches[1]));
        }
      }
      //Gallery
      if (preg_match("/\{\{galeria:(\d+)\s*(\|.*)?/",$ref,$matches))
      {
        // $matches[1] holds the id
        try
        {
          $aa = new ArticleGallery();
          $aa->setGalleryId(floatval($matches[1]));
          $this->addArticleGallery($aa);
        }
        catch(Exception $e)
        {
          throw new Exception(__("Referenced gallery not found. Referenced ID ".$matches[1]));
        }
      }

      //Document
      if (preg_match("/\{\{documento:(\d+)\s*(\|.*)?/",$ref,$matches))
      {
        // $matches[1] holds the id
        try
        {
          $aa = new ArticleDocument();
          $aa->setDocumentId(floatval($matches[1]));
          $this->addArticleDocument($aa);
        }
        catch(Exception $e)
        {
          throw new Exception(__("Referenced document not found. Referenced ID ".$matches[1]));
        }
      }
      //Form
      if (preg_match("/\{\{formulario:(\d+)\s*(\|.*)?/",$ref,$matches))
      {
        // $matches[1] holds the id
        try
        {
          $aa = new ArticleForm();
          $aa->setFormId(floatval($matches[1]));
          $this->addArticleForm($aa);
        }
        catch(Exception $e)
        {
          throw new Exception(__("Referenced form not found. Referenced ID ".$matches[1]));
        }
      }

      //RSS
      if (preg_match("/\{\{rss:(\d+)\s*:[0-9]+(.*)?/",$ref,$matches))
      {
        // $matches[1] holds the id
        try {
          $aa = new ArticleRssChannel();
          $aa->setRssChannelId(floatval($matches[1]));
          $this->addArticleRssChannel($aa);
        }
        catch(Exception $e)
        {
          throw new Exception(__("Referenced RSS Channel not found. Referenced ID ".$matches[1]));
        }
      }
    }

    $refs = preg_split("/\]\]/", $editorText);
    foreach ($refs as $ref)
    {
      //Articles
      if (preg_match("/\[\[articulo:(\d+)\s*(\|.*)?/",$ref,$matches))
      {
        // $matches[1] holds the id
        try
        {
          $aa = new ArticleArticle();

          $aa->setArticleRefereeId(floatval($matches[1]));
          $this->addArticleArticleRelatedByArticleRefererId($aa);
        }
        catch(Exception $e)
        {
          throw new Exception(__("Referenced article not found. Referenced ID ".$matches[1]));
        }
      }
    }

    $this->setBody($rawText);

    return true;
  }

  public function deleteReferences()
  {
    $c = new Criteria();
    $c->add(ArticleArticlePeer::ARTICLE_REFERER_ID, $this->getId());
    ArticleArticlePeer::doDelete($c);

    $c = new Criteria();
    $c->add(ArticleMultimediaPeer::ARTICLE_ID, $this->getId());
    ArticleMultimediaPeer::doDelete($c);

    $c = new Criteria();
    $c->add(ArticleGalleryPeer::ARTICLE_ID, $this->getId());
    ArticleGalleryPeer::doDelete($c);

    $c = new Criteria();
    $c->add(ArticleDocumentPeer::ARTICLE_ID, $this->getId());
    ArticleDocumentPeer::doDelete($c);

    $c = new Criteria();
    $c->add(ArticleFormPeer::ARTICLE_ID, $this->getId());
    ArticleFormPeer::doDelete($c);
  }

  /**
   *  Return an array holding all the Articles referenced
   *  by this article.
   *
   *  @return array
   */
  public function getReferencedArticles()
  {
    $c = new Criteria();
    $c->add(ArticleArticlePeer::ARTICLE_REFERER_ID, $this->getId());
    $c->addJoin(ArticlePeer::ID, ArticleArticlePeer::ARTICLE_REFEREE_ID);

    return ArticlePeer::doSelect($c);
  }

  /**
   *  Return an array holding all the Multimedia elements
   *  referenced by this article.
   *
   *  @return array
   */
  public function getReferencedMultimedia()
  {
    $c = new Criteria();
    $c->addJoin(ArticleMultimediaPeer::MULTIMEDIA_ID, MultimediaPeer::ID);
    $c->add(ArticleMultimediaPeer::ARTICLE_ID, $this->getId());

    return MultimediaPeer::doSelect($c);
  }

  /**
   *  Return an array holding all the Gallery elements
   *  referenced by this article.
   *
   *  @return array
   */
  public function getReferencedGalleries()
  {
    $c = new Criteria();
    $c->addJoin(ArticleGalleryPeer::GALLERY_ID, GalleryPeer::ID);
    $c->add(ArticleGalleryPeer::ARTICLE_ID, $this->getId());

    return GalleryPeer::doSelect($c);
  }

  /**
   *      Return an array holding all the Document elements
   *      referenced by this article.
   *
   *      @return array
   */
   public function getReferencedDocuments()
   {
    $c = new Criteria();
    $c->addJoin(ArticleDocumentPeer::DOCUMENT_ID, DocumentPeer::ID);
    $c->add(ArticleDocumentPeer::ARTICLE_ID, $this->getId());

    return DocumentPeer::doSelect($c);
   }

  /**
   *      Return an array holding all the Forms elements
   *      referenced by this article.
   *
   *      @return array
   */
   public function getReferencedForms()
   {
    $c = new Criteria();
    $c->addJoin(ArticleFormPeer::FORM_ID, FormPeer::ID);
    $c->add(ArticleFormPeer::ARTICLE_ID, $this->getId());

    return FormPeer::doSelect($c);
   }

  /**
   *  Return the string representation associated to $number,
   *  as follows:
   *    0 => full HTML
   *    1 => photo inscription with image on the left
   *    2 => photo summary with image on the left
   *    3 => text only summary
   *    4 => only title
   *    5 => text over photo
   *    6 => only photo with a link
   *    7 => photo summary with image on the right
   *    8 => only photo without link
   *    9 => photo inscription with image on the right
   *
   *  Convenience method, it simply forwards the correct method.
   *
   *  @return string
   */
  public function getRepresentationByNumber($number, $for_template = false, $width_percentage = 100)
  {
    sfLoader::loadHelpers(array("I18N"));
    
    $with_navigation = CmsConfiguration::getUseNavigationInArticles();
    
    switch ($number)
    {
      case (0):
        if ($for_template)
          $response = $this->getFullHTMLRepresentation();
        else
          $response = $this->getActionToolbar($with_navigation) . $this->getFullHTMLRepresentation() . $this->getFooter();
        break;
      case (1):
        $response = $this->getPhotoInscriptionRepresentation('left', $width_percentage);
        break;
      case (2):
        $response = $this->getPhotoSummaryRepresentation('left', $width_percentage);
        break;
      case (3):
        $response = $this->getTextOnlySummaryRepresentation($width_percentage);
        break;
      case (4):
        $response = $this->getListItemRepresentation($width_percentage);
        break;
      case (5):
        $response = $this->getTextOverPhotoRepresentation($width_percentage);
        break;
      case (6):
        $response = $this->getOnlyPhotoRepresentation(true, $width_percentage);
        break;
      case (7):
        $response = $this->getPhotoSummaryRepresentation('right', $width_percentage);
        break;
      case (8):
        $response = $this->getOnlyPhotoRepresentation(false, $width_percentage);
        break;
      case (9):
        $response = $this->getPhotoInscriptionRepresentation('right', $width_percentage);
        break;
      case (10):
        $response = $this->getPhotoOverTextRepresentation($width_percentage);
        break;
      case (11):
        $response = $this->getTextOnlySummaryRepresentationWithoutLink($width_percentage, false);
        break;
      default:
        $response = "<p>" . __("Representación de artículo no soportada") . "</p>";
    }

    return $response;
  }

  /**
   *  Return an associative array holding an integer id for
   *  each representation as the key, and a string description
   *  of that representation.
   *
   *  @return array
   */
  public function getAvailableRepresentationStrings()
  {
    return self::getAvailableRepresentations();
  }

  /**
   *  Return an associative array holding an integer id for
   *  each representation as the key, and a string description
   *  of that representation.
   *
   *  @return array
   */
  static public function getAvailableRepresentations()
  {
    sfLoader::loadHelpers(array('I18N'));

    return array(
            3  => __('Resumen sólo texto'),
            0  => __('HTML Completo'),
            1  => __('Foto epígrafe con foto a la izquierda'),
            2  => __('Resumen con foto a la izquierda'),
            4  => __('Sólo título'),
            5  => __('Texto sobre foto'),
            6  => __('Sólo imagen con vínculo'),
            7  => __('Resumen con foto a la derecha'),
            8  => __('Sólo imagen sin vínculo'),
            9  => __('Foto epígrafe con foto a la derecha'),
            10 => __('Foto sobre texto'),
            11 => __('Sólo título sin link')
          );
  }

  public function __toString()
  {
    return $this->getTitle();
  }

  public function getCurrentStatus()
  {
    sfLoader::loadHelpers(array('I18N'));

    if ($this->getIsArchived())
    {
      return __('Archivado'). ' (' . $this->getArchivedAt('d/m/Y H:i') . ')';
    }
    elseif ($this->getIsPublished())
    {
      return __('Publicado'). ' (' . $this->getPublishedAt('d/m/Y H:i') . ')';
    }

    return __('Sin publicar');
  }

  public function setIsPublished($isPublished)
  {
    if ($isPublished)
    {
      if ($this->isNew()) $this->setPublishedAt(date('Y-m-d H:i'));
      $this->setIsArchived(false);
    }

    return parent::setIsPublished($isPublished);
  }

  public function setIsArchived($isArchived)
  {
    if ($isArchived)
    {
      $this->setArchivedAt(date('Y-m-d H:i'));
      $this->setIsPublished(false);
    }

    return parent::setIsArchived($isArchived);
  }

  public function setUnpublished()
  {
    $this->setIsPublished(false);
    $this->setIsArchived(false);

    return true;
  }

  public function isNotArchived()
  {
    return !$this->getIsArchived();
  }

  public function isPublish()
  {
    return $this->getIsPublished();
  }

  public function isUnpublish()
  {
    return !$this->getIsPublished();

  }

  public static function belongsToArticle($multimedia_id)
  {
    $c = new Criteria();
    $c->add(MultimediaPeer::ID, $multimedia_id);

    return (ArticleMultimediaPeer::doCount($c) > 0);
  }

  public function getAuthor()
  {
    $author = sfGuardUserPeer::retrieveByPK($this->getCreatedBy());

    return ($author) ? $author->getName() : '';
  }

  public function getAuthorUpdated()
  {
    $author = sfGuardUserPeer::retrieveByPK($this->getUpdatedBy());

    return ($author) ? $author->getName() : '';
  }

  public function isReferenced()
  {
    $c = new Criteria();
    $c->add(ArticleArticlePeer::ARTICLE_REFEREE_ID, $this->getId());

    return (ArticleArticlePeer::doCount($c) > 0);
  }

  /*Interface implementation*/
  public static function getSlotletMethods()
  {
    return array('getSlotlet');
  }

  public static function getSlotletName()
  {
    return __("Novedades");
  }

  /**
   *  Returns a piece of HTML code holding the representation
   *  of the (News) Slotlet for Article class. The elements to include
   *  will be obtained through the option parameter 'section_name'.
   *  If 'include_ancestor_sections' is set in options, then the news from the
   *  ancestors sections will be included.
   *  If 'include_children_sections' is set in options, then the news from the
   *  children sections will be included.
   *  The resulting HTML code should look like this:
   *  If the there's a cookie:
   *    <code>
   *        <div id="news">
   *            <div class="title">News</div>
   *                <div class="content">
   *                    <div class="first">Actual representation of a piece of NEWS</div>
   *                </div>
   *            </div>
   *        </div>
   *    </code>
   *
   *  If there isn't any news:
   *    <code>
   *        <div id="news">
   *            <div class="title">News</div>
   *                <div class="content">
   *                    <div class="first">There is no news show</div>
   *                </div>
   *            </div>
   *        </div>
   *    </code>
   *
   *  @param $options Array The options passed to the Slotlet.
   *
   *  @return string The HTML code of the Slotlet.
   */
  public static function getSlotlet($options)
  {
    sfLoader::loadHelpers(array('I18N'));

    if (!(array_key_exists('sort_by_priority', $options) && isset($options['sort_by_priority'])))
    {
      $options['sort_by_priority'] = CmsConfiguration::get('check_sort_news_by_priority', true);
    }

    if (!(array_key_exists('include_ancestor_sections', $options) && isset($options['include_ancestor_sections'])))
    {
      $options['include_ancestor_sections'] = CmsConfiguration::get('check_include_ancestor_sections', true);
    }

    if (!(array_key_exists('include_children_sections', $options) && isset($options['include_children_sections'])))
    {
      $options['include_children_sections'] = CmsConfiguration::get('check_include_children_sections', true);
    }

    if (!(array_key_exists('section_name', $options) && isset($options['section_name'])))
    {
      $options['section_name'] = sfContext::getInstance()->getRequest()->getParameter("section_name", self::HOME);
    }

    sfContext::getInstance()->getResponse()->addStylesheet('frontend/slotlet/sl_news');

    $section  = SectionPeer::retrieveByName($options['section_name']);
    if(is_null($section))
    {
      $section = SectionPeer::retrieveHomeSection();
    }
    $articles = array();
    $template = <<<EOF
<div id="news" class="news_slotlet slotlet">
  <div class="title">
    <a class="rss_icon" href="%rss_url%"><img src="%rss_icon%" alt="%rss_alt%" title="%rss_title%" /></a>
    %title%
  </div>
EOF;

    $str = strtr($template, array(
      '%title%'     => __('Novedades'),
      '%rss_url%'   => url_for('feed/news?section='.$section->getName()),
      '%rss_icon%'  => image_path('common/rss.png'),
      '%rss_title%' => __('Canal RSS de novedades'),
      '%rss_alt%'   => __('RSS')
    ));

    $str .= '<div class="content">';

    if ($section)
    {
      $articles = $section->getSortedNews($options['include_ancestor_sections'],
                                          $options['include_children_sections'],
                                          null,
                                          $options['sort_by_priority']);
      if (count($articles) > 0)
      {
        $articles = array_slice($articles, 0, CmsConfiguration::get('max_news', 5));
        $article = array_shift($articles);
        $str .= "<div class=\"content-child first\">".$article->getHTMLReference()."</div>";
        foreach ($articles as $article)
        {
          $str .= "<div class=\"content-child\">".$article->getHTMLReference()."</div>";
        }
      }
      else
      {
        $str .= "<div class=\"content-child first\">".__("Sin contenidos para mostrar")."</div>";
      }
    }
    else
    {
      //Nothing to show, we are not in a section
      $str .= "<div class=\"content-child first\">".__("Sin contenidos para mostrar")."</div>";
    }
    $str .= "</div><div class=\"footer\"></div></div>";

    return $str;
  }
  /*End Interface implementation*/

    /**
   *  Return the string representation associated to the type of the article,
   *  as follows:
   *    0 => Article
   *    1 => News
   *    2 => "Novedad"
   *
   *  @return string
   */
  public function getTypeText()
  {
    sfLoader::loadHelpers(array('I18N'));

    switch ($this->getType())
    {
      case self::ARTICLE:
        return __("Artículo");
        break;
      case self::NEWS:
        return __("Novedad");
        break;
      case self::INSTITUTIONAL:
        return __("Noticia");
        break;
    }
  }

  public function getTooltipText()
  {
    sfLoader::loadHelpers(array('I18N', 'Asset', 'Tag'));

    return sprintf("<div>%s \"%s\"</div>",
                   ($this->getMultimediaId()) ? image_tag('backend/photo.gif', array('alt' => __('Multimedia presente'), 'title' => __('Multimedia presente'))) : '',
                   $this->getTitle());
  }

  public static function validateBody($editorText)
  {
    $refs = preg_split("/\}\}/", $editorText);
    foreach ($refs as $ref)
    {
      //Multimedia
      if (preg_match("/\{\{multimedia:(\d+)\s*(\|.*)?/",$ref,$matches))
      {
        // $matches[1] holds the id
        $obj=MultimediaPeer::retrieveByPk(floatval($matches[1]));
        if (is_null($obj)) return false;
      }

      //Gallery
      if (preg_match("/\{\{galeria:(\d+)\s*(\|.*)?/",$ref,$matches))
      {
        $obj=GalleryPeer::retrieveByPk(floatval($matches[1]));
        if (is_null($obj)) return false;
      }

      //Document
      if (preg_match("/\{\{documento:(\d+)\s*(\|.*)?/",$ref,$matches))
      {
        $obj=DocumentPeer::retrieveByPk(floatval($matches[1]));
        if (is_null($obj)) return false;
      }

      //Form
      if (preg_match("/\{\{formulario:(\d+)\s*(\|.*)?/",$ref,$matches))
      {
        $obj=FormPeer::retrieveByPk(floatval($matches[1]));
        if (is_null($obj)) return false;
      }

      //RSS
      if (preg_match("/\{\{rss:(\d+)\s*:(\d+)\s*/",$ref,$matches))
      {
        $obj=RssChannelPeer::retrieveByPk(floatval($matches[1]));
        if (is_null($obj)) return false;
      }
    }

    $refs = preg_split("/\]\]/", $editorText);
    foreach ($refs as $ref)
    {
      //Articles
      if (preg_match("/\[\[articulo:(\d+)\s*(\|.*)?/",$ref,$matches))
      {
        $obj=ArticlePeer::retrieveByPk(floatval($matches[1]));
        if (is_null($obj)) return false;
      }
    }

    return true;
  }

  public function getSearchResultRepresentation()
  {
    $str  = '<div class="result">';
    $str .= '<div class="result-title">' . $this->getLinkedTitle() . '</div> ';
    if ($this->shouldIncludeLastUpdateDate())
    {
      $str .= '<div class="result-updated-at-text-only">' . $this->getPrintUpdatedAt() . '</div>';
    }
    $str .= '<div class="result-body">' . $this->getHeading() . '</div>';
    $str .= '</div>';

    return $str;
  }

  public function isIndexable()
  {
    return $this->getIsPublished();
  }

  public function save($con = null)
  {
    parent::save();
    //If my type equals self::NEWS and I have a section set,
    //generate the corresponding ArticleSection element, if it doesn't exist.
    if ($this->getType() == self::NEWS && $this->getSectionId())
    {
      $c = new Criteria();
      $c->add(ArticleSectionPeer::ARTICLE_ID, $this->getId());
      $c->add(ArticleSectionPeer::SECTION_ID, $this->getSectionId());
      if (ArticleSectionPeer::doCount($c) === 0)
      {
        $as = new ArticleSection();
        $as->setArticleId($this->getId());
        $as->setSectionId($this->getSectionId());
        $as->save();
      }
    }
    else
    {
      $c = new Criteria();
      $c->add(ArticleSectionPeer::ARTICLE_ID, $this->getId());
      ArticleSectionPeer::doDelete($c);
    }
  }

  public function getObfuscatedContact()
  {
    if (CmsConfiguration::get('check_obfuscate_mail_addresses', true))
    {
      return self::obfuscate(($this->getContact()) ? $this->getContact(): CmsConfiguration::get('contact_mail'));
    }
    else
    {
      return ($this->getContact() ? $this->getContact() : CmsConfiguration::get('contact_mail'));
    }
  }

  public static function obfuscate($mail)
  {
    sfLoader::loadHelpers(array('I18N'));

    $mail = strrev(str_replace(array('@','.'),array(__(']en['),__('].[')),$mail));

    return $mail;
  }

  public static function unobfuscate($mail)
  {
    if (CmsConfiguration::get('check_obfuscate_mail_addresses', true))
    {
      sfLoader::loadHelpers(array('I18N'));
      $mail = strrev($mail);
      $mail = str_replace(array(']en[','].['),array(__('[en]'),__('[.]')),$mail);
    }

    return $mail;
  }

  /**
   *  Return an array holding all the Rss Channels referenced
   *  by this article.
   *
   *  @return array
   */
  public function getReferencedRssChannels()
  {
    $c = new Criteria();
    $c->add(ArticleRssChannelPeer::ARTICLE_ID, $this->getId());
    $c->addJoin(RssChannelPeer::ID, ArticleRssChannelPeer::RSS_CHANNEL_ID);

    return RssChannelPeer::doSelect($c);
  }

  public function canBeShown()
  {
    return ($this->getIsPublished() || $this->getIsArchived());
  }

  protected function canBeModifiedWhenPublishedOrArchived()
  {
    return
        ($context= sfContext::getInstance()) && 
          ((
          $context->getUser()->hasCredential(array('designer','reporter'),false) &&
          !$this->getIsPublished() &&
          !$this->getIsArchived()
          ) 
          ||
          $context->getUser()->hasCredential(array('designer_admin','reporter_admin'),false)
          );
  }

  public function canEdit()
  {
      return $this->canBeModifiedWhenPublishedOrArchived() && 
        ($context = sfContext::getInstance()) && 
        $context->getUser()->checkSectionCredentialsFor($this->getSection());
  }

  public function canPublish()
  {
    return $this->isUnpublish() && ($context = sfContext::getInstance()) &&
        $context->getUser()->checkSectionCredentialsFor($this->getSection());
  }

  public function canUnpublish()
  {
    return $this->isPublish() && ($context = sfContext::getInstance()) &&
        $context->getUser()->checkSectionCredentialsFor($this->getSection());
  }

  public function canArchive()
  {
    return $this->isNotArchived() && ($context = sfContext::getInstance()) &&
        $context->getUser()->checkSectionCredentialsFor($this->getSection());
  }

  public function canDelete()
  {
    if ( !$this->canBeModifiedWhenPublishedOrArchived() ) return false;
 
    if ( !(($context = sfContext::getInstance()) && 
          $context->getUser()->checkSectionCredentialsFor($this->getSection()))) return false;


    $criteria = new Criteria();
    $criteria->add(ArticleArticlePeer::ARTICLE_REFEREE_ID,$this->getId());
    $article_article_count = ArticleArticlePeer::doCount($criteria);

    if ($article_article_count)
      return false;

    $criteria2 = new Criteria();
    $criteria2->add(EventPeer::ARTICLE_ID, $this->getId());
    $event_count = EventPeer::doCount($criteria2);

    if ($event_count)
      return false;

    $criteria3 = new Criteria();
    $criteria3->add(SectionPeer::ARTICLE_ID,$this->getId());
    $section_count = SectionPeer::doCount($criteria3);

    if ($section_count)
      return false;

    $criteria4 = new Criteria();
    $criteria4->add(ArticlePeer::REFERENCE_TYPE, self::REFERENCE_TYPE_ARTICLE);
    $criteria4->add(ArticlePeer::REFERENCE,$this->getId());
    $reference_count = ArticlePeer::doCount($criteria4);

    if ($reference_count)
      return false;

    return true;
  }


  public function  delete($con = null)
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

  private function getMoreInformationText()
  {
    sfLoader::loadHelpers(array('I18N'));
    $str = '<div class="more-information-div">';
    $str .= $this->getHTMLReference(__(CmsConfiguration::get('full_text_article', 'Para leer el artículo completo, haga click en el título del mismo.')), array('class' => 'more-information'));
    $str .= '</div>';

    return $str;
  }

  public function getTimestampForRssFeed()
  {
    if (CmsConfiguration::get('check_use_published_at_for_rss_feeds', true))
    {
      return $this->getPublishedAt('U');
    }
    else
    {
      return $this->getUpdatedAt('U');
    }
  }

  public function getRssFeedContent()
  {
    return nl2br($this->getHeading());
  }

  public function getMultimediaForRssFeed($size = 'm')
  {
    if (!$this->hasMultimedia())
    {
      return '&nbsp;';
    }

    return $this->getMultimedia()->getHTMLRepresentation($size);
  }

  public function getRssFeedEnclosure()
  {
    if ($this->hasMultimedia())
    {
      return $this->getMultimedia()->asRssFeedEnclosure();
    }

    return null;
  }

  public function getFeedCategories()
  {
    if ($section = $this->getSection())
    {
      if ($section->hasColor())
      {
        return array($section->getColor());
      }
    }
    
    return array();
  }

  /**
   * Get a dynamic Gallery made up from this Article's related
   * Multimedia objects. The dynamic Gallery is not saved.
   *
   * @return Gallery
   */
  public function getGallery($con = null)
  {
    if ($this->hasMainGallery())
    {
      return parent::getGallery($con);
    }
    else if (0 < $this->countArticleMultimedias() || $this->hasMultimedia())
    {
      $gallery = new Gallery();

      return $this->buildGallery($gallery);
    }
  }

  public function hasMainGallery()
  {
    return (null !== $this->getMainGalleryId());
  }

  /**
   * Load a Gallery object holding every Multimedia object
   * related to this Article. The resulting Gallery is not saved.
   *
   * @param  Gallery $gallery The gallery to build.
   *
   * @return Gallery
   */
  public function buildGallery($gallery)
  {
    if ($this->hasMultimedia())
    {
      $gallery->addMultimedia($this->getMultimedia());
    }

    foreach ($this->getArticleMultimediasJoinMultimedia() as $article_multimedia)
    {
      $gallery->addMultimedia($article_multimedia->getMultimedia());
    }

    return $gallery;
  }

  /**
   * Alias for getTitle() used by sfLucene to distinguish between generic titles
   * and the titles of the articles.
   * 
   * @return string
   */
  public function getArticle_title()
  {
    return $this->getTitle();
  }

  /**
   * Create a clone of this Article, changing its name
   * so it's unique and return it.
   *
   * @return Article
   */
  public function duplicate()
  {
    $clone = new self();
    $this->copyInto($clone);
    $clone->setName($clone->getName().' (copia)');
    return $clone;
  }


}
sfLucenePropelBehavior::getInitializer()->setupModel('Article');
