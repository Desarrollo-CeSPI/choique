<?php if (count($sf_guard_user->getGroups()) > 0): ?>
  <ul style="list-style: none">
  <?php foreach ($sf_guard_user->getGroups() as $group): ?>
    <li><?php echo $group->__toString() ?></li>
  <?php endforeach ?>
  </ul>
<?php else: ?>
  <?php echo __("El usuario no posee grupos asignados") ?>
<?php endif ?>
