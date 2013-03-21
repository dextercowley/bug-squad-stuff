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

		// Select required fields from the categories.
		$query->select("CASE WHEN ISNULL(m.tag) THEN 'None' ELSE m.tag END as category");
		$query->select('i.title, i.jc_issue_id, i.close_date');

		$query->from($db->qn('#__code_tracker_issues') . ' AS i');
		$query->join('LEFT', $db->qn('#__code_tracker_issue_tag_map') . ' AS m ON i.issue_id = m.issue_id' .
				' AND m.tag_id IN (39,1,29,44,36,85,11,40,17,82,13,6,35,22,27,21,23,20,49,34,19,25,43,94,88)');

		$query->where('DATE(close_date) BETWEEN ' . $db->q(substr($this->state->params->get('start_date'),0,10)) . ' AND ' .
				$db->q(substr($this->state->params->get('end_date'),0,10)));
		$query->where("status_name LIKE '%Fixed in SVN%'");

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
		$orderCol = $app->getUserStateFromRequest('com_trackerstats.releasenotes.filter_order', 'filter_order', '', 'string');
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = "CASE WHEN ISNULL(m.tag) THEN 'None' ELSE m.tag END";
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->getUserStateFromRequest('com_trackerstats.releasenotes.filter_order_Dir',
				'filter_order_Dir', '', 'cmd');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);

		$this->setState('list.limit', 20);

		$this->setState('list.start', JRequest::getUInt('limitstart', 0));
	}

} // end of class