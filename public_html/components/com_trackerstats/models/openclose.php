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
class TrackerstatsModelOpenclose extends JModelList
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
	public function getIssueCounts()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$periodList = array(1 => 7, 2 => 30, 3 => 90);
		$periodNames = array(1 => 'Weeks', 2 => 'Months', 3 => 'Quarters');
		$periodName = $periodNames[$this->state->get('list.period')];
		$periodValue = $periodList[$this->state->get('list.period')];
		// Get 12 columns
		for ($i = 4; $i > 0; $i--)
		{
			$startDay = ($i * $periodValue) - 1;
			$endDay = ($i - 1) * $periodValue;
			$query->select('SUM(CASE WHEN (DATE(i.close_date) BETWEEN ' .
					'Date(DATE_ADD(now(), INTERVAL -' . $startDay . ' DAY)) ' .
					' AND Date(DATE_ADD(now(), INTERVAL -' . $endDay . ' DAY))) AND i.status_name LIKE \'%fixed%\' THEN 1 ELSE 0 END)' .
					' AS fixed' . $i);
		}
		for ($i = 4; $i > 0; $i--)
		{
		$startDay = ($i * $periodValue) - 1;
		$endDay = ($i - 1) * $periodValue;
		$query->select('SUM(CASE WHEN (DATE(i.close_date) BETWEEN ' .
					'Date(DATE_ADD(now(), INTERVAL -' . $startDay . ' DAY)) ' .
					' AND Date(DATE_ADD(now(), INTERVAL -' . $endDay . ' DAY))) AND i.status_name NOT LIKE \'%fixed%\' THEN 1 ELSE 0 END)' .
					' AS closed' . $i);
		}
		$query->select('DATE(NOW()) AS end_date');
		$query->from($db->qn('#__code_tracker_issues') . ' AS i');
		$query->where('date(i.close_date) > Date(DATE_ADD(now(), INTERVAL -' . ($periodValue * 4) . ' DAY))');
		$query->where('i.state = 0');

		$db->setQuery($query, $this->state->get('list.start'), $this->state->get('list.limit'));
		$closedIssues = $db->loadObject();

		$query = $db->getQuery(true);
		for ($i = 4; $i > 0; $i--)
		{
		$startDay = ($i * $periodValue) - 1;
		$endDay = ($i - 1) * $periodValue;
		$query->select('SUM(CASE WHEN DATE(i.created_date) BETWEEN ' .
					'Date(DATE_ADD(now(), INTERVAL -' . $startDay . ' DAY)) ' .
					' AND Date(DATE_ADD(now(), INTERVAL -' . $endDay . ' DAY)) THEN 1 ELSE 0 END)' .
					' AS opened' . $i);
		}
		$query->select('DATE(NOW()) AS end_date');
		$query->from($db->qn('#__code_tracker_issues') . ' AS i');
		$query->where('date(i.created_date) > Date(DATE_ADD(now(), INTERVAL -' . ($periodValue * 4) . ' DAY))');

		$db->setQuery($query, $this->state->get('list.start'), $this->state->get('list.limit'));
		$openedIssues = $db->loadObject();
		return array($openedIssues, $closedIssues);

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
	}

} // end of class