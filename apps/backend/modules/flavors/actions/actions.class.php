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
 * flavors actions.
 *
 * @package    choique
 * @subpackage flavors
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class flavorsActions extends sfActions
{
  public function executeDeleteFlavor(){
    $this->labels = $this->getLabels();
    if($this->getRequest()->getMethod() == sfRequest::POST)
    {
      $flavors = $this->getRequest()->getParameter('flavors[]');

      $not_deleted = '';
      foreach ($flavors as $flavor)
      { 
        $dir = choiqueFlavors::getPath().'/'.$flavor;
        if (!$this->deleteDirectory($dir))
        {
          $not_deleted =  ($not_deleted == '')?$flavor: $not_deleted . ', ' . $flavor;
        }
      }
      if ($not_deleted != '')
      {
        $this->setFlash('error', 'Los estilos ' . $not_deleted . ' no han podido ser eliminados');
      }
      else 
      {
        $this->setFlash('notice', 'El estilo visual ha sido eliminado correctamente.');
      }
    }
  }

  public function executeCheckFlavorExist(){
      sfLoader::loadHelpers(array('CmsEscaping'));
      $name = $this->getRequest()->getParameter('name');
      $dir = '../flavors/'.escape_string($name,true,true);
      if(!file_exists($dir))
        return sfView::NONE;
  }

  public function getLabels()
  {
     return array(
          'flavor{select}' => 'Estilo visual',
          'flavor{name}' => 'Nombre:',
          'flavor{file}' => 'Archivo:'
     );
  }

  // Function to recursively add a directory,
  // sub-directories and files to a zip archive
  private function addFolderToZip($dir, $zipArchive, $zipdir = '')
  {
    if(is_dir($dir)&&(!strpos($dir,'.svn'))) {
      if($dh = opendir($dir))
      {
      // Loop through all the files
        while (($file = readdir($dh)) !== false) 
        {
          //If it's a folder, run the function again!
          if(!is_file($dir . $file))
          {
          // Skip parent and root directories
            if( ($file !== ".") && ($file !== ".."))
            {
              $this->addFolderToZip($dir . $file . "/", $zipArchive, $zipdir . $file . "/");
            }
          }
          else
          {
            // Add the files
            $zipArchive->addFile($dir . $file, $zipdir . $file);
          }
        }
      }
    }
  }

  private function compressDirectory($dir)
  {
    $zip = new ZipArchive();
    $filename = tempnam(sys_get_temp_dir(),"choique-flavor");
    if ($zip->open($filename, ZIPARCHIVE::OVERWRITE)!==TRUE) {
        throw new Exception();
    }
    $this->addFolderToZip("$dir/", $zip);
    $zip->close();
    return $filename;
  }

  public function executeDownloadFlavor(){
    $flavor = $this->getRequest()->getParameter('flavor');
    $created_zip = false;
    $this->labels=array('zip'=>'Crear zip');
    try
    {
      switch($flavor)
      { 
        case 'current': 
          $created_zip = $this->compressDirectory(choiqueFlavors::getGlobalPath($relative=false));
          break;
        case 'default': 
          $created_zip = $this->compressDirectory(sfConfig::get('app_choique_default_flavor_dir'));
          break;
        default:
          if($this->getRequest()->getMethod() == sfRequest::POST)
            $this->getRequest()->setError('zip','Debe indicar qué estilo descargar');
      }
    }catch(Exception $e)
    {
      $this->getRequest()->setError('zip','No es posible escribir el zip a generar');
    }
    if (!$created_zip)
    {
      return sfView::SUCCESS;
    }
    /* Si ya se selecciono el flavor a descargar y el zip existe... */
    $response = $this->getContext()->getResponse();
    $response->setHttpHeader('Pragma', '');
    $response->setHttpHeader('Cache-Control', '');
    $data = file_get_contents($created_zip);
    $response->setHttpHeader('Content-Type', 'application/zip');
    $response->setHttpHeader('Content-Disposition', 'attachment; filename="'.$flavor.'.zip"');
    $response->setContent($data);
    $this->setLayout(false);
    unlink($created_zip);
    return sfView::NONE;
  }


  public function executeInstallFlavor()
  {
    $this->labels = $this->getLabels();

    if($this->getRequest()->getMethod() == sfRequest::POST)
    {
      $name = $this->getRequest()->getParameter('flavor_name');
      $file_name = $this->getRequest()->getFileName('flavor_file');

      if($name=='')
      {
        $this->getRequest()->setError('flavor{name}','Debe especificar un nombre de estilo visual.');
      }
      else
      {
        $flavors =  choiqueFlavors::getInstance()->getAll();
        if (in_array($name,$flavors))
          $this->getRequest()->setError('flavor{name}','ya existe un estilo visual con ese nombre');
      }

      if($file_name=='')
        $this->getRequest()->setError('flavor{file}','Debe seleccionar un archivo ZIP con la estructura del estilo visual.');
      else
      {
        $tmp_file=$this->getRequest()->getFilePath('flavor_file');
        $guesser = new MimeTypeGuesser();
        $mime_type = $guesser->guess($tmp_file);
        
        if($mime_type!="application/zip")
        {
          $this->getRequest()->setError('flavor{file}','El archivo seleccionado no es de formato ZIP.');
        }
        else
        {
          if(!$this->checkZipStructureFor($tmp_file))
            $this->getRequest()->setError('flavor{file}','La estructura del archivo ZIP no respeta la estructura basica de un estilo o no es posible crear el directorio temporal en el servidor.');
        }
      }      

      if(!$this->getRequest()->hasErrors())
      {
          sfLoader::loadHelpers(array('CmsEscaping'));
          $dir = choiqueFlavors::getPath().'/'.escape_string($name,true,true);

          //If flavor exist, delete it and create folder, else just create folder for new flavor
          if(is_dir($dir))
            $this->emptyDir($dir,true);

          mkdir($dir);
          chmod($dir, 0775);
          $zip = new ZipArchive();
          if($zip->open($tmp_file)==true)
          {
              $zip->extractTo($dir);
              $zip->close();
              @unlink($tmp_file);
              $this->chmod_recursive($dir, 0775);
              $this->setFlash('notice', 'El nuevo estilo visual ha sido instalado correctamente!');
          }
          else
              $this->setFlash('error', 'Error al procesar el archivo ZIP.');
      }
    }
  }

  public function checkZipStructureFor($tmp_file)
  {
    //create a temporary directory to extract de zip basic structure and the uploaded structure
    $tmp_dir = tempnam(sys_get_temp_dir(), "choique-upload-flavor");
    if ( !$tmp_dir ) return false;
    if ( !unlink($tmp_dir) ) return false;
    if ( !mkdir($tmp_dir)  ) return false;
    if ( !chmod($tmp_dir,0775) ) return false;

    $structure_basic_dir = sfConfig::get('app_choique_default_flavor_dir');
 

    //open the file uploaded by the user and extract it on the temporary directory
    $zip = new ZipArchive();
    $r = $zip->open($tmp_file);
    if( !($r ===true && $zip->extractTo($tmp_dir)) )
    {
      $this->setFlash('error', 'Error al procesar el archivo ZIP.');
      return false;
    }
    $zip->close(); 

    $directories = $this->getDirectoryNames(sfConfig::get('app_choique_default_flavor_dir'));
    $ok= $this->checkCompatibility($directories, $tmp_dir);
    $this->deleteDirectory($tmp_dir);
    return $ok;
  }

  private function deleteDirectory($dir) 
  {
    $result = true;
    $files = glob( $dir . '*', GLOB_MARK );
    foreach( $files as $file )
    {
      if( is_dir( $file ) )
        $result = $result && $this->deleteDirectory( $file );
      else
        $result = $result && @unlink( $file );
    }
    if (is_dir($dir)) 
      $result = $result && @rmdir( $dir );
    return $result;
  }

  private function emptyDir($dir, $deleteMe) 
  {
    $result = true;
    if(!$dh = @opendir($dir)) return false;
    while (false !== ($obj = readdir($dh))) 
    {
      if($obj == '.' || $obj == '..') continue;
      if (is_dir("$dir/$obj")) $this->emptyDir("$dir/$obj", true);
      else @unlink("$dir/$obj");
    }
    closedir($dh);
    if($deleteMe)
    {
      $result =  @rmdir($dir);
      if (is_dir($dir)) throw new Exception("No se ha podido eliminar el directorio $dir");
    }
    else
    {
      $pending_files= glob("$dir/*");
      $result= count($pending_files) == 0;
      if ( count($pending_files) ) throw new Exception("No se ha podido eliminar el contenido del directorio $dir");
    }
    return $result;
  }


  /* Usado para comparar que cada directorio en directories aparece en $dir */
  private function checkCompatibility ($directories , $dir)
  {
    $ok = true;
    foreach (array_keys($directories) as $k => $value)
    {
      $ok = $ok && $this->isInDirectory($dir, $value);
      if ($ok)
      {
        $ok = $this->checkCompatibility($directories[$value],$dir);
      }
    }
    return $ok;
  }
  
  /* Usado por checkCompatibility */
  private function isInDirectory($dir, $name)
  {
    $finder = sfFinder::type('dir')->name($name)->prune('.svn')->discard('.svn');
    return count($finder->in($dir)) != 0;
  }

  /* Usado por checkZipStructureFor */
  private function getDirectoryNames($directory)
  {
    $directories = array();
    foreach (new DirectoryIterator($directory) as $fileInfo) 
    {
      if($fileInfo->isDot()) 
      {
        continue;
      }
      elseif ($fileInfo->isDir() && $fileInfo->getFileName()!='.svn')
      {
        $directories[$fileInfo->getFilename()] = $this->getDirectoryNames($fileInfo->getPath() . '/'. $fileInfo->getFileName());
      }
    }
    return $directories;
  }

  public function executeSelectFlavor()
  {
    $this->labels = $this->getLabels();

    if($this->getRequest()->getMethod() == sfRequest::POST){
      $flavor = $this->getRequest()->getParameter('flavor_select');
      try
      {
        choiqueFlavors::getInstance()->setCurrent($flavor, true);
        choiqueFlavors::publishResources($flavor);
        $this->setFlash('notice', 'Se ha establecido el nuevo estilo visual correctamente!.');
        $this->redirect('flavors/selectFlavor');
      }
      catch(Exception $e)
      {
         $this->getRequest()->setError('flavor{select}',"No se ha podido establecer el nuevo estilo visual. Probablemente haya quedado inconsistente! El error ocurrido es: ".$e->getMessage());
      }
    }
  }

  private function recurse_copy($src,$dst) {
    if(strstr($src,".svn")) return 0;
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
      if (( $file != '.' ) && ( $file != '..' )) {
        if ( is_dir($src . '/' . $file) ) {
          $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
        }
        else {
           @copy($src . '/' . $file,$dst . '/' . $file);
        }
      }
    }
    closedir($dir);
  }


  private function chmod_recursive($pathname, $filemode) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathname), RecursiveIteratorIterator::SELF_FIRST);
    foreach($iterator as $item) {
      @chmod($item, $filemode);
    }
  }

}