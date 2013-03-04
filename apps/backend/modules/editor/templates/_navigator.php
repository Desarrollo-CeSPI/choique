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
<div class="base-path">
  <?php echo $base_path ?>
  <a class="refresh" href="#" onclick="refreshNavigator(); return false;"><?php echo image_tag('backend/refresh.png', array('alt' => __('Recargar'), 'title' => __('Volver a cargar'))) ?></a>
</div>
<ul>
<?php foreach ($dirs as $real => $dir): ?>
  <li class="dir">
    <a href="#" onclick="navigate('<?php echo $real ?>'); return false;">
      <?php echo $dir ?>
    </a>
  </li>
<?php endforeach; ?>

<?php foreach ($files as $real => $file): ?>
  <li class="file">
    <a href="#editor" onclick="edit('<?php echo $real ?>'); return false;">
      <?php echo $file ?>
    </a>
  </li>
<?php endforeach; ?>
</ul>

<script type="text/javascript">
//<![CDATA[
//jQuery('#f_upload').uploadifySettings('scriptData', {'r': '<?php echo $b_upload ?>'});

function refreshUpload(base_upload)
{
  var regex=/^web\/(css|images)/;
  jQuery('#b_upload').val(base_upload);
  if (base_upload.match(regex))
  {
    jQuery('#f_upload').show();
    jQuery('#f_upload_submit').show();
  }
  else
  {
    jQuery('#f_upload').hide();
    jQuery('#f_upload_submit').hide();
  }
}

refreshUpload('<?php echo $b_upload?>');

function refreshNavigator()
{
  navigate('<?php echo $b_upload ?>');
}
//]]>
</script>