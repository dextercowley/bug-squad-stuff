<?php
/**
 * @version		$Id: controller.php 461 2010-10-30 15:58:47Z louis $
 * @package		Joomla.Administrator
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.controller');

/**
 * Code master display controller.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_code
 * @since		1.6
 */
class CodeController extends JController
{
	/**
	 * @var		string	The default view.
	 * @since	1.6
	 */
	protected $default_view = 'about';

	/**
	 * Method to display a view.
	 *
	 * @since	1.6
	 */
	public function display()
	{
		require_once JPATH_COMPONENT.'/helpers/code.php';

		$view		= JRequest::getWord('view', 'projects');
		$layout 	= JRequest::getWord('layout', 'default');
		$id			= JRequest::getInt('id');

		// Check for edit form.
		if ($view == 'branch' && $layout == 'edit' && !$this->checkEditId('com_code.edit.branch', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_code&view=branches', false));

			return false;
		}

		parent::display();

		// Load the submenu.
		CodeHelper::addSubmenu($view);
	}
}