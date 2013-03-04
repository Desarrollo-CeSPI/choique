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
<?php $permissions = $sf_guard_user->getIsSuperAdmin()? array('Superusuario'):$sf_guard_user->getPermissions()?>
<?php $permission = array_shift($permissions) ?>
<?php echo $permission === null? '-': 
  (is_object($permission)? 
    sprintf("%s%s", $permission->getDescription(),
      preg_match('/^reporter/', $permission->getName()) && $sf_guard_user->getSection()? sprintf(" (%s)",$sf_guard_user->getSection()):"")
    : $permission) ?>
