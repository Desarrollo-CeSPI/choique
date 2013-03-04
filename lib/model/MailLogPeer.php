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
 * Subclass for performing query and update operations on the 'mail_log' table.
 *
 * 
 *
 * @package lib.model
 */ 
class MailLogPeer extends BaseMailLogPeer
{
  public static function getFieldNamesArray()
  {
    sfLoader::loadHelpers(array('I18N'));

    return array('mail_from'    =>  __('Mail Remitente'),
                 'mail_to'      =>  __('Mail Destinatario'),
                 'subject'      =>  __('Asunto'),
                 'body'         =>  __('Cuerpo'),
                 'sender_name'  =>  __('Remitente'),
                 'section_name' =>  __('Sección'),
                 'article_name' =>  __('Artículo'),
                 'created_at'   =>  __('Fecha'));
  }

  public static function getFieldMethodsArray()
  {
    return array('mail_from'    =>  'getMailFrom',
                 'sender_name'  =>  'getSenderName',
                 'mail_to'      =>  'getMailTo',
                 'subject'      =>  'getSubject',
                 'body'         =>  'getBody',
                 'section_name' =>  'getSectionName',
                 'article_name' =>  'getArticleName',
                 'created_at'   =>  'getCreatedAt');
  }

  public static function getXls($criteria)
  {
    sfLoader::loadHelpers(array('I18N'));

    $fields      = array('mail_from', 'sender_name', 'mail_to', 'subject', 'body', 'section_name', 'article_name', 'created_at');
    $eb          = new ExcelBuilder(__('Listado de Mails'), PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    $row         = 1;
    $header      = array();
    $field_names = self::getFieldNamesArray();
    foreach ($fields as $field)
    {
      $header[] = $field_names[$field];
    }
    $max_fields_count = count($header);
    
    //List's title
    $eb->writeCell($row++, 'A', __('Listado de Mails'), ExcelBuilder::$TitleFormat);
    $eb->mergeCells("A1:" . chr(ord('A') + $max_fields_count - 1) . "1");
    $dateFormat = new sfDateFormat(sfContext::getInstance()->getUser()->getCulture());

    //Header
    $eb->writeRow($row++, 'A', $header, ExcelBuilder::$HeaderFormat);

    //Mails
    $mails         = MailLogPeer::doSelect($criteria);
    $values        = array();
    $field_methods = self::getFieldMethodsArray();
    foreach ($mails as $mail)
    {
      $a_row = array();
      foreach ($fields as $field)
      {
        $v = $mail->$field_methods[$field]();
        if (is_array($v))
        {
          if (count($v) > 0)
          {
            $txt = '';
            foreach ($v as $i)
            {
              $txt .= $i . "\n";
            }
            $v = $txt;
          }
          else
          {
            $v = '-';
          }
        }
        elseif (is_bool($v))
        {
          $v = ($v) ? __('Sí') : __('No');
        }
        else
        {
          $v = (is_null($v) ? '-' : $v);
        }
        $a_row[] = $v;
      }
      $values[] = $a_row;
    }

    $first_row_value = $row;
    $row += $eb->writeTable($row, 'A', $values, ExcelBuilder::$CellFormat);
    $last_row_value = ($row == $first_row_value) ? $row++ : $last_row_value = $row - 1;

    return $eb;
  }
}