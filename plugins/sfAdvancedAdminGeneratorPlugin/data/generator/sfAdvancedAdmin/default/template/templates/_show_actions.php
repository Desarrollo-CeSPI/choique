<ul class="sf_admin_actions">
<?php $createActions = $this->getParameterValue('show.actions') ?>
<?php if (null !== $createActions): ?>
<?php foreach ((array) $createActions as $actionName => $params): ?>
  <?php if (substr($actionName, 0, 10) == '_has_many_'): ?>
    <?php echo $this->addCredentialCondition($this->getButtonToAction(sfInflector::camelize(substr($actionName, 10)), $params, true), $params) ?>
  <?php else: ?>
    <?php echo $this->addCredentialCondition($this->getButtonToAction($actionName, $params, true), $params) ?>
  <?php endif ?>
<?php endforeach; ?>
<?php else: ?>
  <?php echo $this->getButtonToAction('_list', array(), true) ?>
<?php endif; ?>
</ul>
