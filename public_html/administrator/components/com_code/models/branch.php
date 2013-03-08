<?php
/**
 * @version		$Id: branch.php 461 2010-10-30 15:58:47Z louis $
 * @package		Joomla.Administrator
 * @subpackage	com_code
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'code.php';

/**
 * Item Model for a Branch.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_code
 * @since		1.6
 */
class CodeModelBranch extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_CODE';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.delete', 'com_code.branch.'.(int) $record->id);
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check for existing article.
		if (!empty($record->id)) {
			return $user->authorise('core.edit.state', 'com_code.branch.'.(int) $record->id);
		}
		// New article, so check against the project.
		else if (!empty($record->catid)) {
			return $user->authorise('core.edit.state', 'com_code.project.'.(int) $record->project_id);
		}
		// Default to component settings if neither article nor category known.
		else {
			return parent::canEditState($record);
		}
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	*/
	public function getTable($type = 'Branch', $prefix = 'CodeTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_code.branch', 'branch', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		// Determine correct permissions to check.
		if ($id = (int) $this->getState('branch.id')) {
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('project_id', 'action', 'core.edit');
			// Existing record. Can only edit own articles in selected categories.
			$form->setFieldAttribute('project_id', 'action', 'core.edit.own');
		}
		else {
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('project_id', 'action', 'core.create');
		}

		// Modify the form based on Edit State access controls.
		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an article you can edit.
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_code.edit.branch.data', array());

		if (empty($data)) {
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('branch.id') == 0) {
				$app = JFactory::getApplication();
				$data->set('project_id', JRequest::getInt('project_id', $app->getUserState('com_code.branch.filter.project_id')));
			}
		}

		return $data;
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 *
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	1.6
	 */
	protected function getReorderConditions($table = null)
	{
		$condition = array();
		$condition[] = 'project_id = '.(int) $table->project_id;
		return $condition;
	}
}