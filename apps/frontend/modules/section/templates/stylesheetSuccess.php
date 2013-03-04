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
/**
 * Stylesheet holding the colors of "<?php echo $name ?>" section.
 *
 * Use this classes to add section-dependant color schemes to your stylesheets.
 *
 * To add this stylesheet to your layout, put this in the <head> section:
 *
 *   <?php echo '<?php if (isset($section)): ?>'."\n" ?>
 *     <link rel="stylesheet" type="text/css" media="all" href="<?php echo '<?php echo url_for(\'@section_css?name=\'.$section->getName() ?>'."\n" ?>" />
 *   <?php echo '<?php endif; ?>'."\n" ?>
 *
 */

/* Background and foreground colors */
.section-color-bg { background-color: <?php echo $color ?>; }
.section-color-fg { color: <?php echo $color ?>; }

/* Border colors */
.section-color-border-all { border-color: <?php echo $color ?>; }
.section-color-border-t { border-top-color: <?php echo $color ?>; }
.section-color-border-r { border-right-color: <?php echo $color ?>; }
.section-color-border-b { border-bottom-color: <?php echo $color ?>; }
.section-color-border-l { border-left-color: <?php echo $color ?>; }

/* On hover colors */
.section-color-bg-hover:hover { background-color: <?php echo $color ?>; }
.section-color-fg-hover:hover { color: <?php echo $color ?>; }
.section-color-border-all-hover:hover { border-color: <?php echo $color ?>; }
.section-color-border-t-hover:hover { border-top-color: <?php echo $color ?>; }
.section-color-border-r-hover:hover { border-right-color: <?php echo $color ?>; }
.section-color-border-b-hover:hover { border-bottom-color: <?php echo $color ?>; }
.section-color-border-l-hover:hover { border-left-color: <?php echo $color ?>; }

<?php try { include_partial('section/extra_stylesheet', array('name' => $name, 'color' => $color, 'rgba' => $rgba, 'section' => $section)); } catch (sfException $e) { ; } ?>