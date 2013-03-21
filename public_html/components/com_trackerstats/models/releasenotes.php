<?php
/**
 * @version		$Id: category.php 287 2011-11-11 23:13:33Z dextercowley $
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.application.categories');

/**
 * Joomprosubs Component Joomprosub Model
 *
 * @package		Joomla.Site
 * @subpackage	com_joomprosubs
 */
class TrackerstatsModelReleasenotes extends JModelList
{
	/**
	 * Category items data
	 *
	 * @var array
	 */
	protected $items = null;

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());

		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$periodList = array(1 => '-7 DAY', 2 => '-30 Day', 3 => '-90 DAY', 4 => '-1 YEAR');
		$periodValue = $periodList[$this->state->get('list.period')];

		$typeList = array('All', 'Tracker', 'Test', 'Code');
		$type = $typeList[$this->state->get('list.activity_type')];

		// Select required fields from the categories.
		$query->select('u.name');
		$query->select('SUM(t.activity_points) AS total_points');
		$query->select("SUM(CASE WHEN t.activity_group = 'Tracker' THEN t.activity_points ELSE 0 END) AS tracker_points");
		$query->select("SUM(CASE WHEN t.activity_group = 'Test' THEN t.activity_points ELSE 0 END) AS test_points");
		$query->select("SUM(CASE WHEN t.activity_group = 'Code' THEN t.activity_points ELSE 0 END) AS code_points");

		$query->from($db->qn('#__code_activity_detail') . ' AS a');
		$query->join('LEFT', $db->qn('#__users') . 'AS u ON u.id = a.user_id');
		$query->join('LEFT', $db->qn('#__code_activity_types') . ' AS t ON a.activity_type = t.activity_type');
		$query->where('DATE(a.activity_date) > DATE(DATE_ADD(NOW(), INTERVAL ' . $periodValue . '))');

		if ($this->state->get('list.activity_type') > 0)
		{
			$query->where('t.activity_group = ' . $db->q($type));
			$query->order("SUM(CASE WHEN t.activity_group = " . $db->q($type) . " THEN t.activity_points ELSE 0 END) DESC, SUM(t.activity_points) DESC");
		}
		else
		{
			$query->order("SUM(t.activity_points) DESC");
		}

		$query->group('a.user_id');

		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();
		$jinput = $app->input;
		$params	= JComponentHelper::getParams('com_trackerstats');
		$this->setState('list.limit', 25);
		$this->setState('list.start', 0);
		$this->setState('list.period', $jinput->getInt('period', 1));
		$this->setState('list.activity_type', $jinput->getInt('activity_type', 0));
	}

} // end of class