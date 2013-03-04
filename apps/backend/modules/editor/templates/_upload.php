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
<form action="<?php echo url_for('editor/upload') ?>" method="post" enctype="multipart/form-data" id="upload_form">
  <input type="hidden" name="r" id="b_upload" value="<?php echo $base ?>" size="60" />
  
  <!--<label for="f_upload"><?php echo __('Subir un archivo en el directorio actual') ?></label>-->
  <input type="file" name="f" id="f_upload" />

  <input type="submit" value="<?php echo __('Subir') ?>" id="f_upload_submit" />
</form>
