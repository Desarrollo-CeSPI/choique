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
 * @version    SVN: $Id: sfGuardPermissionPeer.php 3187 2007-01-08 10:51:03Z fabien $
 */
class PluginsfGuardPermissionPeer extends BasesfGuardPermissionPeer
{
  public static function retrieveByName($name)
  {
    $c = new Criteria();
    $c->add(self::NAME, $name);

    return self::doSelectOne($c);
  }
}
