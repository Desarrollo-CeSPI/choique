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
<?php use_helper('I18N', 'Javascript') ?>

<div class="error404">
  <?php echo __("La página solicitada no se pudo cargar. Si usted tipeó la dirección, por favor chequee que no contenga errores. Si llegó aquí desde un vínculo de otra parte del sitio, por favor reporte este incidente por mail al administrador del sitio.") ?>
  <div class="actions">
    <?php echo link_to("Volver al inicio", "@homepage", array("title" => __("Volver a la página de inicio"), "alt" => __("Volver a la página de inicio"))) ?>
  </div>
</div>
