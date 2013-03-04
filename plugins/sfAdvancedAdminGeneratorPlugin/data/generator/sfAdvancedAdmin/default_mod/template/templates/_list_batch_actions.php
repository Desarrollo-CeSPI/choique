<?php $batchActions = $this->getParameterValue('list.batch_actions') ?>
<?php if (!is_null($batchActions)): ?>
  <div id="sf_admin_batch_action_choice">
      Para los items seleccionados:
      <select name="sf_admin_batch_action" id="sf_admin_batch_action">
        <option value="">[?php echo __('Elija una acción') ?]</option>
        <?php foreach ((array) $batchActions as $actionName => $params): ?>
          <?php echo $this->addCredentialCondition($this->getOptionToAction($actionName, $params), $params) ?>
        <?php endforeach; ?>
      </select>
      [?php echo submit_tag(__('Ejecutar'), array('onClick' => "if ($('sf_admin_batch_action').value == '') return false;")) ?]
    </form>
  </div>
<?php endif; ?>
