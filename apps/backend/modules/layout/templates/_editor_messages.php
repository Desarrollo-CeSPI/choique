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
<?php if ($sf_flash->has('notice')): ?>
<div style="background-color: #a5f986; border: 1px solid #73b65a; text-align: right; margin: 3px; border-radius: 3px; -moz-border-radius: 3px; -webkit-border-radius: 3px;">
  <h2 style="font-size: 11px; color: #275d12; margin: 3px;"><?php echo __($sf_flash->get('notice')) ?></h2>
</div>
<?php endif; ?>
