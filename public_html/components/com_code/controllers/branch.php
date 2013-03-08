<?php
/**
 * @version		$Id: branch.php 398 2010-06-13 17:53:03Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.controller');

/**
 * The Joomla Code Branch Controller
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeControllerBranch extends JController
{
	protected $_params;

	/**
	 * Constructor
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function __construct()
	{
		// Execute the parent constructor.
		parent::__construct();

		// Get the component/page parameters.
		$app = JFactory::getApplication();
		$this->_params = $app->getParams('com_code');
	}

	/**
	 * The display method should never be requested from the extended
	 * controller.  Throw an error page and exit gracefully.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function display()
	{
		JError::raiseError(404, 'Resource Not Found');
	}

	public function scan()
	{
		// Verify the request token.
		$token = JRequest::getString('token', null, 'method');
		if ($token != '1q2w3e4r') {
			JError::raiseError(403, 'Access Forbidden');
		}

		// Get the branch path from the request.
		$path = JRequest::getVar('path', null, 'method', 'path');

		// Get the branch model.
		$model = $this->getModel('Branch');

		// Attempt to scan for available builds.
		if (!($success = $model->scanBuilds($path))) {
			JError::raiseError(500, JText::sprintf('COM_CODE_SCAN_BUILDS_FAILURE', $model->getError()));
		}

		echo JText::_('COM_CODE_SCAN_BUILDS_SUCCESS');
	}

	public function fix()
	{
		// Verify the request token.
		$token = JRequest::getString('token', null, 'method');
		if ($token != '1q2w3e4r') {
			JError::raiseError(403, 'Access Forbidden');
		}

		// Get the branch path from the request.
		$path = JRequest::getVar('path', 'trunk', 'method', 'path');

		// Get the branch model.
		$model = $this->getModel('Branch');

		// Attempt to scan for available builds.
		if (!($success = $model->fix($path))) {
			JError::raiseError(500, JText::sprintf('COM_CODE_SCAN_BUILDS_FAILURE', $model->getError()));
		}

		echo JText::_('COM_CODE_SCAN_BUILDS_SUCCESS');
	}
}
