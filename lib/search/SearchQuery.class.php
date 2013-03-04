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
 * SearchQuery
 *
 * A search query string for Lucene abstraction class.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class SearchQuery
{
  const FIELD_ALL      = 'all';
  const FIELD_TITLES   = 'titles';
  const FIELD_ARTICLES = 'articles';
  const FIELD_UPLOADS  = 'uploads';

  const OP_AND = 'and';
  const OP_OR  = 'or';

  private $fields = array();
  private $terms  = array();
  private $op     = self::OP_OR;
  private $min    = 3;

  private $lucene = array(
    self::FIELD_TITLES   => array('title', 'heading'),
    self::FIELD_ARTICLES => array('article_title', 'heading', 'body'),
    self::FIELD_UPLOADS  => array('document_title', 'content')
  );

  /**
   * Add a term to the set of terms specified for this SearchQuery object.
   * Return this object for a fluent API.
   *
   * @param  string $term The search term.
   *
   * @return SearchQuery
   */
  public function addTerm($term)
  {
    $this->terms[] = $term;

    return $this;
  }

  /**
   * Add a field to the set of selected fields for this SearchQuery object.
   * Note that only valid fields will be added.
   * Return this object for a fluent API.
   *
   * @param  string $field    The field id.
   * @param  bool   $selected True if the field is selected, false otherwise.
   *
   * @return SearchQuery
   */
  public function addField($field, $selected = true)
  {
    if ($this->isField($field))
    {
      $this->fields[$field] = (bool) $selected;
    }

    return $this;
  }

  /**
   * Set the operator used for joining the terms in this SearchQuery object.
   * Note that only a valid operator will be accepted.
   * Return this object for a fluent API.
   *
   * @param  string $op The operator id.
   *
   * @return SearchQuery
   */
  public function setOperator($op)
  {
    if ($this->isOp($op))
    {
      $this->op = $op;
    }

    return $this;
  }

  /**
   * Answer whether this SearchQuery has $field field selected.
   *
   * @param  string $field The field id.
   *
   * @return bool
   */
  public function hasField($field)
  {
    return array_key_exists($field, $this->fields) && true === $this->fields[$field];
  }

  /**
   * Answer whether this SearchQuery is empty - i.e., it has no terms.
   * 
   * @return bool
   */
  public function isEmpty()
  {
    return (count($this->terms()) == 0);
  }

  /**
   * Set this SearchQuery object to use all fields for the query.
   * Return this object for a fluent API.
   * 
   * @return SearchQuery
   */
  public function useAllFields()
  {
    $this->terms = array(self::FIELD_ALL => true);

    return $this;
  }

  /**
   * Answer whether this SearchQuery is a global query (for all fields).
   *
   * @return bool
   */
  public function isGlobal()
  {
    return (count($this->fields) == 0 || $this->hasAllFields());
  }

  /**
   * Answer whether this SearchQuery object has all the fields selected.
   * 
   * @return bool
   */
  public function hasAllFields()
  {
    return array_key_exists(self::FIELD_ALL, $this->fields) && $this->fields[self::FIELD_ALL];
  }

  /**
   * Set the minimum allowed length for any term in this SearchQuery.
   * Return this object for a fluent API.
   * 
   * @param  int $min The minimum length.
   *
   * @return SearchQuery
   */
  public function setMinimumLength($min)
  {
    $this->min = min($min, 2);
    
    return $this;
  }
  /**
   * Get the minimum allowed length for terms.
   * 
   * @return int
   */
  public function getMinimumLength()
  {
    return $this->min;
  }

  /**
   * Get this SearchQuery object as an sfLuceneCriteria object.
   *
   * @param  sfLucene $search The sfLucene instance.
   *
   * @return sfLuceneCriteria
   */
  public function asLuceneCriteria(sfLucene $search)
  {
    $criteria = new sfLuceneCriteria($search);

    $terms   = array();
    $phrases = array();

    foreach ($this->terms() as $term)
    {
      if (preg_match('/^"(.*)"$/', $term))
      {
        $phrases[] = explode(' ', substr($term, 1, strlen($term) - 2));
      }
      else
      {
        $terms[] = $term;
      }
    }

    $type  = $this->op === self::OP_OR ? null : true;

    if ($this->isGlobal())
    {
      $fields = array(null);
    }
    else
    {
      $fields = $this->fields();
    }

    foreach ($fields as $field)
    {
      foreach ($phrases as $phrase)
      {
        $criteria->addPhrase($phrase, $field, 0, $type);
      }

      if (count($terms) > 0)
      {
        $criteria->addMultiTerm($terms, $field, $type, $type);
      }
    }

    return $criteria;
  }

  /**
   * Get the string representation of this SearchQuery object, suitable for
   * querying sfLucene indexes.
   *
   * @return string
   */
  public function asString()
  {
    if ($this->isEmpty())
    {
      return '';
    }

    if ($this->isGlobal())
    {
      $terms = $this->terms();
    }
    else
    {
      $terms = array();

      foreach ($this->fields() as $field)
      {
        $terms = array_merge($terms, $this->terms($field));
      }
    }

    return implode($this->op(), $terms);
  }

  /**
   * Get the string representation of this SearchQuery.
   *
   * @see asString()
   *
   * @return string
   */
  public function __toString()
  {
    return $this->asString();
  }

  /**
   * Get the terms that make up this SearchQuery as an array of string
   * elements. This conversion allows to have nested SearchQuery objects.
   * 
   * @return array
   */
  private function terms($field = null)
  {
    $terms = array();

    foreach ($this->terms as $term)
    {
      if ('' !== $search_term = $this->term($term, $field))
      {
        $terms[] = $search_term;
      }
    }

    return $terms;
  }

  /**
   * Get the fields that have been selected for this SearchQuery as an array
   * of string elements, suitable for using them in a Lucene query string.
   *
   * @return array
   */
  private function fields()
  {
    $fields = array();

    foreach ($this->fields as $field => $selection)
    {
      if (true === $selection)
      {
        $lucene = $this->getLuceneFieldNames($field);

        if (count($lucene) > 0)
        {
          $fields = array_merge($fields, $lucene);
        }
      }
    }

    return array_unique($fields);
  }

  /**
   * Get the string representation of this operator to use in this SearchQuery
   * in a suitable way for using it inside a Lucene query string.
   *
   * @return string
   */
  private function op()
  {
    return $this->op === self::OP_OR ? ' || ' : ' && ';
  }

  /**
   * Answer whether $field is a valid field id.
   *
   * @param  string $field The field id.
   *
   * @return bool
   */
  private function isField($field)
  {
    return in_array($field, array(
      self::FIELD_ALL,
      self::FIELD_ARTICLES,
      self::FIELD_TITLES,
      self::FIELD_UPLOADS
    ));
  }

  /**
   * Answer whether $op is a valid operator id.
   *
   * @param  string $op The operator id.
   *
   * @return bool
   */
  private function isOp($op)
  {
    return in_array($op, array(self::OP_OR, self::OP_AND));
  }

  /**
   * Get the name(s) of the field $field as known in Lucene.
   * 
   * @param  string $field The field id.
   *
   * @return string
   */
  private function getLuceneFieldNames($field)
  {
    return $this->lucene[$field];
  }

  /**
   * Get the string representation of $term, optionally binding it to $field.
   *
   * @param  mixed  $term  Can either be a string or another SearchQuery object.
   * @param  string $field The Lucene name of a field, optional.
   * 
   * @return string
   */
  private function term($term, $field = null)
  {
    if (is_string($term))
    {
      $term = trim($term);
    }
    else if ($term instanceof self)
    {
      $term = strval($term);
    }

    if (strlen($term) < $this->getMinimumLength())
    {
      return '';
    }
    else
    {
      return (null === $field ? $term : sprintf('%s:(%s)', $field, $term));
    }
  }

}