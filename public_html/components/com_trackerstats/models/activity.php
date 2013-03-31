<?php
/**
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
class TrackerstatsModelActivity extends JModelList
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
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select('t.activity_group');

		$periodList = array(1 => 7, 2 => 30, 3 => 90);
		$periodNames = array(1 => 'Weeks', 2 => 'Months', 3 => 'Quarters');
		$periodName = $periodNames[$this->state->get('list.period')];
		$periodValue = $periodList[$this->state->get('list.period')];
		// Get 12 columns
		for ($i = 4; $i > 0; $i--)
		{
			$startDay = ($i * $periodValue) - 1;
			$endDay = ($i - 1) * $periodValue;
			$query->select('SUM(CASE WHEN DATE(a.activity_date) BETWEEN ' .
					'Date(DATE_ADD(now(), INTERVAL -' . $startDay . ' DAY)) ' .
					' AND Date(DATE_ADD(now(), INTERVAL -' . $endDay . ' DAY)) THEN t.activity_points ELSE 0 END)' .
					' AS p' . $i);
		}
		$query->select('DATE(NOW()) AS end_date');

		$typeList = array('All', 'Tracker', 'Test', 'Code');
		$type = $typeList[$this->state->get('list.activity_type')];

		// Select required fields from the categories.
		$query->from($db->qn('#__code_activity_detail') . ' AS a');
		$query->join('INNER', $db->qn('#__code_activity_types') . ' AS t ON a.activity_type = t.activity_type');
		$query->where('date(a.activity_date) > Date(DATE_ADD(now(), INTERVAL -' . ($periodValue * 4) . ' DAY))');
		$query->group('t.activity_group');
		if ($this->state->get('list.activity_type') > 0)
		{
			$query->where('t.activity_group = ' . $db->q($type));
		}
		$query->order('t.activity_group DESC');
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
		$this->setState('list.activity_type', $jinput->getInt('type', 0));
	}

} // end of class