<?php if ($this->getParameterValue('list.object_actions')): ?>
<td>
<ul class="sf_admin_td_actions">
<?php foreach ($this->getParameterValue('list.object_actions') as $actionName => $params): ?>
<?php if (isset($params['condition'])): ?>
  [?php if ($<?php echo $this->getSingularName() ?>-><?php echo $params['condition'] ?>()): ?]
<?php endif ?>
  <?php echo $this->addCredentialCondition($this->getLinkToAction($actionName, $params, true), $params) ?>
<?php if (isset($params['condition'])): ?>
  [?php endif ?]
<?php endif ?>
<?php endforeach; ?>
</ul>
</td>
<?php endif; ?>
