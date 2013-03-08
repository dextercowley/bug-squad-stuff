<?php
/**
 * @version		$Id: issues.php 423 2010-06-25 03:06:54Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.modellist');

/**
 * Tracker Model for Joomla Code
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeModelIssues extends JModelList
{
	/**
	 * Context string for the model type.  This is used to handle uniqueness
	 * when dealing with the getStoreId() method and caching data structures.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $context = 'com_code.issues';

	/**
	 * @param	boolean	True to join selected foreign information
	 *
	 * @return	string
	 * @since	1.6
	 */
	function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select($this->getState('item.select', 'a.*'));
		$query->from('#__code_tracker_issues AS a');

		// Join on the tracker table.
		$query->select('t.title AS tracker_title, t.alias AS tracker_alias, t.access AS tracker_access');
		$query->join('LEFT', '#__code_trackers AS t on t.tracker_id = a.tracker_id');

		// Join on the project table.
		$query->select('p.title AS project_title, p.alias AS project_alias, p.access AS project_access');
		$query->join('LEFT', '#__code_projects AS p on p.project_id = a.project_id');

		// Join on user table for created by information.
		$query->select('cu.name AS created_user_name, cu.username AS created_user_login_name');
		$query->join('LEFT', '#__users AS cu on cu.id = a.created_by');

		// Join on user table for modified by information.
		$query->select('mu.name AS modified_user_name, mu.username AS modified_user_login_name');
		$query->join('LEFT', '#__users AS mu on mu.id = a.modified_by');

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {

			// Get the current user and its authorised access levels.
			$user = & JFactory::getUser();
			$groups = $user->authorisedLevels();

			// Ensure we are only getting issues where we have access.
			$query->where('t.access IN ('.implode(',', $groups).')');
			$query->where('p.access IN ('.implode(',', $groups).')');
		}

		// Filter by state.
		$stateFilter = $this->getState('filter.state');
		if (is_numeric($stateFilter)) {
			$query->where('a.state = '.(int) $stateFilter);
		}
		elseif (is_array($stateFilter)) {
			JArrayHelper::toInteger($stateFilter);
			$query->where('a.state IN ('.implode(',', $stateFilter).')');
		}

		// Filter by a single or group of trackers.
		$trackerId = $this->getState('filter.tracker_id');
		if (is_numeric($trackerId)) {
			$op = $this->getState('filter.tracker_id.include', true) ? ' = ' : ' <> ';
			$query->where('a.tracker_id'.$op.(int) $trackerId);
		}
		elseif (is_array($trackerId)) {
			JArrayHelper::toInteger($trackerId);
			$op = $this->getState('filter.tracker_id.include', true) ? ' IN ' : ' NOT IN ';
			$query->where('a.tracker_id'.$op.'('.implode(',', $trackerId).')');
		}

		// Filter by a single or group of status.
		$status = $this->getState('filter.status_id');
		if (is_numeric($status)) {
			$op = $this->getState('filter.status_id.include', true) ? ' = ' : ' <> ';
			$query->where('a.status'.$op.(int) $status);
		}
		elseif (is_array($status)) {
			JArrayHelper::toInteger($status);
			$op = $this->getState('filter.status_id.include', true) ? ' IN ' : ' NOT IN ';
			$query->where('a.status'.$op.'('.implode(',', $status).')');
		}

		// Filter by a single or group of tags.
		$tagId = $this->getState('filter.tag_id');
		if (is_numeric($tagId)) {
			$op = $this->getState('filter.tag_id.include', true) ? ' = ' : ' <> ';
			$query->where('tag.tag_id'.$op.(int) $tagId);
			$query->join('LEFT', '#__code_tracker_issue_tag_map AS tags on tags.issue_id = a.issue_id');
			$query->group('a.issue_id');
		}
		elseif (is_array($tagId)) {
			JArrayHelper::toInteger($tagId);
			$op = $this->getState('filter.tag_id.include', true) ? ' IN ' : ' NOT IN ';
			$query->where('tag.tag_id'.$op.'('.implode(',', $tagId).')');
			$query->join('LEFT', '#__code_tracker_issue_tag_map AS tags on tags.issue_id = a.issue_id');
			$query->group('a.issue_id');
		}

		// Filter by a single or group of submitters.
		$submitterId = $this->getState('filter.submitter_id');
		if (is_numeric($submitterId)) {
			$op = $this->getState('filter.submitter_id.include', true) ? ' = ' : ' <> ';
			$query->where('a.created_by'.$op.(int) $submitterId);
		}
		elseif (is_array($submitterId)) {
			JArrayHelper::toInteger($submitterId);
			$op = $this->getState('filter.submitter_id.include', true) ? ' IN ' : ' NOT IN ';
			$query->where('a.created_by'.$op.'('.implode(',', $submitterId).')');
		}

		// Filter by a single or group of closers.
		$closerId = $this->getState('filter.closer_id');
		if (is_numeric($closerId)) {
			$op = $this->getState('filter.closer_id.include', true) ? ' = ' : ' <> ';
			$query->where('a.closed_by'.$op.(int) $closerId);
		}
		elseif (is_array($closerId)) {
			JArrayHelper::toInteger($closerId);
			$op = $this->getState('filter.closer_id.include', true) ? ' IN ' : ' NOT IN ';
			$query->where('a.closed_by'.$op.'('.implode(',', $closerId).')');
		}

		// Filter by a single or group of assignees.
		$assigneeId = $this->getState('filter.assignee_id');
		if (is_numeric($assigneeId)) {
			$op = $this->getState('filter.assignee_id.include', true) ? ' = ' : ' <> ';
			$query->where('ass.user_id'.$op.(int) $assigneeId);
			$query->join('LEFT', '#__code_tracker_issue_assignments AS ass on ass.issue_id = a.issue_id');
			$query->group('a.issue_id');
		}
		elseif (is_array($assigneeId)) {
			JArrayHelper::toInteger($assigneeId);
			$op = $this->getState('filter.assignee_id.include', true) ? ' IN ' : ' NOT IN ';
			$query->where('ass.user_id'.$op.'('.implode(',', $assigneeId).')');
			$query->join('LEFT', '#__code_tracker_issue_assignments AS ass on ass.issue_id = a.issue_id');
			$query->group('a.issue_id');
		}

		/*
		 * Filter by date range or relative date.
		 */

		// Get the field to filter the date based on.
		$dateField = $this->getState('filter.date_field', 'created');
		switch ($dateField) {

			case 'modified':
				$dateField = 'a.modified_date';
				break;

			case 'closed':
				$dateField = 'a.closed_date';
				break;

			default:
			case 'created':
				$dateField = 'a.created_date';
				break;
		}

		// Get the date filtering type.
		$dateFiltering = $this->getState('filter.date_filtering', 'off');
		switch ($dateFiltering) {
			case 'range':
				$nullDate = $db->quote($db->getNullDate());
				$startDateRange = $db->quote($this->getState('filter.start_date_range', $nullDate));
				$endDateRange = $db->quote($this->getState('filter.end_date_range', $nullDate));
				$query->where('('.$dateField.' >= '.$startDateRange.' AND '.$dateField.' <= '.$endDateRange.')');
				break;

			case 'relative':
				$nowDate = $db->quote(JFactory::getDate()->toMySQL());
				$relativeDate = (int) $this->getState('filter.relative_date', 0);
				$query->where($dateField.' >= DATE_SUB('.$nowDate.', INTERVAL '.$relativeDate.' DAY)');
				break;

			case 'off':
			default:
				break;
		}

		/*
		 * TODO: Search Filter
		 */

		// Add the list ordering clause.
		$query->order($this->getState('list.ordering', 'a.created_date').' '.$this->getState('list.direction', 'ASC'));

		return $query;
	}

	/**
	 * Method to get a list of articles.
	 *
	 * Overriden to inject convert the attribs field into a JParameter object.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 * @since	1.6
	 */
	public function & getItems()
	{
		// Get the list of items based on the list query.
		$items	= parent::getItems();

		// Get the current user object and authorised access levels for the user.
		$user	= JFactory::getUser();
		$groups	= $user->authorisedLevels();

		// Process each item in the list.
		foreach ($items as & $item) {

			// Create the options object for the item.
			$item->options = new JRegistry();

			// TODO: Embed the access controls in here
			$item->options->set('access-edit', false);

			// Set the option for telling the layout whether or not the item can be viewed.
			$access = $this->getState('filter.access');
			if ($access) {
				// If the access filter has been set, we already have only the articles this user can view.
				$item->options->set('access-view', true);
			} else {
				// If no access filter is set, the layout takes some responsibility for display of limited information.
				$item->options->set('access-view', in_array($item->access, $groups) && in_array($item->tracker_access, $groups) && in_array($item->project_access, $groups));
			}
		}

		return $items;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Set the project id from the request.
		$pk = JRequest::getInt('project_id');
		$this->setState('project.id', $pk);

		// Load the component/page options from the application.
		$this->setState('options', $app->getParams('com_code'));

		// Set the access filter to true by default.
		$this->setState('filter.access', 1);

		// Set the state filter.
		//$this->setState('filter.state', 1);

		// Set the optional filter search string text.
		//$this->setState('filter.search', JRequest::getString('filter-search'));

		// Set the tracker filter.
		//$this->setState('filter.tracker_id', 1);
		//$this->setState('filter.tracker_id.include', 1);

		// Set the status filter.
		//$this->setState('filter.status_id', 1);
		//$this->setState('filter.status_id.include', 1);

		// Set the tag filter.
		//$this->setState('filter.tag_id', 1);
		//$this->setState('filter.tag_id.include', 1);

		// Set the submitter filter.
		//$this->setState('filter.submitter_id', 1);
		//$this->setState('filter.submitter_id.include', 1);

		// Set the closer filter.
		//$this->setState('filter.closer_id', 1);
		//$this->setState('filter.closer_id.include', 1);

		// Set the assignee filter.
		//$this->setState('filter.assignee_id', 1);
		//$this->setState('filter.assignee_id.include', 1);

		// Set the date filters.
		//$this->setState('filter.date_filtering', null);
		//$this->setState('filter.date_field', null);
		//$this->setState('filter.start_date_range', null);
		//$this->setState('filter.end_date_range', null);
		//$this->setState('filter.relative_date', null);

		// Load the list options from the request.
		$listId = $pk.':'.JRequest::getInt('Itemid', 0);
		$this->setState('list.start', JRequest::getInt('limitstart', 0));
		$this->setState('list.ordering', $app->getUserStateFromRequest('com_code.issues.'.$listId.'.filter_order', 'filter_order', 'a.modified_date', 'string'));
		$this->setState('list.direction', $app->getUserStateFromRequest('com_code.issues.'.$listId.'.filter_order_Dir', 'filter_order_Dir', 'DESC', 'cmd'));
		$this->setState('list.limit', $app->getUserStateFromRequest('com_code.issues.'.$listId.'.limit', 'limit', $app->getCfg('list_limit'), 'int'));
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':'.$this->getState('filter.access');
		$id .= ':'.$this->getState('filter.state');
		$id .= ':'.$this->getState('filter.tracker_id');
		$id .= ':'.$this->getState('filter.tracker_id.include');
		$id .= ':'.$this->getState('filter.status_id');
		$id .= ':'.$this->getState('filter.status_id.include');
		$id .= ':'.$this->getState('filter.tag_id');
		$id .= ':'.$this->getState('filter.tag_id.include');
		$id .= ':'.$this->getState('filter.submitter_id');
		$id .= ':'.$this->getState('filter.submitter_id.include');
		$id .= ':'.$this->getState('filter.closer_id');
		$id .= ':'.$this->getState('filter.closer_id.include');
		$id .= ':'.$this->getState('filter.assignee_id');
		$id .= ':'.$this->getState('filter.assignee_id.include');
		$id .= ':'.$this->getState('filter.date_filtering');
		$id .= ':'.$this->getState('filter.date_field');
		$id .= ':'.$this->getState('filter.start_date_range');
		$id .= ':'.$this->getState('filter.end_date_range');
		$id .= ':'.$this->getState('filter.relative_date');
		$id .= ':'.$this->getState('filter.search');

		return parent::getStoreId($id);
	}
}
