<ul class="sf_admin_actions">
<?php if ($this->getParameterValue('belongs_to.class')): ?>
<?php echo $this->getButtonToAction('back', array('action' => 'back'), false) ?>
<?php endif ?>
<?php $listActions = $this->getParameterValue('list.actions') ?>
<?php if (null !== $listActions): ?>
  <?php foreach ((array) $listActions as $actionName => $params): ?>
    <?php echo $this->addCredentialCondition($this->getButtonToAction($actionName, $params, false), $params) ?>
  <?php endforeach; ?>
<?php else: ?>
  <?php echo $this->getButtonToAction('_create', array(), false) ?>
  <?php endif; ?>
</ul>
