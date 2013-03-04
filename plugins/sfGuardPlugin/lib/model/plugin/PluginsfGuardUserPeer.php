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
 * @version    SVN: $Id: sfGuardUserPeer.php 3810 2007-04-17 19:37:51Z davedash $
 */
class PluginsfGuardUserPeer extends BasesfGuardUserPeer
{
  public static function retrieveByUsername($username, $isActive = true)
  {
    $c = new Criteria();
    $c->add(self::USERNAME, $username);
    $c->add(self::IS_ACTIVE, $isActive);

    return self::doSelectOne($c);
  }

}
