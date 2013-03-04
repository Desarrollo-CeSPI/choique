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
<?php sfLoader::loadHelpers(array('Number','I18N','Date')); ?>
<?php

class ExcelBuilder 
{
  public static $CellFormat = array(
		'alignment' => array(
      'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
      'wrap'     => true
      ),
		'borders' => array(
      'top'    => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
      'bottom' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
      'left'   => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
      'right'  => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
    ));
  
  public static $ResultFormat = array(
    'font'      => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT),
		'borders'   => array(
      'top'    => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
      'bottom' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
      'left'   => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
      'right'  => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
    ));

  public static $HeaderFormat = array(
    'font'     => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
		'borders' => array(
      'top'    => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
      'bottom' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
      'left'   => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
      'right'  => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
    ));
  
  public static $TitleFormat = array(
    'font' => array(
      'bold' => true,
      'size' => 12,
    ));
  
  public static $SubtitleFormat = array(
    'font' => array(
      'bold'   => true,
      'italic' => true,
      'size'   => 11,
    ));

	public function __construct($name=null,$orientation=PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
	{
		$this->objPHPExcel = new sfPhpExcel();
		$this->objPHPExcel->setActiveSheetIndex(0);
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation($orientation);
    if (!is_null($name))
    {
      $this->objPHPExcel->getActiveSheet()->setTitle($name);
    }
	} 
	
  public function addPage($name=null,$orientation=PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
  {
    $this->objPHPExcel->setActiveSheetIndex($this->objPHPExcel->getIndex($this->objPHPExcel->createSheet()));
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation($orientation);
    if (!is_null($name))
    {
      $this->objPHPExcel->getActiveSheet()->setTitle($name);
    }
  }

  public function mergeCells($cells)
  {
    $this->objPHPExcel->getActiveSheet()->mergeCells($cells);
  }

	public function writeCell($row,$column,$value,$format=array())
	{
		$this->objPHPExcel->getActiveSheet()->setCellValue($column.$row, $value);
    $this->applyStyle($row,$column,$format); 
	}


	public function applyStyle($row,$column,$format)
	{
		$this->objPHPExcel->getActiveSheet()->getStyle($column.$row)->applyFromArray($format);
	}
	
	public function writeRow($row, $from_column,$values,$format=array())
	{
		$column = $from_column;
		foreach($values as $value)
		{
			$this->writeCell($row,$column,$value,$format);
			$this->objPHPExcel->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
			$column = chr(ord($column) + 1);
			
		}
	}
	public function writeTable($from_row, $from_column,$matrix_of_values,$format=array())
  {
    $rows_written = 0;
    foreach($matrix_of_values as $values)
    {
      $this->writeRow($from_row++, $from_column,$values,$format);
      $rows_written++;
    }
    
    return $rows_written; 
  }

  public function toXLS($file)
  {
    $objWriter = new PHPExcel_Writer_Excel5($this->objPHPExcel);
    $objWriter->save($file);
  }

  public function toPDF($file)
  {
    $objWriter = new PHPExcel_Writer_PDF($this->objPHPExcel);
    $objWriter->save($file);
  }
}