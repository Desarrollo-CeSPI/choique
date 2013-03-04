<?php $first = true ?>
<?php foreach ($this->getColumnCategories('show.display') as $category): ?>
<?php
  if ($category[0] == '-')
  {
    $category_name = substr($category, 1);
    $collapse = true;

    if ($first)
    {
      $first = false;
      echo "[?php use_javascript(sfConfig::get('sf_prototype_web_dir').'/js/prototype') ?]\n";
      echo "[?php use_javascript(sfConfig::get('sf_admin_web_dir').'/js/collapse') ?]\n";
    }
  }
  else
  {
    $category_name = $category;
    $collapse = false;
  }
?>
<fieldset id="sf_fieldset_<?php echo preg_replace('/[^a-z0-9_]/', '_', strtolower($category_name)) ?>" class="<?php if ($collapse): ?> collapse<?php endif; ?>">
<?php if ($category != 'NONE'): ?><h2>[?php echo __('<?php echo $category_name ?>') ?]</h2>

<?php endif; ?>

<?php foreach ($this->getColumns('show.display', $category) as $name => $column): ?>
<?php $credentials = $this->getParameterValue('show.fields.'.$column->getName().'.credentials') ?>
<?php if ($credentials): $credentials = str_replace("\n", ' ', var_export($credentials, true)) ?>
    [?php if ($sf_user->hasCredential(<?php echo $credentials ?>)): ?]
<?php endif; ?>
<div class="form-row">
  [?php echo label_for('<?php echo $this->getParameterValue("show.fields.".$column->getName().".label_for", $this->getSingularName()."[".$column->getName()."]") ?>', __($labels['<?php echo $this->getSingularName() ?>{<?php echo $column->getName() ?>}']), 'class="required" ') ?]
  <div class="content">
  [?php $value = <?php echo $this->getColumnShowTag($column); ?>; echo $value ? $value : '&nbsp;' ?]
  <?php echo $this->getHelp($column, 'show') ?>
  </div>
</div>
<?php if ($credentials): ?>
    [?php endif; ?]
<?php endif; ?>

<?php endforeach; ?>
</fieldset>
<?php endforeach; ?>

[?php include_partial('show_actions', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>)) ?]
