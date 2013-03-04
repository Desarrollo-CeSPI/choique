<?php if ($this->getParameterValue('list.object_actions')): ?>
<td>
<ul class="sf_admin_td_actions">
<?php foreach ($this->getParameterValue('list.object_actions') as $actionName => $params): ?>
<?php if (isset($params['condition'])): ?>
<?php if (substr($actionName, 0, 10) == '_has_many_'): ?>
  [?php if ($<?php echo $this->getSingularName() ?>-><?php echo sfInflector::camelize($params['condition']) ?>()):  ?]
    <?php echo $this->addCredentialCondition($this->getHasManyLinkTo(sfInflector::camelize(substr($actionName, 10)), $params, true), $params) ?>
  [?php endif ?]
<?php else: ?>
  [?php if ($<?php echo $this->getSingularName() ?>-><?php echo sfInflector::camelize($params['condition']) ?>()):  ?]
    <?php echo $this->addCredentialCondition($this->getLinkToAction($actionName, $params, true), $params) ?>
  [?php endif ?]
<?php endif ?>
<?php else: ?>
<?php if (substr($actionName, 0, 10) == '_has_many_'): ?>
  <?php echo $this->addCredentialCondition($this->getHasManyLinkTo(sfInflector::camelize(substr($actionName, 10)), $params, true), $params) ?>
<?php else: ?>
  <?php echo $this->addCredentialCondition($this->getLinkToAction($actionName, $params, true), $params) ?>
<?php endif ?>
<?php endif ?>
<?php endforeach; ?>
</ul>
</td>
<?php endif; ?>
