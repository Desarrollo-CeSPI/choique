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
 * Subclass for representing a row from the 'layout' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Layout extends BaseLayout
{
  /**
   * Get a default layout configuration for empty layouts.
   * It can either be encoded (default) or not.
   *
   * @param  bool $encoded TRUE if the return value must be encoded.
   *
   * @return mixed array or string
   */
  protected function getDefaultLayout($encoded = true)
  {
    $default_layout = array(
      'options'    => array(),
      'rows'       => array()
    );

    return $encoded ? json_encode($default_layout) : $default_layout;
  }

  /**
   * Decode $encoded_layout and return it. If $encoded_layout is empty,
   * the decoded default layout is returned.
   *
   * @param  string $encoded_layout
   *
   * @return stdClass The decoded layout
   */
  public function decode($encoded_layout)
  {
    if ('' == trim($encoded_layout))
    {
      $encoded_layout = $this->getDefaultLayout();
    }

    return json_decode($encoded_layout);
  }

  /**
   * Encode $decoded_layout and return it. If $decoded_layout is empty,
   * the encoded default layout is returned.
   *
   * @param  array $decoded_layout
   *
   * @return string The encoded layout
   */
  public function encode($decoded_layout)
  {
    if (!is_array($decoded_layout))
    {
      $decoded_layout = $this->getDefaultLayout(false);
    }

    return json_encode($decoded_layout);
  }

  /**
   * Get the layout configuration for articles.
   *
   * @return array
   */
  public function getArticleLayout()
  {
    return $this->decode(parent::getArticleLayout());
  }

  /**
   * Set the layout configuration for articles to $v.
   */
  public function setArticleLayout($v)
  {
    if (!$this->isEncoded($v))
    {
      $v = $this->encode($v);
    }

    parent::setArticleLayout($v);
  }

  private function isEncoded($v)
  {
    return is_string($v);
  }

  public function basicSetArticleLayout($v)
  {
    parent::setArticleLayout($v);
  }
  
  /**
   * Get the layout configuration for templates.
   *
   * @return array
   */
  public function getTemplateLayout()
  {
    return $this->decode(parent::getTemplateLayout());
  }

  public function isTemplateLayoutEmpty()
  {
    return (null === parent::getTemplateLayout());
  }

  /**
   * Set the layout configuration for templates to $v.
   */
  public function setTemplateLayout($v)
  {
    parent::setTemplateLayout($this->encode($v));
  }

  public function basicSetTemplateLayout($v)
  {
    parent::setTemplateLayout($v);
  }

  /**
   * Answer whether this Layout can be deleted, i.e. it is not referenced by
   * any section nor it is the default layout
   * .
   * @return bool
   */
  public function canBeDeleted()
  {
    return (!$this->getIsDefault() && $this->countSections() == 0);
  }

  /**
   * Answer whether this Layout can be set as the default layout, i.e. it is
   * not the default layout.
   * 
   * @return bool
   */
  public function canBeSetAsDefault()
  {
    return (!$this->getIsDefault());
  }

  public function __toString()
  {
    return $this->getName().($this->getIsDefault() ? ' (Por defecto)' : '');
  }

  /**
   * Set this Layout as the default one.
   *
   * Transactionally unset the current default layout (if any) and set this
   * as the default layout.
   *
   * Return TRUE if everything went fine, or FALSE otherwise
   *
   * @param  PDO $con
   * 
   * @return bool
   */
  public function becomeDefault($con = null)
  {
    if (null === $con)
    {
      $con = Propel::getConnection(LayoutPeer::DATABASE_NAME);
    }

    try
    {
      $con->begin();

      if (!$this->getIsDefault())
      {
        $default_layout = LayoutPeer::retrieveDefault($con);

        if (!$default_layout->isNew())
        {
          $default_layout->setIsDefault(false);
          $default_layout->save($con);
        }
      }

      $this->setIsDefault(true);
      $this->save($con);

      $con->commit();

      return true;
    }
    catch (PropelException $exception)
    {
      $con->rollback();
      
      return false;
    }
  }

  /**
   * Get the name of the virtual section to which this Layout applies.
   * 
   * @return string
   */
  public function getVirtualSection()
  {
    return VirtualSection::getInstance()->getById($this->getVirtualSectionId());
  }

  public function getVirtualSectionName()
  {
    return !$this->getVirtualSectionId()?'Todas':VirtualSection::getInstance()->getById($this->getVirtualSectionId());
  }

  /**
   * Create a clone of this Layout, changing its name
   * so it's unique and return it.
   *
   * This method doesn't use parent's copy() as the layout
   * configurations wouldn't get copied.
   *
   * @return Layout
   */
  public function duplicate()
  {
    $clone = new self();

    $clone->setName(LayoutPeer::getNameForDuplicate($this->getName()));
    $clone->basicSetArticleLayout($this->article_layout);
    $clone->basicSetTemplateLayout($this->template_layout);
    $clone->setIsDefault(false);
    $clone->setVirtualSectionId(null);

    return $clone;
  }
  
}