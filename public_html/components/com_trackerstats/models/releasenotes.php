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
 * Gets the data for the release notes menu item.
 *
 * @package		Joomla.Site
 * @subpackage	com_trackerstats
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
		$subQuery = $db->getQuery(true);
		$includeRaw = $this->state->params->get('include_issues', null);
		$excludeRaw = $this->state->params->get('exclude_issues', null);
		$includeArray = explode(',', $includeRaw);
		$excludeArray = explode(',', $excludeRaw);
		JArrayHelper::toInteger($includeArray);
		JArrayHelper::toInteger($excludeArray);

		$subQuery->select('issue_id, tag_id, tag')
			->from('#__code_tracker_issue_tag_map')
			->where('tag_id IN (39,1,29,44,36,85,11,40,17,82,13,6,35,22,27,21,23,20,49,34,19,25,43,94,88,125,112,114)')
			->GROUP('issue_id, tag_id, tag');

		// Select required fields from the categories.
		$query->select("CASE WHEN ISNULL(m.tag) THEN 'None' ELSE m.tag END as category");
		$query->select('i.title, i.jc_issue_id, i.close_date');

		$query->from($db->qn('#__code_tracker_issues') . ' AS i');
		$query->join('LEFT', '(' . $subQuery->__toString() . ') AS m ON i.issue_id = m.issue_id');

		$query->where('((DATE(close_date) BETWEEN ' . $db->q(substr($this->state->params->get('start_date'),0,10)) . ' AND ' .
				$db->q(substr($this->state->params->get('end_date'),0,10)) . ') OR (i.jc_issue_id IN (' . implode(',', $includeArray) . ')))');
		$query->where("status_name LIKE '%Fixed in SVN%'");
		$query->where('i.jc_issue_id NOT IN (' . implode(',', $excludeArray) . ')');

		if ($this->state->get('list.filter'))
		{
			$query->where('i.title LIKE ' . $db->q('%' . $this->state->get('list.filter') . '%'));
		}
		$query->order("CASE WHEN ISNULL(m.tag) THEN 'None' ELSE m.tag END ASC");

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
		$app	= JFactory::getApplication('site');
		$jinput = $app->input;

		$params = $app->getParams();
		$menuParams = new JRegistry;

		if ($menu = $app->getMenu()->getActive()) {
			$menuParams->loadString($menu->params);
		}

		$mergedParams = clone $menuParams;
		$mergedParams->merge($params);
		$this->setState('params', $mergedParams);

		$user		= JFactory::getUser();
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		// Optional filter text
		$this->setState('list.filter', JRequest::getString('filter-search'));

		// filter.order
		$limit = $app->getUserStateFromRequest('com_trackerstats.releasenotes.limit', 'limit', $params->get('display_num', 20), 'uint');
		$this->setState('list.limit', $limit);
		$this->setState('list.start', JRequest::getUInt('limitstart', 0));
	}

} // end of class