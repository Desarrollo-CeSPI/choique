<ul class="sf_admin_actions">
<?php $createActions = $this->getParameterValue('show.actions') ?>
<?php if (null !== $createActions): ?>
<?php foreach ((array) $createActions as $actionName => $params): ?>
<?php if (isset($params['condition'])): ?>
  [?php if ($<?php echo $this->getSingularName() ?>-><?php echo $params['condition'] ?>()): ?]
<?php endif ?>
  <?php echo $this->addCredentialCondition($this->getButtonToAction($actionName, $params, true), $params) ?>
<?php if (isset($params['condition'])): ?>
  [?php endif ?]
<?php endif ?>
<?php endforeach; ?>
<?php else: ?>
  <?php echo $this->getButtonToAction('_list', array(), true) ?>
<?php endif; ?>
</ul>
