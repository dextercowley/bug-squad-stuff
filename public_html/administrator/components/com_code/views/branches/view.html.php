<?php
/**
 * @version		$Id: view.html.php 461 2010-10-30 15:58:47Z louis $
 * @package		Joomla.Administrator
 * @subpackage	com_code
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of branches.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_code
 * @since		1.6
 */
class CodeViewBranches extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @return	void
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_CODE_BRANCHES_TITLE'), 'code');

		$canDo	= CodeHelper::getActions();

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('branch.edit','JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::custom('branches.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('branches.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('branches.archive','JTOOLBAR_ARCHIVE');
			JToolBarHelper::custom('branches.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'branches.delete','JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}
		else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('branches.trash','JTOOLBAR_TRASH');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_code');
			JToolBarHelper::divider();
		}

		JToolBarHelper::help('JHELP_CODE_BRANCH_MANAGER');
	}
}
