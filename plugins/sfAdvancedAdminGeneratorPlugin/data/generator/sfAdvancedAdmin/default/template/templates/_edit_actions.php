<ul class="sf_admin_actions">
<?php $editActions = $this->getParameterValue('edit.actions') ?>
<?php if (null !== $editActions): ?>
<?php foreach ((array) $editActions as $actionName => $params): ?>
  <?php if (substr($actionName, 0, 10) == '_has_many_'): ?>
    <?php if ($actionName == '_delete') continue ?>
    <?php echo $this->addCredentialCondition($this->getButtonToAction(sfInflector::camelize(substr($actionName, 10)), $params, true), $params) ?>
  <?php else: ?>
    <?php if ($actionName == '_delete') continue ?>
    <?php echo $this->addCredentialCondition($this->getButtonToAction($actionName, $params, true), $params) ?>
  <?php endif ?>
<?php endforeach; ?>
<?php else: ?>
  <?php echo $this->getButtonToAction('_list', array(), true) ?>
  <?php echo $this->getButtonToAction('_save', array(), true) ?>
  <?php echo $this->getButtonToAction('_save_and_add', array(), true) ?>
<?php endif; ?>
</ul>
