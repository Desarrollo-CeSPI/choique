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

<?php if ($sf_request->hasError('section{section_links}')): ?>
 	<?php echo form_error('section{section_links}', array('class' => 'form-error-msg')) ?>
<?php endif; ?>

<?php list($links, $objects_associated, $assoc_ids) = _get_object_list($section, 'getSectionLinks', array(
	'control_name' => 'section[section_links]',
  'through_class' => 'SectionLink'), null)?>


<ul class="sf_admin_checklist">
	<?php foreach ($links as $link):?>
		<?php $relatedPrimaryKeyHtmlId = "associated_section_links_" . $link->getId()?>
		<li class="section_link">
			<span class="link">
				<?php echo checkbox_tag("associated_section_links[]", $link->getId(), in_array($link->getId(), $assoc_ids)) ?>
				<label for="<?php echo get_id_from_name('associated_section_links[]', $relatedPrimaryKeyHtmlId) ?>">
					<?php echo $link ?>
				</label>
			</span>

			<?php $section_link = SectionLinkPeer::retrieveBySectionIdAndLinkId($section->getId(), $link->getId()); ?>			

			<span class="target">
				<?php echo checkbox_tag("associated_section_links_target_" . $link->getId(), $link->getId(), is_null($section_link) ? false : $section_link->getTargetBlank())?>
				<label for="<?php echo get_id_from_name('associated_section_links[]', $relatedPrimaryKeyHtmlId) ?>">
					<?php echo __('Target blank ?') ?>
				</label>
			</span>

		</li>
	<?php endforeach ?>
</ul>