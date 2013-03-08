<?php
/**
 * @version		$Id: builds.php 418 2010-06-25 01:27:48Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.modellist');

/**
 * Builds Model for Joomla Code
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeModelBuilds extends JModelList
{
	/**
	 * Context string for the model type.  This is used to handle uniqueness
	 * when dealing with the getStoreId() method and caching data structures.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $context = 'com_code.builds';

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
		$query->from('#__code_builds AS a');

		// Join on the branch table.
		$query->select('b.title AS branch_title, b.path AS branch_path, b.access AS branch_access');
		$query->join('LEFT', '#__code_branches AS b on b.branch_id = a.branch_id');

		// Join on the project table.
		$query->select('p.title AS project_title, p.alias AS project_alias, p.access AS project_access');
		$query->join('LEFT', '#__code_projects AS p on p.project_id = a.project_id');

		// Join on user table for created by information.
		$query->select('u.name AS user_name, u.username AS user_login_name');
		$query->join('LEFT', '#__users AS u on u.id = a.user_id');

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {

			// Get the current user and its authorised access levels.
			$user = & JFactory::getUser();
			$groups = $user->authorisedLevels();

			// Ensure we are only getting issues where we have access.
			$query->where('b.access IN ('.implode(',', $groups).')');
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

		// Filter by a single or group of branches.
		$branchId = $this->getState('filter.branch_id');
		if (is_numeric($branchId)) {
			$op = $this->getState('filter.branch_id.include', true) ? ' = ' : ' <> ';
			$query->where('a.branch_id'.$op.(int) $branchId);
		}
		elseif (is_array($branchId)) {
			JArrayHelper::toInteger($branchId);
			$op = $this->getState('filter.branch_id.include', true) ? ' IN ' : ' NOT IN ';
			$query->where('a.branch_id'.$op.'('.implode(',', $branchId).')');
		}

		// Filter by a single or group of associated issues.
		$issueId = $this->getState('filter.issue_id');
		if (is_numeric($issueId)) {
			$op = $this->getState('filter.issue_id.include', true) ? ' = ' : ' <> ';
			$query->where('ass.issue_id'.$op.(int) $issueId);
			$query->join('LEFT', '#__code_tracker_issue_commits AS ass on ass.build_id = a.build_id');
			$query->group('a.build_id');
		}
		elseif (is_array($issueId)) {
			JArrayHelper::toInteger($issueId);
			$op = $this->getState('filter.issue_id.include', true) ? ' IN ' : ' NOT IN ';
			$query->where('ass.issue_id'.$op.'('.implode(',', $issueId).')');
			$query->join('LEFT', '#__code_tracker_issue_commits AS ass on ass.build_id = a.build_id');
			$query->group('a.build_id');
		}

		// Filter by a single or group of authors.
		$authorId = $this->getState('filter.author_id');
		if (is_numeric($authorId)) {
			$op = $this->getState('filter.author_id.include', true) ? ' = ' : ' <> ';
			$query->where('a.user_id'.$op.(int) $authorId);
		}
		elseif (is_array($authorId)) {
			JArrayHelper::toInteger($authorId);
			$op = $this->getState('filter.submitter_id.include', true) ? ' IN ' : ' NOT IN ';
			$query->where('a.user_id'.$op.'('.implode(',', $authorId).')');
		}

		// Filter by date range or relative date.
		$dateFiltering = $this->getState('filter.date_filtering', 'off');
		switch ($dateFiltering) {
			case 'range':
				$nullDate = $db->quote($db->getNullDate());
				$startDateRange = $db->quote($this->getState('filter.start_date_range', $nullDate));
				$endDateRange = $db->quote($this->getState('filter.end_date_range', $nullDate));
				$query->where('(a.commit_date >= '.$startDateRange.' AND a.commit_date <= '.$endDateRange.')');
				break;

			case 'relative':
				$nowDate = $db->quote(JFactory::getDate()->toMySQL());
				$relativeDate = (int) $this->getState('filter.relative_date', 0);
				$query->where('a.commit_date >= DATE_SUB('.$nowDate.', INTERVAL '.$relativeDate.' DAY)');
				break;

			case 'off':
			default:
				break;
		}

		/*
		 * TODO: Search Filter
		 */

		// Add the list ordering clause.
		$query->order($this->getState('list.ordering', 'a.commit_date').' '.$this->getState('list.direction', 'DESC'));

		return $query;
	}

	/**
	 * Method to get a list of builds.
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
				$item->options->set('access-view', in_array($item->branch_access, $groups) && in_array($item->project_access, $groups));
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

		// Set the tracker issue filter.
		//$this->setState('filter.issue_id', 1);
		//$this->setState('filter.issue_id.include', 1);

		// Set the branch filter.
		//$this->setState('filter.branch_id', 1);
		//$this->setState('filter.branch_id.include', 1);

		// Set the author filter.
		//$this->setState('filter.author_id', 1);
		//$this->setState('filter.author_id.include', 1);

		// Set the date filters.
		//$this->setState('filter.date_filtering', null);
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
		$id .= ':'.$this->getState('filter.issue_id');
		$id .= ':'.$this->getState('filter.issue_id.include');
		$id .= ':'.$this->getState('filter.branch_id');
		$id .= ':'.$this->getState('filter.branch_id.include');
		$id .= ':'.$this->getState('filter.author_id');
		$id .= ':'.$this->getState('filter.author_id.include');
		$id .= ':'.$this->getState('filter.date_filtering');
		$id .= ':'.$this->getState('filter.start_date_range');
		$id .= ':'.$this->getState('filter.end_date_range');
		$id .= ':'.$this->getState('filter.relative_date');
		$id .= ':'.$this->getState('filter.search');

		return parent::getStoreId($id);
	}
}
