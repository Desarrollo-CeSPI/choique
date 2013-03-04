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
 * SearchQueryBuilder
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class SearchQueryBuilder
{
  static public function build($terms, $fields = array(), $options = array())
  {
    $terms = SearchSanitizer::sanitize($terms);
    
    $query = new SearchQuery();

    if (!is_array($terms))
    {
      $terms = array($terms);
    }

    foreach ($terms as $term)
    {
      $query->addTerm($term);
    }

    if (count($fields) == 0)
    {
      $query->useAllFields();
    }
    else if (array_keys($fields) !== range(0, count($fields) - 1))
    {
      // Associative array: Each value should indicate if the field is selected
      foreach ($fields as $field => $selection)
      {
        $query->addField($field, $selection);
      }
    }
    else
    {
      // Sequential array: assume all are selected
      foreach ($fields as $field)
      {
        $query->addField($field, true);
      }
    }

    if (isset($options['use_and']) && $options['use_and'])
    {
      $query->setOperand(SearchQuery::OP_AND);
    }

    return $query;
  }

}