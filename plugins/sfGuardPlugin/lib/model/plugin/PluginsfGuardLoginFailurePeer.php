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
 * @version    SVN: $Id: sfGuardGroupPeer.php 3109 2006-12-23 07:52:31Z fabien $
 */
class PluginsfGuardLoginFailurePeer extends BasesfGuardLoginFailurePeer
{

  public static function doCountForUsernameInTimeRange($username, $login_failure_check_range_time)
  {
      $criteria = self::buildCriteriaForUsernameInTimeRange($username, $login_failure_check_range_time);
      return self::doCount($criteria);
  }

  public static function doCountForIpInTimeRange($ip, $login_failure_check_range_time )
  {
      $criteria = self::buildCriteriaForIpInTimeRange($ip, $login_failure_check_range_time );
      return self::doCount($criteria);
  }

  protected static function buildCriteriaForUsernameInTimeRange  ($username, $login_failure_check_range_time)
  {
      $criteria = self::buildCriteriaForRangeTime($login_failure_check_range_time);
      $criteria->add(self::USERNAME, $username);
      return $criteria;
  }

  protected static function buildCriteriaForIpInTimeRange($ip, $login_failure_check_range_time )
  {
      $criteria = self::buildCriteriaForRangeTime($login_failure_check_range_time);
      $criteria->add(self::IP_ADDRESS, $ip);
      return $criteria;
  }

  protected static function buildCriteriaForRangeTime($range)
  {
      $to = time();
      $from = strtotime("-$range seconds");    
      $criteria = new Criteria();
      $cri = $criteria->getNewCriterion(self::AT,$from,Criteria::GREATER_EQUAL);
      $cri->addAnd($criteria->getNewCriterion(self::AT,$to,Criteria::LESS_EQUAL));
      $criteria->add($cri);
      return $criteria;
  }

}
