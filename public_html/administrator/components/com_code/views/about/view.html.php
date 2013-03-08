<?php
/**
 * @version		$Id: view.html.php 398 2010-06-13 17:53:03Z louis $
 * @package		Joomla.Administrator
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.view');

/**
 * View to show the about screen.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_code
 * @since		1.6
 */
class CodeViewAbout extends JView
{
	protected $state;

	/**
	 * Display the view
	 *
	 * @since	1.6
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$canDo		= CodeHelper::getActions();

		JToolBarHelper::title(JText::_('COM_CODE_MANAGER_ABOUT'), 'code');


		if ($canDo->get('core.admin')) {
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_code');
		}

		JToolBarHelper::help('JHELP_COMPONENTS_CODE_MANAGER_ABOUT');
	}
}
