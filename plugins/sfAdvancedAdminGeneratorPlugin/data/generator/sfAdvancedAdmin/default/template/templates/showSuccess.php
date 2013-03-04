[?php use_helper('Object', 'Validation', 'ObjectAdmin', 'I18N', 'Date') ?]

[?php use_stylesheet('<?php echo $this->getParameterValue('css', sfConfig::get('sf_admin_web_dir').'/css/main') ?>') ?]

<div id="sf_admin_container">

<h1><?php echo $this->getI18NString('show.title', 'show '.$this->getModuleName()) ?></h1>

<div id="sf_admin_header">
[?php include_partial('<?php echo $this->getModuleName() ?>/show_header', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>)) ?]
</div>

<div id="sf_admin_content">
[?php include_partial('<?php echo $this->getModuleName() ?>/show', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'labels' => $labels)) ?]
</div>

<div id="sf_admin_footer">
[?php include_partial('<?php echo $this->getModuleName() ?>/show_footer', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>)) ?]
</div>

</div>
