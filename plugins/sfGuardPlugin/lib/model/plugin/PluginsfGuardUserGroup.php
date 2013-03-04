<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGuardUserGroup.php 4939 2007-08-30 14:00:49Z fabien $
 */
class PluginsfGuardUserGroup extends BasesfGuardUserGroup
{
  public function save($con = null)
  {
    parent::save($con);

    $this->getsfGuardUser($con)->reloadGroupsAndPermissions();
  }
}
