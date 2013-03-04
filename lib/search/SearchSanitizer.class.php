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
 * SearchSanitizer
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class SearchSanitizer
{
  /**
   * QUE HACEMOS CON LOS ACENTOS???!!
   */
  const SPECIAL_CHARACTERS = '+-&|!(){}[]^~*?:\\';
  const SPACE_CHARACTERS   = " \t\n\r";
  const SENTENCE_DELIMITER = '"';
  const LUCENE_OR          = '||';
  const GLUE               = ' ';

  static private $instance = null;

  private function __construct()
  {
    // Make this constructor private
  }

  /**
   * Get the unique instance of this class.
   * 
   * @return SearchSanitizer
   */
  static private function getInstance()
  {
    if (null === self::$instance)
    {
      self::$instance = new self();
    }

    return self::$instance;
  }

  /**
   * Sanitize $query and return it.
   *
   * @param  string $query The query string to sanitize.
   * 
   * @return array
   */
  static public function sanitize($query)
  {
    return self::getInstance()->parseQuery($query);
  }

  /**
   * Check if the double quotes on $query are balanced (even). If not, remove
   * them all.
   * After that, add slashes to any special character that may appear in $query.
   *
   * @param  string $query The query to escape.
   * 
   * @return string
   */
  private function escapeSpecialCharacters($query)
  {
    $quotes = preg_match_all('/'.self::SENTENCE_DELIMITER.'/', $query, $discard);

    if ($quotes > 0 && ($quotes % 2) != 0)
    {
      $query = preg_replace('/'.self::SENTENCE_DELIMITER.'/', '', $query, 1);
    }

    $escaped = addcslashes($query, self::SPECIAL_CHARACTERS);
    return mb_strtolower($escaped, 'UTF-8');
  }

  /**
   * Check if $character is a sentence delimiter character.
   *
   * @param  char $character The character to test.
   *
   * @return bool
   */
  private function isSentenceDelimiter($character)
  {
    return $character == self::SENTENCE_DELIMITER;
  }

  /**
   * Check if $character is a space delimiter character.
   *
   * @param  char $character The character to test.
   *
   * @return bool
   */
  private function isSpaceDelimiter($character)
  {
    return in_array($character, str_split(self::SPACE_CHARACTERS));
  }

  /**
   * Split $query into terms and return them as an array.
   * Terms may be words or phrases.
   *
   * @param  string $query The query string to split into terms.
   *
   * @return array
   */
  private function splitTerms($query)
  {
    $terms = array();

    for ($i = 0; $i < strlen($query); $i++)
    {
      if ($this->isSentenceDelimiter($query[$i]))
      {
        $term = array($query[$i]);

        // Copy the sentence
        for ($i++; ($i < strlen($query)) && !$this->isSentenceDelimiter($query[$i]); $term[] = $query[$i++])
          ;

        if (($i < strlen($query)) && ($this->isSentenceDelimiter($query[$i])))
        {
          $term[] = $query[$i];

          if (count($term) > 2)
          {
            $terms[] = implode($term);
          }
        }

        $term = array();
      }

      // Consume any remaining spaces
      for (; ($i < strlen($query)) && $this->isSpaceDelimiter($query[$i]); $i++)
        ;

      // If there is no space nor a sentence beginning...
      if (($i < strlen($query)) && !$this->isSentenceDelimiter($query[$i]) && !$this->isSpaceDelimiter($query[$i]))
      {
        $term = array();

        for (; ($i < strlen($query)) && !$this->isSentenceDelimiter($query[$i]) && !$this->isSpaceDelimiter($query[$i]); $term[] = $query[$i++])
          ;

        if (count($term) > 0)
        {
          $terms[] = implode($term);
        }

        $term = array();

        // Go back one character if this is a sentence beginning
        if (($i < strlen($query)) && $this->isSentenceDelimiter($query[$i]))
        {
          $i--;
        }
      }
    }
    
    return $terms;
  }

  /**
   * Build a query expression from $terms.
   *
   * @param  array $terms The terms that make up the expression.
   *
   * @return string
   */
  private function buildQueryExpression($terms)
  {
    return implode(self::GLUE, $terms);
  }

  /**
   * Parse $query and return a sanitized, clean, escaped version of it.
   *
   * @param  string $query The query to parse.
   *
   * @return array
   */
  public function parseQuery($query)
  {
    return $this->splitTerms($this->escapeSpecialCharacters($query));
  }

}