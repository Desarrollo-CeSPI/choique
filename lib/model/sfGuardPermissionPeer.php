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
 * @version    SVN: $Id: sfGuardPermissionPeer.php 5760 2007-10-30 07:51:16Z francois $
 */
class sfGuardPermissionPeer extends PluginsfGuardPermissionPeer
{
  public static function doSelectSectionPermissions(Criteria $c=null)
  {
    if ($c== null) $c=new Criteria();
    $c->addAnd(self::SECTION, true);
    return self::doSelect($c);
  }

  public static function doCountSectionPermissions(Criteria $c=null)
  {
    if ($c== null) $c=new Criteria();
    $c->addAnd(self::SECTION, true);
    return self::doCount($c);
  }
}
