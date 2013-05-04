<?php
/**
 * @copyright	Copyright (C) 2011 Mark Dexter. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.application.categories');

/**
 * Gets the data for the total open issues by time period bar chart.
 *
 * @package		com_trackerstats
 */
class TrackerstatsModelSnapshot extends JModelList
{
	/**
	 * Category items data
	 *
	 * @var array
	 */
	protected $items = null;

	/**
	 * Method to get the data for the snapshots bar chart.
	 *
	 * @return	string	An SQL query
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		$periodList = array(1 => 1, 2 => 7, 3 => 30);
		$periodNames = array(1 => 'Days', 2 => 'Weeks', 3 => 'Months');
		$periodName = $periodNames[$this->state->get('list.period')];
		$periodValue = $periodList[$this->state->get('list.period')];

		// Get starting date from the database -- latest date available. Should usually be today.
		$today = $this->getLatestDate();

		// Calculate the prior three dates
		$priorDates = array();
		$db	= $this->getDbo();
		$priorDates[] = $db->quote($today);
		for ($i = 1; $i < 4; $i++)
		{
			$workDate = new DateTime($today);
			$workDate->sub(new DateInterval('P' . ($periodValue * $i) . 'D'));
			$priorDates[] = $db->quote($workDate->format('Y-m-d'));
		}

		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__code_tracker_snapshots')
			->where('tracker_id = 3')
			->where('snapshot_day IN (' . implode(',', $priorDates) . ')');
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

	/**
	 * Method to get the most recent date available from the snapshot table
	 *
	 */
	protected function getLatestDate()
	{
		$db	= $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('MAX(snapshot_day)')
			->from('#__code_tracker_snapshots')
			->where('tracker_id = 3');
		$db->setQuery($query);
		return $db->loadResult();
	}

} // end of class