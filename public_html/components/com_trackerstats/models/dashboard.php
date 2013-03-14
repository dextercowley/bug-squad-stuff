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
class TrackerstatsModelDashboard extends JModelList
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

		// Select required fields from the categories.
		$query->select('u.name');
		$query->select('SUM(t.activity_points) AS total_points');
		$query->select("SUM(CASE WHEN t.activity_group = 'Tracker' THEN t.activity_points ELSE 0 END) AS tracker_points");
		$query->select("SUM(CASE WHEN t.activity_group = 'Test' THEN t.activity_points ELSE 0 END) AS test_points");
		$query->select("SUM(CASE WHEN t.activity_group = 'Code' THEN t.activity_points ELSE 0 END) AS code_points");

		$query->from($db->qn('#__code_activity_detail') . ' AS a');
		$query->join('LEFT', $db->qn('#__users') . 'AS u ON u.id = a.user_id');
		$query->join('LEFT', $db->qn('#__code_activity_types') . ' AS t ON a.activity_type = t.activity_type');
		$query->where('DATE(a.activity_date) > DATE(DATE_ADD(NOW(), INTERVAL -1 MONTH))');
		$query->order('SUM(t.activity_points) DESC');
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
		$params	= JComponentHelper::getParams('com_trackerstats');
		$limit = 3;
		$this->setState('list.limit', $limit);
		$limitstart = 0;
		$this->setState('list.start', $limitstart);
		;
	}

} // end of class