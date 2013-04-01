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
		$days = $params->get('number_days');
		$points = $params->get('minimum_points');

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('CONCAT(u.first_name, " ", u.last_name) AS name, SUM(t.activity_points) AS total_points');
		$query->from('#__code_activity_detail AS a');
		$query->join('INNER', '#__code_activity_types AS t ON a.activity_type = t.activity_type');
		$query->join('INNER', '#__code_users AS u ON a.jc_user_id = u.jc_user_id');
		$query->where('date(a.activity_date) > Date(DATE_ADD(now(), INTERVAL -' . $days . ' DAY))');
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
}
