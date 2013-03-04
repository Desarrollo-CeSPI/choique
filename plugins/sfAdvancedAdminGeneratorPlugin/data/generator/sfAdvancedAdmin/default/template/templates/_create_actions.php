<ul class="sf_admin_actions">
<?php $createActions = $this->getParameterValue('create.actions') ?>
<?php if (null !== $createActions): ?>
<?php foreach ((array) $createActions as $actionName => $params): ?>
  <?php if ($actionName == '_delete') continue ?>
  <?php echo $this->addCredentialCondition($this->getButtonToAction($actionName, $params, true), $params) ?>
<?php endforeach; ?>
<?php else: ?>
  <?php echo $this->getButtonToAction('_list', array(), true) ?>
  <?php echo $this->getButtonToAction('_save', array(), true) ?>
  <?php echo $this->getButtonToAction('_save_and_add', array(), true) ?>
<?php endif; ?>
</ul>
