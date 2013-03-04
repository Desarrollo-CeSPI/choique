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
<style type="text/css">
#wrapper,
#footer,
#container_
{
  margin: 0 auto;
}

.error404
{
  margin: 22% auto;
  border: 1px solid #EBE9ED;
  padding: 6px;
  width: 70%;
}

.error404 h1
{
  font-size: 14px;
  color: #666;
  border-bottom: 1px solid #ddd;
}

.error404 h2
{
  font-size: 12px;
  color: #666;
}

.error404 p
{
  color: #222;
}
</style>

<div id="container_">
  <div id="wrapper">
    <?php include_partial('global/header') ?>
    <div class="error404">
      <h1><?php echo __('Sitio en mantenimiento') ?></h1>
      <h2><?php echo __('Disculpe, en estos momentos estamos realizando tareas de mantenimiento en el sitio') ?>.</h2>
      <p><?php echo __('Intente nuevamente en unos instantes') ?>.</p>
    </div>
  </div>
  <?php include_partial('global/footer'); ?>
</div>