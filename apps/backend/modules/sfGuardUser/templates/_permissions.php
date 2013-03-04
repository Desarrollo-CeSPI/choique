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
<?php foreach(sfGuardPermissionPeer::doSelect(new Criteria()) as $permission): ?>
    <?php $bool = array_key_exists( $permission->getName(), $sf_guard_user->getPermissions() ) ?>
    <?php $options = array('onchange' => sprintf("jQuery('#section_autocomplete_query, #clean_section_id').%s", !preg_match('/^reporter/',$permission->getName())?"attr('disabled',true)":"removeAttr('disabled')")) ?>
    <?php echo sprintf("%s %s",  radiobutton_tag('sf_guard_user[permissions]', $permission->getId(), $bool, $options ), $permission->getDescription())?><br />
<?php endforeach ?>
