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

  function escape_string($string, $make_lowercase = true, $trim = true)
  {
    $replacements = array(
        "á" => "a", "é" => "e", "í" => "i", "ó" => "o", "ú" => "u", "à" => "a", "è" => "e", "ì" => "i",
        "ò" => "o", "ù" => "u", "ñ" => "n", "ç" => "c", "ü" => "u", "Á" => "a", "É" => "e", "Í" => "i",
        "Ó" => "o", "Ú" => "u", "À" => "a", "È" => "e", "Ì" => "i", "Ò" => "o", "Ù" => "u", "Ñ" => "n",
        "Ç" => "c", "Ü" => "u", " " => "_", "'" => "_", "\"" => "_", ":" => "_", "!" => "_", "?" => "_",
        "¿" => "_", "¡" => "_", "/" => "_", "\\" => "_", "-" => "_", "(" => "_", ")" => "_", "[" => "_",
        "]" => "_", "`" => "_", "=" => "_", "+" => "_", "$" => "_", "%" => "_", "&" => "_", "," => "_",
        ";" => "_", "<" => "_", ">" => "_", "{" => "_", "}" => "_", "*" => "_", "^" => "_", "º" => "o",
        "ª" => "a", "|" => "_", "@" => "_", "~" => "_", "#" => "_", "." => "_", "·" => "_", "," => "_",
        "´" => "_", "≠" => "_", "”" => "_", "“" => "_", "÷" => "_", "¬" => "_", "∞" => "_", "¢" => "c",
        "¨" => "_", "„" => "_", "…" => "_", "–" => "_", "‚" => "_"
      );

    $source = $string;

    if ($trim)
    {
      $source = trim($source);
    }

    if ($make_lowercase)
    {
      $source = strtolower($source);
    }

    foreach ($replacements as $original => $replacement)
    {
      $source = str_replace($original, $replacement, $source);
    }

    return $source;
  }