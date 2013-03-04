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
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/"
                       xmlns:moz="http://www.mozilla.org/2006/browser/search/">
<InputEncoding>UTF-8</InputEncoding>
<ShortName><?php echo $shortname ?></ShortName>
<Description><?php echo $shortname ?> Engine</Description>
<Contact><?php echo $contactmail ?></Contact>
<Url type="text/html" template="<?php echo url_for('@search',true)?>?query={searchTerms}&amp;cms_search=1"/>
<LongName><?php echo $shortname ?></LongName>
<Image height="16" width="16" type="image/x-icon"><?php echo image_path('frontend/favicon.ico',true) ?></Image>
<Developer>Cespi-Unlp Development Team</Developer>
<Attribution>Copyright 2008 Cespi-Unlp Development Team</Attribution>
<SyndicationRight>open</SyndicationRight>
<AdultContent>false</AdultContent>
<Language>Es-AR</Language>
<OutputEncoding>windows-1251</OutputEncoding>
</OpenSearchDescription>