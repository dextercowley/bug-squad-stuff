<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_articles_category
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


abstract class modTrackerstatsUsersHelper
{
	public static function getList(&$params)
	{
		$days = $params->get('number_days', 0);
		$fromDate = $params->get('fromDate');
		$toDate = $params->get('toDate');
		$points = $params->get('minimum_points');

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('CONCAT(u.first_name, " ", u.last_name) AS name, SUM(t.activity_points) AS total_points');
		$query->from('#__code_activity_detail AS a');
		$query->join('INNER', '#__code_activity_types AS t ON a.activity_type = t.activity_type');
		$query->join('INNER', '#__code_users AS u ON a.jc_user_id = u.jc_user_id');

		// Where can either be number of days or date range. Number of days takes priority if set.

		if ($days === 0)
		{
			if (self::datesValid($fromDate, $toDate))
			{
				$query->where('DATE(a.activity_date) BETWEEN DATE(' . $db->q($fromDate) . ') AND DATE(' . $db->q($toDate) . ')');
			}
			else
			{
				// If invalid dates, hard-code to 30 days
				$query->where('DATE(a.activity_date) > DATE(DATE_ADD(now(), INTERVAL -30 DAY))');
			}
		}
		else
		{
			$query->where('DATE(a.activity_date) > DATE(DATE_ADD(now(), INTERVAL -' . $days . ' DAY))');
		}

		$query->group('u.jc_user_id, CONCAT(u.first_name, " ", u.last_name)');
		$query->having('SUM(t.activity_points) > ' . (int) $points);
		$query->order('CONCAT(u.first_name, " ", u.last_name) ASC');

		$db->setQuery($query);
		$rows = (array) $db->loadObjectList();
		$nameArray = array();

		foreach ($rows as $row)
		{
			$nameArray[] = $row->name;
		}
		return implode(', ', $nameArray) . '.';
	}

	protected static function datesValid($date1, $date2)
	{
		// check that they are dates and that $date1 <= $date2
		$date1 = substr($date1, 0, 10);
		$date2 = substr($date1, 0, 10);
		if (($date1 == date('Y-m-d', strtotime($date1))) && ($date2 == date('Y-m-d', strtotime($date2)))
				&& ($date1 <= $date2))
		{
			return true;
		}
		else
		{
			return false;
		}

	}
}
