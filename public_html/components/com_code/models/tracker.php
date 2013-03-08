<?php
/**
 * @version		$Id: tracker.php 420 2010-06-25 01:56:28Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.modelitem');

/**
 * Tracker Model for Joomla Code
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeModelTracker extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $_context = 'com_code.tracker';

	protected $issues;
	protected $pagination;

	/**
	 * Method to get article data.
	 *
	 * @param	integer	The id of the article.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('tracker.id');

		// Initialize the memory storage array.
		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {

			try {
				// Get a database and query object.
				$db = $this->getDbo();
				$query = $db->getQuery(true);

				// Select the fields from the main table.
				$query->select($this->getState('item.select', 'a.*'));
				$query->from('#__code_trackers AS a');

				// Join on the project table.
				$query->select('p.title AS project_title, p.alias AS project_alias, p.access AS project_access');
				$query->join('LEFT', '#__code_projects AS p on p.project_id = a.project_id');

				// Get only the row by primary key.
				$query->where('a.tracker_id = ' . (int) $pk);

				// Filter by published state.
				$published = $this->getState('filter.published');
				$archived = $this->getState('filter.archived');
				if (is_numeric($published)) {
					$query->where('(a.state = ' . (int) $published . ' OR a.state =' . (int) $archived . ')');
				}

				// Get the data object from the database.
				$db->setQuery($query);
				$data = $db->loadObject();

				// Check for errors.
				if ($error = $db->getErrorMsg()) {
					throw new Exception($error);
				}

				if (empty($data)) {
					JError::raiseError(404, JText::_('COM_CODE_ERROR_TRACKER_NOT_FOUND'));
				}

				// Check for published state if filter set.
				if (((is_numeric($published)) || (is_numeric($archived))) && (($data->state != $published) && ($data->state != $archived)))
				{
					JError::raiseError(404, JText::_('COM_CODE_ERROR_TRACKER_NOT_FOUND'));
				}

				// Setup the options registry object.
				$options = new JRegistry($data->options);
				$data->options = clone $this->getState('options');
				$data->options->merge($options);

				// Setup the metadata registry object.
				$metadata = new JRegistry($data->metadata);
				$data->metadata = $metadata;

				// Compute access permissions.
				if ($access = $this->getState('filter.access')) {
					// If the access filter has been set, we already know this user can view.
					$data->options->set('access-view', true);
				}
				else {
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					$user =& JFactory::getUser();
					$groups = $user->authorisedLevels();

					if ($data->project_id == 0 || $data->project_access === null) {
						$data->options->set('access-view', in_array($data->access, $groups));
					}
					else {
						$data->options->set('access-view', in_array($data->access, $groups) && in_array($data->project_access, $groups));
					}
				}

				$this->_item[$pk] = $data;
			}
			catch (JException $e)
			{
				$this->setError($e);
				$this->_item[$pk] = false;
			}
		}

		return $this->_item[$pk];
	}


	/**
	 * Get the articles in the category
	 *
	 * @return	mixed	An array of articles or false if an error occurs.
	 */
	public function getItems($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('tracker.id');

		if ($this->issues === null && $branch = $this->getItem()) {
			$model = JModel::getInstance('Issues', 'CodeModel', array('ignore_request' => true));

			$model->setState('options', $this->getState('options'));
			$model->setState('filter.tracker_id', $pk);
			$model->setState('filter.access', $this->getState('filter.access'));
			$model->setState('list.start', $this->getState('list.start'));
			$model->setState('list.ordering', $this->getState('list.ordering'));
			$model->setState('list.direction', $this->getState('list.direction'));
			$model->setState('list.limit', $this->getState('list.limit'));
			$model->setState('list.filter', $this->getState('list.filter'));

			$this->issues = $model->getItems();

			if ($this->issues === false) {
				$this->setError($model->getError());
			}

			$this->pagination = $model->getPagination();
		}

		return $this->issues;

	}

	public function getPagination()
	{
		$this->getItems();

		return $this->pagination;
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

		// Set the tracker id from the request.
		$pk = JRequest::getInt('tracker_id');
		$this->setState('tracker.id', $pk);

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
		$this->setState('list.ordering', $app->getUserStateFromRequest('com_code.tracker.'.$listId.'.filter_order', 'filter_order', 'a.modified_date', 'string'));
		$this->setState('list.direction', $app->getUserStateFromRequest('com_code.tracker.'.$listId.'.filter_order_Dir', 'filter_order_Dir', 'DESC', 'cmd'));
		$this->setState('list.limit', $app->getUserStateFromRequest('com_code.tracker.'.$listId.'.limit', 'limit', $app->getCfg('list_limit'), 'int'));
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
		$id .= ':'.$this->getState('tracker.id');
		$id .= ':'.$this->getState('filter.access');
		$id .= ':'.$this->getState('filter.state');
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
